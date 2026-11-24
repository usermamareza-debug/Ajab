<?php
require __DIR__ . '/_layout.php'; require_admin(); $pdo = db();
$id = (int)($_GET['id'] ?? 0);
$st = $pdo->prepare('SELECT * FROM orders WHERE id=?'); $st->execute([$id]); $o = $st->fetch() ?: redirect('orders.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check(); $new = $_POST['status'] ?? '';
    if (isset(STATUSES[$new]) && $new !== $o['status']) {
        // لغو سفارش: موجودی به انبار برمی‌گردد
        if ($new === 'cancelled' && $o['status'] !== 'cancelled') {
            $pdo->prepare('UPDATE products SET stock=stock+(SELECT qty FROM order_items WHERE order_id=? AND product_id=products.id) WHERE id IN (SELECT product_id FROM order_items WHERE order_id=?)')->execute([$id, $id]);
        } elseif ($o['status'] === 'cancelled' && $new !== 'cancelled') {
            $pdo->prepare('UPDATE products SET stock=MAX(0,stock-(SELECT qty FROM order_items WHERE order_id=? AND product_id=products.id)) WHERE id IN (SELECT product_id FROM order_items WHERE order_id=?)')->execute([$id, $id]);
        }
        $pdo->prepare('UPDATE orders SET status=? WHERE id=?')->execute([$new, $id]); flash('وضعیت سفارش به‌روز شد.');
    }
    redirect('order.php?id=' . $id);
}
$items = $pdo->prepare('SELECT * FROM order_items WHERE order_id=?'); $items->execute([$id]); $items = $items->fetchAll();
adm_head('سفارش شماره ' . $id, 'orders.php'); ?>
<div class="two"><div>
<table><tr><th>کالا</th><th>قیمت</th><th>تعداد</th><th>جمع</th></tr>
<?php foreach ($items as $i): ?><tr><td><?= e($i['name']) ?></td><td><?= number_format($i['price']) ?></td><td><?= $i['qty'] ?></td><td><?= number_format($i['price'] * $i['qty']) ?></td></tr><?php endforeach; ?></table>
<div class="panel sum" style="margin-top:12px"><div><span>جمع کالاها</span><span><?= money($o['subtotal']) ?></span></div><div><span>ارسال</span><span><?= money($o['shipping']) ?></span></div><div class="t"><span>جمع کل</span><span><?= money($o['total']) ?></span></div></div></div>
<div class="panel"><p><b><?= e($o['name']) ?></b><br><?= e($o['phone']) ?><br><?= e($o['city']) ?> — <?= nl2br(e($o['address'])) ?></p>
<?php if ($o['note']): ?><p class="muted">توضیحات: <?= e($o['note']) ?></p><?php endif; ?>
<p class="muted">ثبت: <?= jdate($o['created_at']) ?></p>
<form method="post"><?= csrf_field() ?><label>وضعیت سفارش</label><select name="status"><?php foreach (STATUSES as $k => $l): ?><option value="<?= $k ?>" <?= $o['status'] === $k ? 'selected' : '' ?>><?= $l ?></option><?php endforeach; ?></select>
<p><button class="btn">به‌روزرسانی وضعیت</button> <a class="btn ghost" href="orders.php">بازگشت</a></p></form></div></div>
<?php adm_foot();
