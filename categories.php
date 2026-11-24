<?php
require __DIR__ . '/_layout.php'; require_admin(); $pdo = db();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check(); $a = $_POST['action'] ?? ''; $id = (int)($_POST['id'] ?? 0); $name = trim($_POST['name'] ?? '');
    if ($a === 'add' && $name !== '') { $pdo->prepare('INSERT INTO categories (name) VALUES (?)')->execute([$name]); flash('دسته‌بندی اضافه شد.'); }
    elseif ($a === 'rename' && $name !== '') { $pdo->prepare('UPDATE categories SET name=? WHERE id=?')->execute([$name, $id]); flash('نام دسته‌بندی تغییر کرد.'); }
    elseif ($a === 'delete') {
        $c = $pdo->prepare('SELECT COUNT(*) FROM products WHERE category_id=?'); $c->execute([$id]);
        if ($c->fetchColumn() > 0) flash('ابتدا محصولات این دسته را به دسته دیگری منتقل یا حذف کنید.', 'err');
        else { $pdo->prepare('DELETE FROM categories WHERE id=?')->execute([$id]); flash('دسته‌بندی حذف شد.'); }
    }
    redirect('categories.php');
}
$rows = $pdo->query('SELECT c.*, (SELECT COUNT(*) FROM products WHERE category_id=c.id) n FROM categories c ORDER BY id')->fetchAll();
adm_head('دسته‌بندی‌ها', 'categories.php'); ?>
<form method="post" class="row panel" style="margin-bottom:16px"><?= csrf_field() ?><input type="hidden" name="action" value="add"><input name="name" placeholder="نام دسته‌بندی جدید" style="flex:1" required><button class="btn">افزودن</button></form>
<table><tr><th>نام</th><th>تعداد محصول</th><th></th></tr>
<?php foreach ($rows as $c): ?><tr><td><form method="post" class="row"><?= csrf_field() ?><input type="hidden" name="action" value="rename"><input type="hidden" name="id" value="<?= $c['id'] ?>"><input name="name" value="<?= e($c['name']) ?>" style="max-width:280px"><button class="btn sm ghost">ذخیره نام</button></form></td>
<td><?= $c['n'] ?></td><td><form method="post" onsubmit="return confirm('حذف شود؟')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $c['id'] ?>"><button class="btn sm danger">حذف</button></form></td></tr><?php endforeach; ?></table>
<?php adm_foot();
