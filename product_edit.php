<?php
require __DIR__ . '/_layout.php'; require_admin(); $pdo = db();
$id = (int)($_GET['id'] ?? 0);
$p = ['name'=>'','brand'=>'','category_id'=>0,'price'=>'','old_price'=>0,'stock'=>0,'description'=>'','specs'=>'','image'=>'','featured'=>0,'active'=>1];
if ($id) { $st = $pdo->prepare('SELECT * FROM products WHERE id=?'); $st->execute([$id]); $p = $st->fetch() ?: redirect('products.php'); }
$cats = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $num = fn($k) => (int)preg_replace('/\D/', '', strtr($_POST[$k] ?? '0', ['۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9']));
    $d = [trim($_POST['name'] ?? ''), trim($_POST['brand'] ?? ''), (int)($_POST['category_id'] ?? 0), $num('price'), $num('old_price'), $num('stock'),
          trim($_POST['description'] ?? ''), trim($_POST['specs'] ?? ''), isset($_POST['featured']) ? 1 : 0, isset($_POST['active']) ? 1 : 0];
    if ($d[0] === '' || $d[3] <= 0) $err = 'نام و قیمت محصول الزامی است.';
    else {
        $image = $p['image'];
        if (!empty($_FILES['image']['name'])) {
            $new = save_image($_FILES['image']);
            if (!$new) $err = 'تصویر نامعتبر است (فقط JPG/PNG/WebP تا ۳ مگابایت).';
            else { if ($image) @unlink(ROOT . '/uploads/' . basename($image)); $image = $new; }
        }
        if (!$err) {
            if ($id) $pdo->prepare('UPDATE products SET name=?,brand=?,category_id=?,price=?,old_price=?,stock=?,description=?,specs=?,featured=?,active=?,image=? WHERE id=?')->execute([...$d, $image, $id]);
            else $pdo->prepare('INSERT INTO products (name,brand,category_id,price,old_price,stock,description,specs,featured,active,image) VALUES (?,?,?,?,?,?,?,?,?,?,?)')->execute([...$d, $image]);
            flash('محصول ذخیره شد.'); redirect('products.php');
        }
    }
    $p = array_merge($p, array_combine(['name','brand','category_id','price','old_price','stock','description','specs','featured','active'], $d));
}
adm_head($id ? 'ویرایش محصول' : 'محصول جدید', 'products.php'); ?>
<?php if ($err): ?><div class="flash err"><?= e($err) ?></div><?php endif; ?>
<form method="post" enctype="multipart/form-data" class="panel formgrid"><?= csrf_field() ?>
<div><label>نام محصول</label><input name="name" value="<?= e($p['name']) ?>" required></div>
<div><label>برند</label><input name="brand" value="<?= e($p['brand']) ?>"></div>
<div><label>دسته‌بندی</label><select name="category_id"><?php foreach ($cats as $c): ?><option value="<?= $c['id'] ?>" <?= $p['category_id'] == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></div>
<div><label>موجودی (عدد)</label><input name="stock" inputmode="numeric" value="<?= e($p['stock']) ?>"></div>
<div><label>قیمت (<?= CURRENCY ?>)</label><input name="price" inputmode="numeric" value="<?= e($p['price']) ?>" required></div>
<div><label>قیمت قبل از تخفیف (اختیاری)</label><input name="old_price" inputmode="numeric" value="<?= e($p['old_price']) ?>"></div>
<div class="full"><label>توضیحات</label><textarea name="description"><?= e($p['description']) ?></textarea></div>
<div class="full"><label>مشخصات فنی (هر خط به شکل «عنوان: مقدار»)</label><textarea name="specs"><?= e($p['specs']) ?></textarea></div>
<div class="full"><label>تصویر (JPG / PNG / WebP، حداکثر ۳MB)</label><?php if ($p['image']): ?><img class="thumb-s" style="width:90px;height:90px" src="../uploads/<?= e($p['image']) ?>" alt=""><?php endif; ?><input type="file" name="image" accept="image/jpeg,image/png,image/webp"></div>
<div class="row full" style="margin-top:12px"><label style="margin:0"><input type="checkbox" name="active" style="width:auto" <?= $p['active'] ? 'checked' : '' ?>> فعال (نمایش در سایت)</label>
<label style="margin:0"><input type="checkbox" name="featured" style="width:auto" <?= $p['featured'] ? 'checked' : '' ?>> محصول ویژه (صفحه اصلی)</label></div>
<div class="full row" style="margin-top:16px"><button class="btn">ذخیره</button><a class="btn ghost" href="products.php">انصراف</a></div>
</form>
<?php adm_foot();
