<?php
require __DIR__ . '/_layout.php'; require_admin(); $pdo = db();
$f = $_GET['status'] ?? '';
$st = $pdo->prepare('SELECT * FROM orders ' . ($f ? 'WHERE status=? ' : '') . 'ORDER BY id DESC LIMIT 200');
$st->execute($f ? [$f] : []); $rows = $st->fetchAll();
adm_head('سفارش‌ها', 'orders.php'); ?>
<div class="row" style="margin-bottom:14px"><a class="btn sm <?= $f ? 'ghost' : '' ?>" href="orders.php">همه</a>
<?php foreach (STATUSES as $k => $l): ?><a class="btn sm <?= $f === $k ? '' : 'ghost' ?>" href="?status=<?= $k ?>"><?= $l ?></a><?php endforeach; ?></div>
<div class="scroll"><table><tr><th>#</th><th>مشتری</th><th>موبایل</th><th>شهر</th><th>مبلغ</th><th>تاریخ</th><th>وضعیت</th><th></th></tr>
<?php foreach ($rows as $o): ?><tr><td><?= $o['id'] ?></td><td><?= e($o['name']) ?></td><td><?= e($o['phone']) ?></td><td><?= e($o['city']) ?></td><td><?= number_format($o['total']) ?></td><td><?= jdate($o['created_at']) ?></td>
<td><span class="status st-<?= e($o['status']) ?>"><?= e(STATUSES[$o['status']] ?? '') ?></span></td><td><a class="btn sm ghost" href="order.php?id=<?= $o['id'] ?>">جزئیات</a></td></tr>
<?php endforeach; if (!$rows): ?><tr><td colspan="8" class="muted">سفارشی وجود ندارد.</td></tr><?php endif; ?></table></div>
<?php adm_foot();
