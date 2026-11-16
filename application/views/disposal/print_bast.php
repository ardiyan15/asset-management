<?php
$days      = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$reviewed  = array_filter(array_column($details, 'reviewed_at'));
$date_ref  = $reviewed ? strtotime(max($reviewed)) : strtotime($disposal['created_at']);
$reviewers = array_unique(array_filter(array_column($details, 'reviewed_by_name')));
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?= html_escape($title); ?></title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; }
        .header { text-align: center; margin-bottom: 14px; border-bottom: 2px solid #000; padding-bottom: 8px; }
        .logo-left { float: left; width: 55px; }
        .logo-right { float: right; width: 55px; }
        .header-text { margin-top: 12px; font-weight: bold; font-size: 13px; }
        .clear { clear: both; }
        .sub-header { text-align: center; margin-bottom: 14px; font-weight: bold; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th, td { border: 1px solid #000; padding: 4px 5px; }
        th { background-color: #f2f2f2; }
        table.info td { border: none; padding: 2px 4px; vertical-align: top; }
        .center { text-align: center; }
        .signatures { width: 100%; margin-top: 30px; }
        .signature-box { width: 32%; float: left; text-align: center; }
    </style>
</head>

<body>
    <div class="header">
        <img src="assets/img/logo_bast.png" class="logo-left">
        <img src="assets/img/logo_bast.png" class="logo-right">
        <div class="header-text">ASSET MANAGEMENT SYSTEM</div>
        <div class="clear"></div>
    </div>

    <div class="sub-header">
        BERITA ACARA PENGHAPUSAN ASET<br>
        NOMOR: <?= html_escape($disposal['code']); ?>
    </div>

    <p>
        Pada hari ini, <strong><?= $days[(int) date('w', $date_ref)]; ?></strong> tanggal
        <strong><?= date('d-m-Y', $date_ref); ?></strong>, telah disetujui penghapusan aset dari daftar inventaris
        dengan rincian sebagai berikut:
    </p>

    <table class="info">
        <tr>
            <td style="width: 22%;">Nomor pengajuan</td>
            <td>: <?= html_escape($disposal['code']); ?> (diajukan <?= date('d-m-Y', strtotime($disposal['created_at'])); ?>)</td>
        </tr>
        <tr>
            <td>Metode penghapusan</td>
            <td>: <?= html_escape(Disposal_model::method_label($disposal['method'])); ?></td>
        </tr>
        <tr>
            <td>Alasan</td>
            <td>: <?= nl2br(html_escape($disposal['reason'])); ?></td>
        </tr>
        <tr>
            <td>Jumlah aset</td>
            <td>: <?= count($details); ?> unit</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th>Nama Aset</th>
                <th>Merk</th>
                <th>Nomor Seri</th>
                <th>Lokasi Terakhir</th>
                <th style="width: 13%;">Kondisi</th>
                <th style="width: 14%;">Tgl Disetujui</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($details as $i => $d) : ?>
                <tr>
                    <td class="center"><?= $i + 1; ?></td>
                    <td><?= html_escape($d['asset_name'] ?? '-'); ?></td>
                    <td><?= html_escape($d['merk'] ?? '-'); ?></td>
                    <td><?= html_escape($d['serial_number'] ?? '-'); ?></td>
                    <td><?= html_escape(($d['room_name'] ?? '-') . ', ' . ($d['building_name'] ?? '-')); ?></td>
                    <td class="center"><?= html_escape(asset_condition_label($d['condition_at_request'])); ?></td>
                    <td class="center"><?= $d['reviewed_at'] ? date('d-m-Y', strtotime($d['reviewed_at'])) : '-'; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p>Dengan ditandatanganinya berita acara ini, aset di atas dinyatakan dihapus dari daftar aset aktif dan tanggung jawab pengelolaannya berakhir.</p>

    <div class="signatures">
        <div class="signature-box">
            <p>Diajukan oleh,</p>
            <br><br><br>
            <p>( <?= html_escape($disposal['requested_by_name'] ?? '......................'); ?> )</p>
            <p>Pengelola Gedung</p>
        </div>
        <div class="signature-box">
            <p>Disetujui oleh,</p>
            <br><br><br>
            <p>( <?= $reviewers ? html_escape(implode(', ', $reviewers)) : '......................'; ?> )</p>
            <p>Administrator Aset</p>
        </div>
        <div class="signature-box">
            <p>Mengetahui,</p>
            <br><br><br>
            <p>( ...................................... )</p>
            <p>Pimpinan</p>
        </div>
        <div class="clear"></div>
    </div>
</body>

</html>
