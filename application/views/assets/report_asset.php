<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
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


    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>