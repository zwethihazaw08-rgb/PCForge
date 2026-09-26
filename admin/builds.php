<?php
require __DIR__ . '/../includes/admin-functions.php';
require __DIR__ . '/../includes/admin-builds.php';
$where = ''; $values = [];
if (isset($_GET['user_id'])) { $where = ' WHERE b.user_id = ?'; $values[] = admin_id($_GET['user_id']); }
$total = (int)admin_query('SELECT COUNT(*) FROM saved_builds b' . $where,$values)->fetchColumn();
[$page,$offset,$pages] = admin_pagination($total);
$builds = admin_query('SELECT b.*,u.username FROM saved_builds b JOIN users u ON u.id=b.user_id' . $where . " ORDER BY b.id DESC LIMIT 20 OFFSET $offset",$values)->fetchAll();
admin_start('PC Builds','Customer configurations, current component prices, and power estimates.');
?>
<section class="admin-panel"><div class="table-responsive"><table class="table"><thead><tr><th>Build</th><th>Owner</th><th>CPU / GPU</th><th>Current price</th><th>Estimated power</th><th>Created</th></tr></thead><tbody>
<?php foreach ($builds as $build): $summary = admin_build_summary($build['build_data']); ?><tr><td><a href="build-view.php?id=<?= (int)$build['id'] ?>"><?= e($build['build_name']) ?></a></td><td><a href="user-view.php?id=<?= (int)$build['user_id'] ?>"><?= e($build['username']) ?></a></td><td><?= e($summary['parts']['cpu']['name'] ?? 'CPU missing') ?><small class="d-block text-muted"><?= e($summary['parts']['gpu']['name'] ?? 'GPU missing') ?></small></td><td><?= e(admin_money(cents_decimal($summary['cents']))) ?><?= $summary['missing'] || $summary['missingPrice'] ? ' (partial)' : '' ?></td><td><?= $summary['watts'] ?> W</td><td><?= e($build['created_at']) ?></td></tr><?php endforeach; if (!$builds) admin_empty(6,'No saved PC builds. Customer builds will appear here when saved.'); ?>
</tbody></table></div><?php admin_pager($page,$pages,$total); ?></section><?php admin_end(); ?>
