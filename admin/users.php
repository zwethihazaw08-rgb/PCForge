<?php
require __DIR__ . '/../includes/admin-functions.php';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    csrf_verify();
    try {
        $id = admin_id($_POST['id'] ?? null); $status = admin_text($_POST,'status');
        if (!in_array($status,['active','disabled'],true)) throw new InvalidArgumentException('Invalid account status.');
        if ($id === (int)auth_user()['id'] && $status === 'disabled') throw new InvalidArgumentException('You cannot disable your own account.');
        admin_query('UPDATE users SET status = ? WHERE id = ?',[$status,$id]);
        flash_set('Account status updated.');
    } catch (Throwable $exception) { flash_set(admin_error($exception),'danger'); }
    redirect('admin/users.php');
}
$q = admin_text($_GET,'q');
$where = $q !== '' ? ' WHERE username LIKE ? OR email LIKE ?' : '';
$values = $q !== '' ? ["%$q%","%$q%"] : [];
$total = (int)admin_query('SELECT COUNT(*) FROM users' . $where,$values)->fetchColumn();
[$page,$offset,$pages] = admin_pagination($total);
$users = admin_query('SELECT id,username,email,role,status,created_at FROM users' . $where . " ORDER BY id DESC LIMIT 20 OFFSET $offset",$values)->fetchAll();
admin_start('Customers','Registered customers and administrators.');
?>
<section class="admin-panel"><form class="admin-filters" method="get"><div><label for="q">Name or email</label><input class="form-control" name="q" id="q" value="<?= e($q) ?>"></div><button class="btn btn-dark">Search</button></form><div class="table-responsive"><table class="table align-middle"><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Registered</th><th>Actions</th></tr></thead><tbody>
<?php foreach ($users as $user): ?><tr><td><?= (int)$user['id'] ?></td><td><a href="user-view.php?id=<?= (int)$user['id'] ?>"><?= e($user['username']) ?></a></td><td><?= e($user['email']) ?></td><td><?= e($user['role']) ?></td><td><?= admin_badge($user['status']) ?></td><td><?= e($user['created_at']) ?></td><td><?php if ((int)$user['id'] !== (int)auth_user()['id']): ?><form method="post" <?= $user['status'] === 'active' ? 'data-confirm="Disable this account? The user will lose access until reactivated."' : '' ?>><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$user['id'] ?>"><input type="hidden" name="status" value="<?= $user['status'] === 'active' ? 'disabled' : 'active' ?>"><button class="btn btn-outline-secondary btn-sm"><?= $user['status'] === 'active' ? 'Disable' : 'Activate' ?></button></form><?php else: ?><span class="text-muted">Your account</span><?php endif; ?></td></tr><?php endforeach; if (!$users) admin_empty(7,'No matching accounts.'); ?>
</tbody></table></div><?php admin_pager($page,$pages,$total); ?></section><?php admin_end(); ?>
