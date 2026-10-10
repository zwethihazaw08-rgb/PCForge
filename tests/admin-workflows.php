<?php
// Local integration checks using disposable records and the real HTTP handlers.
// Never run against production. Cleanup is restricted to IDs created by this run.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/orders.php';
$pdo = db();
$tag = 'admin_check_' . bin2hex(random_bytes(6));
$userId = null; $products = []; $orderIds = []; $buildId = null;
$cookies = tempnam(sys_get_temp_dir(),'pcforge-cookie-');
$imageTemp = null; $uploadedImage = null;
$password = bin2hex(random_bytes(20));
$base = rtrim(getenv('PCFORGE_TEST_URL') ?: 'http://localhost/PCForge/', '/') . '/';

function check(bool $ok, string $message): void
{
    if (!$ok) throw new RuntimeException($message);
    echo 'PASS ' . $message . PHP_EOL;
}
function request_page(string $path, ?array $post = null, bool $multipart = false): array
{
    $curl = curl_init($GLOBALS['base'] . $path);
    curl_setopt_array($curl,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>20,CURLOPT_COOKIEJAR=>$GLOBALS['cookies'],CURLOPT_COOKIEFILE=>$GLOBALS['cookies'],CURLOPT_FOLLOWLOCATION=>false]);
    if ($post !== null) curl_setopt_array($curl,[CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$multipart ? $post : http_build_query($post)]);
    $body = curl_exec($curl); $status = (int)curl_getinfo($curl,CURLINFO_HTTP_CODE); $location = curl_getinfo($curl,CURLINFO_REDIRECT_URL);
    if ($body === false) throw new RuntimeException(curl_error($curl));
    curl_close($curl);
    return compact('body','status','location');
}
function token(array $response): string
{
    if (!preg_match('/name="csrf_token" value="([a-f0-9]+)"/',$response['body'],$match)) throw new RuntimeException('Missing CSRF token.');
    return $match[1];
}
try {
    $query=$pdo->prepare("INSERT INTO users (username,email,password,role) VALUES (?,?,?,'admin')");
    $query->execute([$tag,$tag.'@example.invalid',password_hash($password,PASSWORD_DEFAULT)]); $userId=(int)$pdo->lastInsertId();
    $guest=request_page('admin/dashboard.php'); check($guest['status']===303,'guest redirected to login');
    $login=request_page('login.php');
    $login=request_page('login.php',['csrf_token'=>token($login),'login'=>$tag,'password'=>$password,'redirect'=>'index.php']);
    check($login['status']===303 && str_ends_with($login['location'],'admin/dashboard.php'),'admin login lands on dashboard');
    $dashboard=request_page('admin/dashboard.php'); $csrf=token($dashboard);
    check(str_contains($dashboard['body'],'kpi-grid') && str_contains($dashboard['body'],'dashboard-data'),'dashboard contains live statistics and charts');
    foreach (['dashboard.php','products.php','categories.php','inventory.php','users.php','builds.php','orders.php','reports.php','settings.php','product-add.php'] as $page) {
        $response=request_page('admin/'.$page);
        check($response['status']===200 && !preg_match('/Warning:|Fatal error:|could not be loaded/',$response['body']),$page.' renders');
    }
    $response=request_page('admin/product-add.php?type=users'); check($response['status']===400,'arbitrary product tables rejected');
    $response=request_page('admin/product-add.php?type=cpu',['name'=>'no-token']); check($response['status']===403,'missing CSRF rejected');
    foreach (array_keys(component_categories()) as $type) {
        $post=['csrf_token'=>$csrf,'name'=>$tag.' '.$type,'price'=>'10.25','stock'=>'5','status'=>'active'];
        $response=request_page('admin/product-add.php?type='.$type,$post);
        if (preg_match('/[?&]id=(\d+)/',$response['location'],$match)) $products[$type]=(int)$match[1];
        check($response['status']===303 && isset($products[$type]),'create '.$type.' product');
        $id=$products[$type];
        $response=request_page("admin/product-edit.php?type=$type&id=$id"); check($response['status']===200,'edit form '.$type);
        $response=request_page("admin/product-view.php?type=$type&id=$id"); check($response['status']===200,'view '.$type);
    }
    $id=$products['cpu'];
    $response=request_page("admin/product-edit.php?type=cpu&id=$id",['csrf_token'=>$csrf,'name'=>'<script>alert(1)</script>','price'=>'10.25','stock'=>'5','previous_stock'=>'5','status'=>'active']);
    check($response['status']===303,'edit product');
    $imageTemp=tempnam(sys_get_temp_dir(),'pcforge-image-');
    file_put_contents($imageTemp,'<?php echo "not an image"; ?>');
    $uploadPost=['csrf_token'=>$csrf,'name'=>$tag.' cpu','price'=>'10.25','stock'=>'5','previous_stock'=>'5','status'=>'active','image'=>new CURLFile($imageTemp,'image/jpeg','fake.jpg')];
    $response=request_page("admin/product-edit.php?type=cpu&id=$id",$uploadPost,true);
    check(str_contains($response['body'],'Invalid image type.'),'disguised PHP upload rejected');
    file_put_contents($imageTemp,base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+j8ioAAAAASUVORK5CYII='));
    $uploadPost['image']=new CURLFile($imageTemp,'image/png','test.png');
    $uploadPost['name']='<script>alert(1)</script>';
    $response=request_page("admin/product-edit.php?type=cpu&id=$id",$uploadPost,true);
    $uploadedImage=$pdo->query("SELECT image_url FROM cpu WHERE id=$id")->fetchColumn();
    check($response['status']===303 && preg_match('/^product_[a-f0-9]{32}\.png$/D',(string)$uploadedImage)===1,'valid image saved with a generated filename');
    $response=request_page("admin/product-view.php?type=cpu&id=$id"); check(str_contains($response['body'],'&lt;script&gt;alert(1)&lt;/script&gt;'),'product output escaped');
    $response=request_page('admin/inventory.php',['csrf_token'=>$csrf,'type'=>'cpu','id'=>$id,'previous'=>'5','stock'=>'-1']);
    check((int)$pdo->query("SELECT stock FROM cpu WHERE id=$id")->fetchColumn()===5,'negative stock rejected');
    request_page('admin/inventory.php',['csrf_token'=>$csrf,'type'=>'cpu','id'=>$id,'previous'=>'5','stock'=>'6']);
    check((int)$pdo->query("SELECT stock FROM cpu WHERE id=$id")->fetchColumn()===6,'stock updated');
    request_page('admin/inventory.php',['csrf_token'=>$csrf,'type'=>'cpu','id'=>$id,'previous'=>'5','stock'=>'9']);
    check((int)$pdo->query("SELECT stock FROM cpu WHERE id=$id")->fetchColumn()===6,'stale inventory form rejected');
    request_page("admin/product-edit.php?type=cpu&id=$id",['csrf_token'=>$csrf,'name'=>'Stale product','price'=>'10.25','stock'=>'10','previous_stock'=>'5','status'=>'active']);
    check((int)$pdo->query("SELECT stock FROM cpu WHERE id=$id")->fetchColumn()===6,'stale product edit cannot overwrite inventory');
    request_page('admin/users.php',['csrf_token'=>$csrf,'id'=>$userId,'status'=>'disabled']);
    check($pdo->query("SELECT status FROM users WHERE id=$userId")->fetchColumn()==='active','admin cannot disable self');
    $query=$pdo->prepare('INSERT INTO saved_builds (user_id,build_name,build_data,share_token) VALUES (?,?,?,?)');
    $buildParts=$products; unset($buildParts['fans']);
    $query->execute([$userId,$tag,json_encode($buildParts),bin2hex(random_bytes(32))]); $buildId=(int)$pdo->lastInsertId();
    foreach (["user-view.php?id=$userId","build-view.php?id=$buildId",'builds.php','products.php?q=admin_check&filter=in&min=1&max=100','reports.php?range=custom&start=2026-01-01&end=2026-12-31'] as $page) {
        $response=request_page('admin/'.$page); check($response['status']===200 && !preg_match('/Warning:|Fatal error:/',$response['body']),$page.' renders');
    }
    // HTTP cart -> checkout -> order management, with immutable purchased prices.
    $response=request_page('cart.php');
    request_page('cart.php',['csrf_token'=>token($response),'action'=>'add_product','category'=>'cpu','id'=>$id]);
    $query=$pdo->prepare('INSERT INTO user_shipping_details (user_id,name,phone,address,city,postal_code,country,region) VALUES (?,?,?,?,?,?,?,?)');
    $query->execute([$userId,'Shipping fixture','123456','Fixture street','Yangon','11181','Myanmar','Yangon Region']);
    $response=request_page('checkout.php');
    check(str_contains($response['body'],'Fixture street') && str_contains($response['body'],'Myanmar'),'checkout prefills saved shipping details');
    $response=request_page('checkout.php',['csrf_token'=>token($response),'name'=>'Integration customer','email'=>$tag.'@example.invalid','address'=>'Test address','city'=>'Yangon','postal_code'=>'11181','phone'=>'123456','country'=>'Myanmar','region'=>'Yangon Region','payment_method'=>'demo']);
    $query=$pdo->prepare('SELECT id FROM orders WHERE user_id=? ORDER BY id'); $query->execute([$userId]); $orderIds=array_map('intval',$query->fetchAll(PDO::FETCH_COLUMN));
    check($response['status']===303 && count($orderIds)===1,'checkout persists one demo order');
    $orderId=$orderIds[0];
    check($pdo->query("SELECT shipping_country FROM orders WHERE id=$orderId")->fetchColumn()==='Myanmar','order stores delivery country snapshot');
    $pdo->exec("UPDATE cpu SET price=99.00 WHERE id=$id");
    check($pdo->query("SELECT unit_price FROM order_items WHERE order_id=$orderId")->fetchColumn()==='10.25','order price preserved after catalog price changes');
    $response=request_page('admin/order-view.php?id='.$orderId); check($response['status']===200,'order details render');
    request_page('admin/order-view.php?id='.$orderId,['csrf_token'=>$csrf,'status'=>'processing']);
    request_page('admin/order-view.php?id='.$orderId,['csrf_token'=>$csrf,'status'=>'completed']);
    check((int)$pdo->query("SELECT stock FROM cpu WHERE id=$id")->fetchColumn()===5,'completion deducts stock');
    request_page('admin/order-view.php?id='.$orderId,['csrf_token'=>$csrf,'status'=>'completed']);
    check((int)$pdo->query("SELECT stock FROM cpu WHERE id=$id")->fetchColumn()===5,'repeat completion does not deduct again');
    request_page('admin/order-view.php?id='.$orderId,['csrf_token'=>$csrf,'status'=>'pending']);
    check($pdo->query("SELECT status FROM orders WHERE id=$orderId")->fetchColumn()==='completed','completed order cannot be reopened');
    request_page("admin/product-delete.php?type=cpu&id=$id",['csrf_token'=>$csrf]);
    check($pdo->query("SELECT status FROM cpu WHERE id=$id")->fetchColumn()==='inactive','product soft deletion');
    check((int)$pdo->query("SELECT COUNT(*) FROM order_items WHERE order_id=$orderId")->fetchColumn()===1,'soft deletion preserves order items');
    $response=request_page('admin/dashboard.php');
    if (in_array('--preview',$argv,true)) {
        // Read-only visual snapshot; token belongs to a disposable account removed below.
        file_put_contents(__DIR__.'/admin-preview.html',$response['body']);
        file_put_contents(__DIR__.'/products-preview.html',request_page('admin/products.php')['body']);
    }
} finally {
    // Test fixtures only: no existing products, users, orders or settings are touched.
    if ($userId) {
        $query=$pdo->prepare('DELETE i FROM order_items i JOIN orders o ON o.id=i.order_id WHERE o.user_id=?'); $query->execute([$userId]);
        $query=$pdo->prepare('DELETE FROM orders WHERE user_id=?'); $query->execute([$userId]);
        $query=$pdo->prepare('DELETE FROM users WHERE id=?'); $query->execute([$userId]);
    }
    foreach ($products as $type=>$id) { $query=$pdo->prepare("DELETE FROM `$type` WHERE id=?"); $query->execute([$id]); }
    if (is_file($cookies)) unlink($cookies);
    if ($imageTemp && is_file($imageTemp)) unlink($imageTemp);
    if (is_string($uploadedImage) && preg_match('/^product_[a-f0-9]{32}\.png$/D',$uploadedImage)) unlink(__DIR__.'/../assets/images/'.$uploadedImage);
}
