<?php
/**
 * Email pengingat peminjaman terlambat (dipakai Cron::loan_reminders). Hanya inline style.
 */
?>
<div style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #333; max-width: 640px;">
    <p>Halo <?= html_escape($recipient['username']); ?>,</p>
    <p>Ada <b><?= count($items); ?></b> aset yang terlambat dikembalikan peminjam:</p>

    <table cellpadding="6" cellspacing="0" style="border-collapse: collapse; width: 100%; font-size: 13px;">
        <thead>
            <tr style="background: #e74a3b; color: #fff; text-align: left;">
                <th>Aset</th>
                <th>Peminjam</th>
                <th>Batas kembali</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $l) : ?>
                <tr style="border-bottom: 1px solid #e3e6f0;">
                    <td><b><?= html_escape($l['asset_name']); ?></b><br><span style="color: #858796;"><?= html_escape($l['serial_number'] . ' · ' . $l['room_name'] . ', ' . $l['building_name']); ?></span></td>
                    <td>
                        <?= html_escape($l['borrower_name']); ?><?= $l['borrower_unit'] ? '<br><span style="color: #858796;">' . html_escape($l['borrower_unit']) . '</span>' : ''; ?>
                        <?= $l['borrower_phone'] ? '<br>' . html_escape($l['borrower_phone']) : ''; ?>
                    </td>
                    <td>
                        <?= date('d-m-Y', strtotime($l['due_date'])); ?><br>
                        <span style="color: #e74a3b; font-weight: bold;"><?= loan_status($l)['label']; ?></span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p style="margin-top: 20px;">
        <a href="<?= base_url('loan?status=overdue'); ?>" style="background: #4e73df; color: #fff; padding: 10px 16px; border-radius: 4px; text-decoration: none;">Buka daftar peminjaman</a>
    </p>
    <p style="color: #858796; font-size: 12px;">Setelah aset kembali, catat pengembaliannya di aplikasi supaya pengingat berhenti.</p>
</div>
