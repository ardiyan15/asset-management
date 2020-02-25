<?php
// header("Content-type: application/octet-stream");
// header("Content-Disposition: attachment; filename=$title.Xlsx");
// header("Pragma: no-cache");
// header("Expires: 0");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

<body>

    <a href="<?= base_url('report/export'); ?>"> Export Test </a>
    <h1> Report Asset Books&Beyond </h1>
    <table class="table" border="1" width="100%">

        <thead class="thead-dark">
            <tr>
                <th> No </th>
                <th>Name</th>
                <th>Merk</th>
                <th>Qty</th>
                <th>Serial Number</th>
                <th> Origin Location </th>
                <th> Current Location</th>
            </tr>
        </thead>

        <tbody>

            <?php $i = 1;
            foreach ($asset as $ast) : ?>
                <tr>
                    <td align="center"><?= $i; ?>
                    <td align="center"><?= $ast['asset_name']; ?></td>
                    <td align="center"><?= $ast['merk']; ?></td>
                    <td align="center"><?= $ast['qty']; ?></td>
                    <td align="center"><?= $ast['serial_number']; ?></td>
                    <td align="center"><?= $ast['origin_location']; ?></td>
                    <td align="center"><?= $ast['asset_location']; ?></td>
                </tr>
            <?php $i++;
            endforeach; ?>
        </tbody>
    </table>


    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>