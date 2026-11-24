<?php
require __DIR__ . '/_layout.php'; require_admin(); $pdo = db();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $st = $pdo->prepare('SELECT * FROM admins WHERE id=?'); $st->execute([$_SESSION['admin']]); $a = $st->fetch();
    $new = $_POST['new'] ?? '';
    if (!password_verify($_POST['old'] ?? '', $a['password'])) flash('رمز فعلی درست نیست.', 'err');
    elseif (mb_strlen($new) < 8) flash('رمز جدید حداقل ۸ کاراکتر باشد.', 'err');
    elseif ($new !== ($_POST['new2'] ?? '')) flash('تکرار رمز جدید یکسان نیست.', 'err');
    else {
        $u = trim($_POST['username'] ?? '') ?: $a['username'];
        $pdo->prepare('UPDATE admins SET username=?, password=? WHERE id=?')->execute([$u, password_hash($new, PASSWORD_DEFAULT), $a['id']]);
        flash('رمز عبور تغییر کرد.');
    }
    redirect('password.php');
}
adm_head('تغییر نام کاربری و رمز', 'password.php'); ?>
<form method="post" class="panel" style="max-width:420px"><?= csrf_field() ?>
<label>نام کاربری (خالی = بدون تغییر)</label><input name="username" autocomplete="username">
<label>رمز فعلی</label><input type="password" name="old" autocomplete="current-password" required>
<label>رمز جدید (حداقل ۸ کاراکتر)</label><input type="password" name="new" autocomplete="new-password" required>
<label>تکرار رمز جدید</label><input type="password" name="new2" autocomplete="new-password" required>
<p><button class="btn">ذخیره</button></p></form>
<?php adm_foot();
