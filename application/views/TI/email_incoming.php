<?php
/**
 * Email notifikasi kiriman aset masuk (Transaction_notifier).
 * Hanya inline style karena banyak klien email mengabaikan <style>.
 */
?>
<div style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #333; max-width: 640px;">
    <p>Halo <?= html_escape($recipient['username']); ?>,</p>
    <p>
        Ada <b><?= count($trx['items']); ?> aset</b> yang dikirim ke
        <b><?= html_escape($trx['room_name']); ?></b><?= $trx['building_name'] ? ', ' . html_escape($trx['building_name']) : ''; ?>
        dan menunggu diterima.
    </p>

    <table cellpadding="4" cellspacing="0" style="font-size: 13px; margin-bottom: 12px;">
        <tr><td style="color: #858796;">Kode transaksi</td><td><b><?= html_escape($trx['code']); ?></b></td></tr>
        <tr><td style="color: #858796;">Dikirim</td><td><?= $trx['created_at'] ? date('d-m-Y H:i', strtotime($trx['created_at'])) : '-'; ?><?= $trx['sender'] ? ' oleh ' . html_escape($trx['sender']) : ''; ?></td></tr>
        <?php if (!empty($trx['notes'])) : ?>
            <tr><td style="color: #858796;">Catatan</td><td><?= nl2br(html_escape($trx['notes'])); ?></td></tr>
        <?php endif; ?>
    </table>

    <table cellpadding="6" cellspacing="0" style="border-collapse: collapse; width: 100%; font-size: 13px;">
        <thead>
            <tr style="background: #4e73df; color: #fff; text-align: left;">
                <th>Aset</th>
                <th>Serial</th>
                <th>Dari</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($trx['items'] as $item) : ?>
                <tr style="border-bottom: 1px solid #e3e6f0;">
                    <td><b><?= html_escape($item['asset_name']); ?></b><?= $item['merk'] ? '<br><span style="color: #858796;">' . html_escape($item['merk']) . '</span>' : ''; ?></td>
                    <td><?= html_escape($item['serial_number']); ?></td>
                    <td><?= html_escape($item['source_room'] . ($item['source_building'] ? ', ' . $item['source_building'] : '')); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p style="margin-top: 20px;">
        <a href="<?= $detail_url; ?>" style="background: #1cc88a; color: #fff; padding: 10px 16px; border-radius: 4px; text-decoration: none;">Lihat &amp; terima kiriman</a>
    </p>
    <p style="color: #858796; font-size: 12px;">Periksa fisik aset sebelum menerima. Kalau aset tidak sesuai, tolak kiriman supaya aset kembali tercatat di ruangan asal.</p>
</div>
