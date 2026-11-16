<?php
$is_open  = (int) $opname['status'] === 0;
$expected = array_values(array_filter($details, function ($d) { return (int) $d['result'] !== 2; }));
$wrong    = array_values(array_filter($details, function ($d) { return (int) $d['result'] === 2; }));
$days     = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$date_ref = strtotime($opname['finished_at'] ?: $opname['started_at']);
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
        .draft { color: #c0392b; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th, td { border: 1px solid #000; padding: 4px 5px; }
        th { background-color: #f2f2f2; }
        table.info td { border: none; padding: 2px 4px; }
        table.summary td { text-align: center; }
        table.summary .num { font-size: 14px; font-weight: bold; }
        .center { text-align: center; }
        .missing { color: #c0392b; font-weight: bold; }
        .found { color: #1e7e34; }
        h4 { margin: 10px 0 6px; font-size: 11px; }
        .signatures { width: 100%; margin-top: 30px; }
        .signature-box { width: 45%; float: left; text-align: center; }
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
        BERITA ACARA STOCK OPNAME ASET<br>
        NOMOR: <?= html_escape($opname['code']); ?>
        <?php if ($is_open) : ?><br><span class="draft">DRAF — OPNAME BELUM DISELESAIKAN</span><?php endif; ?>
    </div>

    <p>
        Pada hari ini, <strong><?= $days[(int) date('w', $date_ref)]; ?></strong> tanggal
        <strong><?= date('d-m-Y', $date_ref); ?></strong>, telah dilakukan pemeriksaan fisik (stock opname) aset dengan rincian sebagai berikut:
    </p>

    <table class="info">
        <tr>
            <td style="width: 22%;">Gedung / Lantai</td>
            <td>: <?= html_escape($opname['building_name'] . ' / ' . $opname['floor_name']); ?></td>
        </tr>
        <tr>
            <td>Ruangan</td>
            <td>: <?= html_escape($opname['room_name']); ?><?= !empty($opname['room_description']) ? ' (' . html_escape($opname['room_description']) . ')' : ''; ?></td>
        </tr>
        <tr>
            <td>Petugas</td>
            <td>: <?= html_escape($opname['username'] ?? '-'); ?></td>
        </tr>
        <tr>
            <td>Waktu pemeriksaan</td>
            <td>: <?= date('d-m-Y H:i', strtotime($opname['started_at'])); ?> s.d. <?= $opname['finished_at'] ? date('d-m-Y H:i', strtotime($opname['finished_at'])) : '-'; ?></td>
        </tr>
    </table>

    <table class="summary">
        <tr>
            <th>Aset Terdaftar</th>
            <th>Ditemukan</th>
            <th>Tidak Ditemukan</th>
            <th>Salah Ruangan</th>
        </tr>
        <tr>
            <td class="num"><?= (int) $opname['total_expected']; ?></td>
            <td class="num"><?= (int) $opname['total_found']; ?></td>
            <td class="num"><?= (int) $opname['total_missing']; ?></td>
            <td class="num"><?= (int) $opname['total_wrong_room']; ?></td>
        </tr>
    </table>

    <h4>A. Aset terdaftar di ruangan</h4>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th>Nama Aset</th>
                <th>Merk</th>
                <th>Nomor Seri</th>
                <th style="width: 13%;">Kondisi</th>
                <th style="width: 16%;">Hasil</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($expected)) : ?>
                <tr><td colspan="6" class="center">Tidak ada aset terdaftar</td></tr>
            <?php endif; ?>
            <?php foreach ($expected as $i => $d) : ?>
                <tr>
                    <td class="center"><?= $i + 1; ?></td>
                    <td><?= html_escape($d['asset_name'] ?? '(aset dihapus)'); ?></td>
                    <td><?= html_escape($d['merk'] ?? '-'); ?></td>
                    <td><?= html_escape($d['serial_number'] ?? '-'); ?></td>
                    <td class="center"><?= html_escape(asset_condition_label($d['asset_condition'] ?? null)); ?></td>
                    <td class="center">
                        <?php if ((int) $d['result'] === 1) : ?>
                            <span class="found">Ditemukan</span>
                        <?php else : ?>
                            <span class="missing"><?= $is_open ? 'Belum dicek' : 'Tidak ditemukan'; ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (!empty($wrong)) : ?>
        <h4>B. Aset ruangan lain yang ditemukan di ruangan ini</h4>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th>Nama Aset</th>
                    <th>Merk</th>
                    <th>Nomor Seri</th>
                    <th style="width: 13%;">Kondisi</th>
                    <th style="width: 16%;">Tercatat di</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($wrong as $i => $d) : ?>
                    <tr>
                        <td class="center"><?= $i + 1; ?></td>
                        <td><?= html_escape($d['asset_name'] ?? '(aset dihapus)'); ?></td>
                        <td><?= html_escape($d['merk'] ?? '-'); ?></td>
                        <td><?= html_escape($d['serial_number'] ?? '-'); ?></td>
                        <td class="center"><?= html_escape(asset_condition_label($d['asset_condition'] ?? null)); ?></td>
                        <td class="center"><?= html_escape($d['expected_room_name'] ?? '-'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <p>Catatan: <?= !empty($opname['notes']) ? html_escape($opname['notes']) : '-'; ?></p>
    <p>Demikian berita acara ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana mestinya.</p>

    <div class="signatures">
        <div class="signature-box">
            <p>Petugas Opname,</p>
            <br><br><br>
            <p>( <?= html_escape($opname['username'] ?? '......................................'); ?> )</p>
        </div>
        <div class="signature-box" style="float: right;">
            <p>Mengetahui,</p>
            <br><br><br>
            <p>( ...................................... )</p>
            <p>Pengelola Aset</p>
        </div>
        <div class="clear"></div>
    </div>
</body>

</html>
