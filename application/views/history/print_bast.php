<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?></title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .logo-left { float: left; width: 60px; }
        .logo-right { float: right; width: 60px; }
        .header-text { margin-top: 10px; font-weight: bold; font-size: 14px; }
        .sub-header { text-align: center; margin-bottom: 20px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background-color: #f2f2f2; }
        .no-border { border: none; }
        .signatures { width: 100%; margin-top: 50px; }
        .signature-box { width: 45%; float: left; text-align: center; }
        .clear { clear: both; }
    </style>
</head>
<body>
    <div class="header">
        <img src="assets/img/logo_bast.png" class="logo-left">
        <img src="assets/img/logo_bast.png" class="logo-right">
        <div class="header-text">
            ASSET MANAGEMENT SYSTEM
        </div>
        <div class="clear"></div>
    </div>

    <div class="sub-header">
        BERITA ACARA SERAH TERIMA ASET<br>
        NOMOR: <?= $asset['transaction_id'] ?>
    </div>

    <p>Pada hari ini, tanggal <strong><?= date('d-m-Y', strtotime($asset['created_at'])) ?></strong>, telah dilakukan serah terima aset sebagai berikut:</p>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th>Nama Aset</th>
                <th>Merk</th>
                <th>Nomor Seri</th>
                <th>Kondisi/Status</th>
                <th>Lokasi Tujuan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($asset['assets'])): ?>
                <?php $i = 1; foreach ($asset['assets'] as $item): ?>
                <tr>
                    <td style="text-align: center;"><?= $i++ ?></td>
                    <td><?= $item['asset_name'] ?></td>
                    <td><?= $item['merk'] ?></td>
                    <td><?= $item['serial_number'] ?></td>
                    <td style="text-align: center;">
                        <?php 
                            if ($item['status'] == 1) echo 'Diterima';
                            elseif ($item['status'] == 2) echo 'Ditolak';
                            else echo 'Pending';
                        ?>
                    </td>
                    <td><?= $item['destination_name'] ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" style="text-align: center;">Tidak ada data aset</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <p>Catatan: <?= !empty($asset['notes']) ? $asset['notes'] : '-' ?></p>

    <div class="signatures">
        <div class="signature-box">
            <p>Yang Menyerahkan,</p>
            <br><br><br><br>
            <p>( ...................................... )</p>
            <p>Pengelola Aset</p>
        </div>
        <div class="signature-box" style="float: right;">
            <p>Yang Menerima,</p>
            <br><br><br><br>
            <p>( ...................................... )</p>
            <p>Penerima Aset</p>
        </div>
        <div class="clear"></div>
    </div>
</body>
</html>
