<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h2>
        <center>Laporan Aset <?= $room['name'] ?> ( <?= $room['description'] ?> )</center>
    </h2>
    <hr />
    <div>

        <table border="1" cellpadding="5" cellspacing="0" class="table table-border table-sm table-hover" id="myTable">
            <thead>
                <tr>
                    <th width="20">No</th>
                    <th width="200">Nama</th>
                    <th width="30">Merk</th>
                    <th width="150">Nomor Seri</th>
                    <th>Lokasi Saat Ini</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; ?>
                <?php foreach ($assets as $asset) : ?>
                    <tr>
                        <td scope="row"> <?= $i; ?></td>
                        <td scope="row"><?= $asset['asset_name']; ?></td>
                        <td scope="row"><?= $asset['merk']; ?></td>
                        <td scope="row"><?= $asset['serial_number']; ?></td>
                        <?php if ($asset['placement_status'] == 1) : ?>
                            <td scope="row"><?= $asset['name']; ?></td>
                        <?php else : ?>
                            <td scope="row">Sedang Dipindahkan</td>
                        <?php endif ?>
                    </tr>
                    <?php $i++; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>