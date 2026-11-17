<?php
/**
 * Email pengingat jadwal maintenance (dipakai Cron::maintenance_reminders).
 * Hanya inline style karena banyak klien email mengabaikan <style>.
 */
?>
<div style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #333; max-width: 640px;">
    <p>Halo <?= html_escape($recipient['username']); ?>,</p>
    <p>Ada <b><?= count($items); ?></b> jadwal maintenance aset yang sudah jatuh tempo atau akan segera jatuh tempo:</p>

    <table cellpadding="6" cellspacing="0" style="border-collapse: collapse; width: 100%; font-size: 13px;">
        <thead>
            <tr style="background: #4e73df; color: #fff; text-align: left;">
                <th>Perawatan</th>
                <th>Aset</th>
                <th>Lokasi</th>
                <th>Jatuh tempo</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $s) : ?>
                <?php $due = maintenance_due_status($s['next_due_date'], $s['remind_days_before']); ?>
                <tr style="border-bottom: 1px solid #e3e6f0;">
                    <td><b><?= html_escape($s['title']); ?></b><br><span style="color: #858796;"><?= maintenance_interval_label($s['interval_value'], $s['interval_unit']); ?></span></td>
                    <td><?= html_escape($s['asset_name']); ?><br><span style="color: #858796;"><?= html_escape($s['serial_number']); ?></span></td>
                    <td><?= html_escape($s['room_name'] . ', ' . $s['building_name']); ?></td>
                    <td>
                        <?= date('d-m-Y', strtotime($s['next_due_date'])); ?><br>
                        <span style="color: <?= $due['color'] === 'danger' ? '#e74a3b' : '#b7791f'; ?>; font-weight: bold;"><?= $due['label']; ?></span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p style="margin-top: 20px;">
        <a href="<?= base_url('maintenance'); ?>" style="background: #4e73df; color: #fff; padding: 10px 16px; border-radius: 4px; text-decoration: none;">Buka jadwal maintenance</a>
    </p>
    <p style="color: #858796; font-size: 12px;">Setelah pekerjaan selesai, catat di aplikasi supaya jadwal maju ke periode berikutnya dan pengingat berhenti.</p>
</div>
