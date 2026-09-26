<?php
// Isolated service tests: no live accounts, orders or inventory are changed.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
class OrderTestPDO extends PDO {
    public function prepare(string $query, array $options = []): PDOStatement|false {
        // SQLite has a single writer; MySQL row locks are exercised by HTTP tests.
        return parent::prepare(str_replace(' FOR UPDATE','',$query),$options);
    }
}
$connection = new OrderTestPDO('sqlite::memory:',null,null,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
function db(): PDO { return $GLOBALS['connection']; }
require __DIR__ . '/../includes/orders.php';
foreach (array_keys(component_categories()) as $type) $connection->exec("CREATE TABLE `$type` (id INTEGER PRIMARY KEY,name TEXT,price TEXT,stock INTEGER,status TEXT)");
$connection->exec("CREATE TABLE store_settings (id INTEGER PRIMARY KEY,store_name TEXT,store_email TEXT,currency TEXT,low_stock_threshold INTEGER,maintenance_mode INTEGER)");
$connection->exec("INSERT INTO store_settings VALUES (1,'PCForge','','USD',5,0)");
$connection->exec("CREATE TABLE orders (id INTEGER PRIMARY KEY AUTOINCREMENT,order_number TEXT,user_id INTEGER,customer_name TEXT,customer_email TEXT,shipping_address TEXT,shipping_city TEXT,shipping_postal_code TEXT,shipping_phone TEXT,shipping_region TEXT,shipping_country TEXT,subtotal TEXT,total TEXT,currency TEXT,status TEXT DEFAULT 'pending',stock_deducted_at TEXT)");
$connection->exec('CREATE TABLE order_items (id INTEGER PRIMARY KEY AUTOINCREMENT,order_id INTEGER,category TEXT,product_id INTEGER,product_name TEXT,quantity INTEGER,unit_price TEXT,line_total TEXT)');
$connection->exec("INSERT INTO cpu VALUES (1,'Test CPU','10.25',5,'active')");
$connection->exec("INSERT INTO gpu VALUES (1,'Test GPU','20.50',5,'active')");
function verify(bool $condition, string $description): void {
    if (!$condition) throw new RuntimeException($description);
    echo 'PASS ' . $description . PHP_EOL;
}
$cart=[['quantity'=>2,'parts'=>['cpu'=>1,'gpu'=>1]],['quantity'=>1,'parts'=>['cpu'=>1]]];
$customer=['name'=>'Test','email'=>'test@example.invalid','address'=>'Test','city'=>'Test','postal_code'=>'1'];
create_demo_order($cart,$customer,1);
$id=(int)$connection->lastInsertId();
$id=(int)$connection->query('SELECT MAX(id) FROM orders')->fetchColumn();
verify($connection->query("SELECT total FROM orders WHERE id=$id")->fetchColumn()==='71.75','integer-cent checkout total across multiple cart lines');
verify((int)$connection->query("SELECT quantity FROM order_items WHERE order_id=$id AND category='cpu'")->fetchColumn()===3,'duplicate component quantities aggregated');
$connection->exec("UPDATE cpu SET price='99.99' WHERE id=1");
verify($connection->query("SELECT unit_price FROM order_items WHERE order_id=$id AND category='cpu'")->fetchColumn()==='10.25','historical price survives catalog changes');
order_status_update($id,'processing');
$connection->exec('UPDATE gpu SET stock=0 WHERE id=1');
$rejected=false;
try { order_status_update($id,'completed'); } catch (InvalidArgumentException $error) { $rejected=true; }
verify($rejected,'insufficient stock blocks completion');
verify((int)$connection->query('SELECT stock FROM cpu WHERE id=1')->fetchColumn()===5,'earlier component deduction rolled back');
verify($connection->query("SELECT status FROM orders WHERE id=$id")->fetchColumn()==='processing','failed completion retains processing status');
$connection->exec('UPDATE gpu SET stock=5 WHERE id=1');
order_status_update($id,'completed');
verify((int)$connection->query('SELECT stock FROM cpu WHERE id=1')->fetchColumn()===2 && (int)$connection->query('SELECT stock FROM gpu WHERE id=1')->fetchColumn()===3,'completion deducts all aggregated quantities');
order_status_update($id,'completed');
verify((int)$connection->query('SELECT stock FROM cpu WHERE id=1')->fetchColumn()===2,'repeat completion is idempotent');
$rejected=false;
try { order_status_update($id,'cancelled'); } catch (InvalidArgumentException $error) { $rejected=true; }
verify($rejected,'completed order is final');
$count=(int)$connection->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$rejected=false;
try { create_demo_order($cart,$customer,1); } catch (InvalidArgumentException $error) { $rejected=true; }
verify($rejected && (int)$connection->query('SELECT COUNT(*) FROM orders')->fetchColumn()===$count,'checkout rejects insufficient stock without leaving orders');
$connection->exec("INSERT INTO monitor VALUES (1,'Imported monitor','1299.99',NULL,'active')");
$monitorCart=[['quantity'=>1,'parts'=>['monitor'=>1]]];
$rejected=false;
try { create_demo_order($monitorCart,$customer,1); } catch (InvalidArgumentException $error) { $rejected=true; }
verify($rejected && (int)$connection->query('SELECT COUNT(*) FROM orders')->fetchColumn()===$count,'imported monitor with unknown stock cannot be purchased');
$connection->exec('UPDATE monitor SET stock=2 WHERE id=1');
create_demo_order($monitorCart,$customer,1);
$monitorOrder=(int)$connection->query('SELECT MAX(id) FROM orders')->fetchColumn();
verify($connection->query("SELECT category FROM order_items WHERE order_id=$monitorOrder")->fetchColumn()==='monitor','monitor checkout keeps the monitor category');
verify($connection->query("SELECT total FROM orders WHERE id=$monitorOrder")->fetchColumn()==='1299.99','monitor checkout uses the catalog price');
order_status_update($monitorOrder,'processing');
order_status_update($monitorOrder,'completed');
verify((int)$connection->query('SELECT stock FROM monitor WHERE id=1')->fetchColumn()===1,'monitor completion deducts monitor inventory');
echo "All isolated order tests passed.\n";
