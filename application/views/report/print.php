<?php
$conditions        = asset_condition_options();
$show_disposed_col = $filters['status'] !== 'active';
?>
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
        .warn { color: #b7791f; font-weight: bold; }
        .bad { color: #c0392b; font-weight: bold; }
    </style>
</head>

<body>
    <div class="header">
        <img src="assets/img/logo_bast.png" class="logo-left">
        <img src="assets/img/logo_bast.png" class="logo-right">
        <div class="header-text">ASSET MANAGEMENT SYSTEM</div>
        <div class="clear"></div>
    </div>

    <h3>REKAP ASET PER <?= strtoupper($group_label); ?></h3>
    <div class="sub"><?= html_escape(implode(' · ', $filter_labels)); ?> · dicetak <?= date('d-m-Y H:i'); ?></div>

    <h4>A. Rekap</h4>
    <table>
        <thead>
            <tr>
                <th><?= $group_label; ?></th>
                <th class="c">Jumlah Aset</th>
                <?php foreach ($conditions as $label) : ?>
                    <th class="c"><?= $label; ?></th>
                <?php endforeach; ?>
                <?php if ($show_disposed_col) : ?><th class="c">Dihapus</th><?php endif; ?>
                <th class="r">Harga Perolehan</th>
                <th class="r">Nilai Buku</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($groups as $g) : ?>
                <tr>
                    <td><?= html_escape($g['label']); ?></td>
                    <td class="c"><?= $g['total']; ?></td>
                    <?php foreach ($conditions as $c => $label) : ?>
                        <td class="c <?= $c === 2 && $g['conditions'][$c] ? 'warn' : ($c === 3 && $g['conditions'][$c] ? 'bad' : ''); ?>"><?= $g['conditions'][$c]; ?></td>
                    <?php endforeach; ?>
                    <?php if ($show_disposed_col) : ?><td class="c"><?= $g['disposed']; ?></td><?php endif; ?>
                    <td class="r"><?= rupiah($g['cost']); ?></td>
                    <td class="r"><?= rupiah($g['book_value']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td>Total</td>
                <td class="c"><?= $totals['total']; ?></td>
                <?php foreach ($conditions as $c => $label) : ?>
                    <td class="c"><?= $totals['conditions'][$c]; ?></td>
                <?php endforeach; ?>
                <?php if ($show_disposed_col) : ?><td class="c"><?= $totals['disposed']; ?></td><?php endif; ?>
                <td class="r"><?= rupiah($totals['cost']); ?></td>
                <td class="r"><?= rupiah($totals['book_value']); ?></td>
            </tr>
        </tfoot>
    </table>

    <h4>B. Detail aset</h4>
    <table>
        <thead>
            <tr>
                <th class="c">No</th>
                <th>Nama Aset</th>
                <th>Merk</th>
                <th>Nomor Seri</th>
                <th>Kategori</th>
                <th>Gedung</th>
                <th>Ruangan</th>
                <th class="c">Kondisi</th>
                <th class="c">Status</th>
                <th class="r">Nilai Buku</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $i => $row) : ?>
                <?php
                $d        = $row['depreciation'];
                $disposed = (int) $row['is_disposed'] === 1;
                ?>
                <tr>
                    <td class="c"><?= $i + 1; ?></td>
                    <td><?= html_escape($row['asset_name']); ?></td>
                    <td><?= html_escape($row['merk']); ?></td>
                    <td><?= html_escape($row['serial_number']); ?></td>
                    <td><?= html_escape($row['category_name'] ?? '-'); ?></td>
                    <td><?= html_escape($row['building_name']); ?></td>
                    <td><?= html_escape($row['room_name']); ?></td>
                    <td class="c"><?= html_escape(asset_condition_label($row['asset_condition'])); ?></td>
                    <td class="c"><?= $disposed ? 'Dihapus' : ((int) $row['placement_status'] === 1 ? 'Di ruangan' : 'Dipindahkan'); ?></td>
                    <td class="r"><?= $d && !$disposed ? rupiah($d['book_value'], false) : '-'; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>
