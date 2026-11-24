<?php
require __DIR__ . '/../inc/bootstrap.php';
function require_admin(): void { if (empty($_SESSION['admin'])) redirect('login.php'); }
function adm_head(string $title, string $active = ''): void {
    $f = flash();
    $nav = ['index.php'=>'داشبورد','orders.php'=>'سفارش‌ها','products.php'=>'محصولات','categories.php'=>'دسته‌بندی‌ها','password.php'=>'تغییر رمز']; ?>
<!doctype html><html lang="fa" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title) ?> | پنل مدیریت</title><link rel="stylesheet" href="../assets/style.css"></head>
<body><div class="adm"><aside><b>پنل <?= e(SITE_NAME) ?></b>
<?php foreach ($nav as $u => $l): ?><a href="<?= $u ?>" class="<?= $active === $u ? 'on' : '' ?>"><?= $l ?></a><?php endforeach; ?>
<a href="../index.php" target="_blank">مشاهده سایت</a>
<form method="post" action="logout.php"><?= csrf_field() ?><button class="btn ghost block" style="color:#cfe3df;margin-top:8px">خروج</button></form></aside>
<section><h1><?= e($title) ?></h1>
<?php if ($f): ?><div class="flash <?= e($f[1]) ?>"><?= e($f[0]) ?></div><?php endif;
}
function adm_foot(): void { echo '</section></div></body></html>'; }
