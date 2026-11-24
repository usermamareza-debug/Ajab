<?php
require __DIR__ . '/_layout.php'; require_admin(); $pdo = db();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check(); $id = (int)($_POST['id'] ?? 0);
    if (($_POST['action'] ?? '') === 'delete') {
        $img = $pdo->prepare('SELECT image FROM products WHERE id=?'); $img->execute([$id]); $img = $img->fetchColumn();
        $used = $pdo->prepare('SELECT COUNT(*) FROM order_items WHERE product_id=?'); $used->execute([$id]);
        if ($used->fetchColumn() > 0) { $pdo->prepare('UPDATE products SET active=0 WHERE id=?')->execute([$id]); flash('این کالا در سفارش‌ها استفاده شده؛ به‌جای حذف، غیرفعال شد.'); }
        else { $pdo->prepare('DELETE FROM products WHERE id=?')->execute([$id]); if ($img) @unlink(ROOT . '/uploads/' . basename($img)); flash('محصول حذف شد.'); }
    }
    redirect('products.php');
}
$q = trim($_GET['q'] ?? '');
$st = $pdo->prepare('SELECT p.*, c.name cat FROM products p LEFT JOIN categories c ON c.id=p.category_id WHERE p.name LIKE ? OR p.brand LIKE ? ORDER BY p.id DESC');
$st->execute(["%$q%", "%$q%"]); $rows = $st->fetchAll();
adm_head('محصولات', 'products.php'); ?>
<div class="row" style="margin-bottom:14px"><a class="btn" href="product_edit.php">+ محصول جدید</a>
<form class="row" style="margin-right:auto"><input name="q" value="<?= e($q) ?>" placeholder="جستجو…" style="width:200px"><button class="btn ghost">جستجو</button></form></div>
<div class="scroll"><table><tr><th></th><th>نام</th><th>دسته</th><th>قیمت</th><th>موجودی</th><th>وضعیت</th><th></th></tr>
<?php foreach ($rows as $p): ?><tr>
<td><div class="thumb-s"><?= $p['image'] ? '<img class="thumb-s" src="../uploads/' . e($p['image']) . '" alt="">' : '' ?></div></td>
<td><?= e($p['name']) ?><br><small class="muted"><?= e($p['brand']) ?></small></td><td><?= e($p['cat']) ?></td><td><?= number_format($p['price']) ?></td><td><?= $p['stock'] ?></td>
<td><?= $p['active'] ? 'فعال' : '<span class="muted">غیرفعال</span>' ?><?= $p['featured'] ? ' · ویژه' : '' ?></td>
<td class="row"><a class="btn sm ghost" href="product_edit.php?id=<?= $p['id'] ?>">ویرایش</a>
<form method="post" onsubmit="return confirm('این محصول حذف شود؟')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $p['id'] ?>"><button class="btn sm danger">حذف</button></form></td></tr>
<?php endforeach; if (!$rows): ?><tr><td colspan="7" class="muted">محصولی پیدا نشد.</td></tr><?php endif; ?></table></div>
<?php adm_foot();
