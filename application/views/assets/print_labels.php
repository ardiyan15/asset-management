<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title><?= html_escape($title); ?></title>
    <style>
        @page { margin: 10mm 7mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 7pt; color: #222; margin: 0; }
        table.sheet { width: 100%; border-collapse: separate; border-spacing: 2mm 2mm; table-layout: fixed; }
        table.sheet tr { page-break-inside: avoid; }
        td.label { width: 33.33%; height: 34mm; border: 0.3mm dashed #9aa0b4; border-radius: 2mm; padding: 2mm; vertical-align: middle; }
        td.empty { border: 0; }
        table.inner { width: 100%; border-collapse: collapse; }
        td.qr { width: 25mm; vertical-align: middle; }
        td.qr img { width: 24mm; height: 24mm; }
        td.info { vertical-align: top; padding-left: 1.5mm; }
        .org { font-size: 5.5pt; letter-spacing: .4pt; text-transform: uppercase; color: #4e73df; font-weight: bold; }
        .name { font-size: 8.5pt; font-weight: bold; margin: .8mm 0 .3mm; line-height: 1.15; }
        .merk { font-size: 6.5pt; color: #555; }
        .serial { font-family: DejaVu Sans Mono, monospace; font-size: 6.5pt; font-weight: bold; background: #eef1fb; padding: .4mm 1mm; margin-top: 1.2mm; display: inline-block; }
        .loc { font-size: 5.8pt; color: #555; margin-top: 1.2mm; line-height: 1.25; }
    </style>
</head>

<body>
    <table class="sheet">
        <?php foreach (array_chunk($assets, 3) as $row) : ?>
            <tr>
                <?php foreach ($row as $asset) : ?>
                    <td class="label">
                        <table class="inner">
                            <tr>
                                <td class="qr"><img src="<?= $asset['qr']; ?>" alt=""></td>
                                <td class="info">
                                    <div class="org">Asset Management</div>
                                    <div class="name"><?= html_escape($asset['asset_name']); ?></div>
                                    <div class="merk"><?= html_escape($asset['merk']); ?></div>
                                    <div class="serial"><?= html_escape($asset['serial_number']); ?></div>
                                    <div class="loc">
                                        <?= html_escape($asset['building_name']); ?> &middot; <?= html_escape($asset['floor_name']); ?><br>
                                        Ruang <?= html_escape($asset['room_name']); ?>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                <?php endforeach; ?>
                <?php for ($i = count($row); $i < 3; $i++) : ?>
                    <td class="label empty"></td>
                <?php endfor; ?>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>
