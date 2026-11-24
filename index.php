<?php
require __DIR__ . '/_layout.php'; require_admin(); $pdo = db();
$n = fn($sql) => (int)$pdo->query($sql)->fetchColumn();
$stats = [
  'سفارش‌های جدید' => $n("SELECT COUNT(*) FROM orders WHERE status='pending'"),
  'کل سفارش‌ها' => $n('SELECT COUNT(*) FROM orders'),
  'فروش (غیر لغوشده)' => money($n("SELECT COALESCE(SUM(total),0) FROM orders WHERE status<>'cancelled'")),
  'تعداد محصولات' => $n('SELECT COUNT(*) FROM products'),
];
$latest = $pdo->query('SELECT * FROM orders ORDER BY id DESC LIMIT 8')->fetchAll();
$low = $pdo->query('SELECT id,name,stock FROM products WHERE stock<=3 AND active=1 ORDER BY stock LIMIT 8')->fetchAll();
adm_head('داشبورد', 'index.php'); ?>
<div class="stats"><?php foreach ($stats as $l => $v): ?><div class="stat"><span class="muted"><?= $l ?></span><b><?= is_int($v) ? number_format($v) : $v ?></b></div><?php endforeach; ?></div>
<div class="two"><div><h2 style="margin-top:0">آخرین سفارش‌ها</h2><div class="scroll"><table><tr><th>#</th><th>مشتری</th><th>مبلغ</th><th>وضعیت</th></tr>
<?php foreach ($latest as $o): ?><tr><td><a href="order.php?id=<?= $o['id'] ?>"><u><?= $o['id'] ?></u></a></td><td><?= e($o['name']) ?></td><td><?= number_format($o['total']) ?></td><td><span class="status st-<?= e($o['status']) ?>"><?= e(STATUSES[$o['status']] ?? '') ?></span></td></tr><?php endforeach; ?>
<?php if (!$latest): ?><tr><td colspan="4" class="muted">هنوز سفارشی ثبت نشده.</td></tr><?php endif; ?></table></div></div>
<div><h2 style="margin-top:0">موجودی کم</h2><table><?php foreach ($low as $p): ?><tr><td><a href="product_edit.php?id=<?= $p['id'] ?>"><?= e($p['name']) ?></a></td><td><?= $p['stock'] ?> عدد</td></tr><?php endforeach; ?>
<?php if (!$low): ?><tr><td class="muted">همه محصولات موجودی کافی دارند.</td></tr><?php endif; ?></table></div></div>
<?php adm_foot();
