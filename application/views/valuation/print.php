<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?= html_escape($title); ?></title>
    <style>
        @page { margin: 12mm 10mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 8px; color: #111; }
        .header { text-align: center; margin-bottom: 10px; border-bottom: 2px solid #000; padding-bottom: 6px; }
        .logo-left { float: left; width: 45px; }
        .logo-right { float: right; width: 45px; }
        .header-text { margin-top: 8px; font-weight: bold; font-size: 12px; }
        .clear { clear: both; }
        h3 { text-align: center; margin: 4px 0 2px; font-size: 11px; }
        .sub { text-align: center; margin-bottom: 10px; color: #444; }
        h4 { margin: 10px 0 4px; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #555; padding: 3px 4px; }
        th { background: #e8ecf8; }
        .r { text-align: right; }
        .c { text-align: center; }
        tfoot td { font-weight: bold; background: #f4f4f4; }
        .muted { color: #777; }
    </style>
</head>

<body>
    <div class="header">
        <img src="assets/img/logo_bast.png" class="logo-left">
        <img src="assets/img/logo_bast.png" class="logo-right">
        <div class="header-text">ASSET MANAGEMENT SYSTEM</div>
        <div class="clear"></div>
    </div>

    <h3>LAPORAN NILAI ASET</h3>
    <div class="sub">
        Per <?= date('d-m-Y', strtotime($filters['as_of'])); ?> · <?= html_escape($scope_label); ?> ·
        Metode penyusutan garis lurus · dicetak <?= date('d-m-Y H:i'); ?>
    </div>

    <h4>A. Ringkasan per kategori</h4>
    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th class="c">Masa Manfaat</th>
                <th class="c">Nilai Sisa</th>
                <th class="c">Jumlah Aset</th>
                <th class="r">Harga Perolehan</th>
                <th class="r">Akumulasi Penyusutan</th>
                <th class="r">Nilai Buku</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($summary as $s) : ?>
                <tr>
                    <td><?= html_escape($s['name']); ?></td>
                    <td class="c"><?= $s['life_years'] ? (int) $s['life_years'] . ' th' : '-'; ?></td>
                    <td class="c"><?= $s['residual_percent'] !== null ? (float) $s['residual_percent'] . '%' : '-'; ?></td>
                    <td class="c"><?= $s['count']; ?></td>
                    <td class="r"><?= rupiah($s['cost']); ?></td>
                    <td class="r"><?= rupiah($s['accumulated']); ?></td>
                    <td class="r"><?= rupiah($s['book_value']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td>Total</td>
                <td></td>
                <td></td>
                <td class="c"><?= $totals['count']; ?></td>
                <td class="r"><?= rupiah($totals['cost']); ?></td>
                <td class="r"><?= rupiah($totals['accumulated']); ?></td>
                <td class="r"><?= rupiah($totals['book_value']); ?></td>
            </tr>
        </tfoot>
    </table>
    <?php if ($totals['missing'] > 0) : ?>
        <p class="muted"><?= $totals['missing']; ?> aset belum memiliki data harga/tanggal perolehan dan tidak ikut dihitung.</p>
    <?php endif; ?>

    <h4>B. Detail aset</h4>
    <table>
        <thead>
            <tr>
                <th class="c">No</th>
                <th>Nama Aset</th>
                <th>Nomor Seri</th>
                <th>Kategori</th>
                <th>Lokasi</th>
                <th>Vendor</th>
                <th class="c">Tgl Perolehan</th>
                <th class="c">Umur</th>
                <th class="r">Harga Perolehan</th>
                <th class="r">Susut/Bulan</th>
                <th class="r">Akumulasi</th>
                <th class="r">Nilai Buku</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $i => $row) : ?>
                <?php $d = $row['depreciation']; ?>
                <tr>
                    <td class="c"><?= $i + 1; ?></td>
                    <td><?= html_escape($row['asset_name'] . ' ' . $row['merk']); ?></td>
                    <td><?= html_escape($row['serial_number']); ?></td>
                    <td><?= html_escape($row['category_name'] ?? '-'); ?></td>
                    <td><?= html_escape($row['room_name'] . ', ' . $row['building_name']); ?></td>
                    <td><?= html_escape($row['vendor'] ?? '-'); ?></td>
                    <td class="c"><?= $row['purchase_date'] ? date('d-m-Y', strtotime($row['purchase_date'])) : '-'; ?></td>
                    <td class="c"><?= $d ? asset_age_label($d['age_months']) : '-'; ?></td>
                    <td class="r"><?= $d ? rupiah($d['price'], false) : '-'; ?></td>
                    <td class="r"><?= $d && $d['configured'] ? rupiah($d['monthly'], false) : '-'; ?></td>
                    <td class="r"><?= $d ? rupiah($d['accumulated'], false) : '-'; ?></td>
                    <td class="r"><?= $d ? rupiah($d['book_value'], false) : '-'; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>
