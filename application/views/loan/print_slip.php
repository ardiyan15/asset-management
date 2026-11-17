<?php $returned = (int) $loan['status'] === 1; ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?= html_escape($title); ?></title>
    <style>
        @page { margin: 10mm 12mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9.5px; color: #111; }
        .header { border-bottom: 2px solid #000; padding-bottom: 5px; margin-bottom: 8px; }
        .logo { float: left; width: 38px; margin-right: 8px; }
        .org { font-weight: bold; font-size: 12px; padding-top: 4px; }
        .doc { font-size: 10px; }
        .code { float: right; text-align: right; font-size: 9px; padding-top: 4px; }
        .clear { clear: both; }
        table { width: 100%; border-collapse: collapse; }
        table.info td { padding: 2px 4px; vertical-align: top; }
        table.info td.k { width: 26%; color: #444; }
        .box { border: 1px solid #999; padding: 5px 6px; margin-top: 6px; }
        .box h4 { margin: 0 0 3px; font-size: 9.5px; }
        .cols td { width: 50%; vertical-align: top; }
        .sign { margin-top: 10px; }
        .sign td { width: 33%; text-align: center; vertical-align: top; }
        .stamp { border: 1px solid #c0392b; color: #c0392b; padding: 1px 5px; font-weight: bold; }
        .ok { border-color: #1e7e34; color: #1e7e34; }
        .muted { color: #666; }
    </style>
</head>

<body>
    <div class="header">
        <img src="assets/img/logo_bast.png" class="logo">
        <div class="code">
            No. <b><?= html_escape($loan['code']); ?></b><br>
            <?php if ($returned) : ?>
                <span class="stamp ok">DIKEMBALIKAN</span>
            <?php elseif ($state['key'] === 'overdue') : ?>
                <span class="stamp"><?= strtoupper($state['label']); ?></span>
            <?php endif; ?>
        </div>
        <div class="org">ASSET MANAGEMENT SYSTEM</div>
        <div class="doc">BUKTI PEMINJAMAN ASET</div>
        <div class="clear"></div>
    </div>

    <table class="cols">
        <tr>
            <td>
                <table class="info">
                    <tr><td class="k">Aset</td><td>: <b><?= html_escape($loan['asset_name']); ?></b> <?= html_escape($loan['merk']); ?></td></tr>
                    <tr><td class="k">Nomor seri</td><td>: <?= html_escape($loan['serial_number']); ?></td></tr>
                    <tr><td class="k">Lokasi asal</td><td>: <?= html_escape($loan['room_name'] . ', ' . $loan['floor_name'] . ', ' . $loan['building_name']); ?></td></tr>
                    <tr><td class="k">Kondisi keluar</td><td>: <?= html_escape(asset_condition_label($loan['condition_out'])); ?></td></tr>
                </table>
            </td>
            <td>
                <table class="info">
                    <tr><td class="k">Peminjam</td><td>: <b><?= html_escape($loan['borrower_name']); ?></b></td></tr>
                    <tr><td class="k">Unit / divisi</td><td>: <?= html_escape($loan['borrower_unit'] ?: '-'); ?></td></tr>
                    <tr><td class="k">No. HP</td><td>: <?= html_escape($loan['borrower_phone'] ?: '-'); ?></td></tr>
                    <tr><td class="k">Keperluan</td><td>: <?= html_escape($loan['purpose'] ?: '-'); ?></td></tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="box">
        <table class="info">
            <tr>
                <td class="k">Tanggal pinjam</td>
                <td>: <?= date('d-m-Y H:i', strtotime($loan['loaned_at'])); ?></td>
                <td class="k">Batas kembali</td>
                <td>: <b><?= date('d-m-Y', strtotime($loan['due_date'])); ?></b><?= (int) $loan['extended_count'] > 0 ? ' <span class="muted">(diperpanjang ' . (int) $loan['extended_count'] . '×)</span>' : ''; ?></td>
            </tr>
            <?php if ($returned) : ?>
                <tr>
                    <td class="k">Tanggal kembali</td>
                    <td>: <?= date('d-m-Y H:i', strtotime($loan['returned_at'])); ?></td>
                    <td class="k">Kondisi kembali</td>
                    <td>: <?= html_escape(asset_condition_label($loan['condition_in'])); ?></td>
                </tr>
                <?php if ($loan['return_notes']) : ?>
                    <tr><td class="k">Catatan</td><td colspan="3">: <?= html_escape($loan['return_notes']); ?></td></tr>
                <?php endif; ?>
            <?php endif; ?>
        </table>
    </div>

    <p class="muted" style="margin: 6px 0 0;">Peminjam bertanggung jawab atas aset selama dipinjam dan wajib mengembalikannya dalam kondisi seperti saat dipinjam paling lambat pada batas kembali.</p>

    <table class="sign">
        <tr>
            <td>Peminjam,<br><br><br><br>( <?= html_escape($loan['borrower_name']); ?> )</td>
            <td>Petugas (keluar),<br><br><br><br>( <?= html_escape($loan['created_by_name'] ?? '....................'); ?> )</td>
            <td>Petugas (kembali),<br><br><br><br>( <?= $returned ? html_escape($loan['returned_by_name'] ?? '....................') : '....................'; ?> )</td>
        </tr>
    </table>
</body>

</html>
