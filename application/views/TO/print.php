<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 text-gray-800"></h1>

    <div class="row">
        <div class="col-lg-8">
            <h2> Receipt Form </h2>
            <p> To : <b> <?= $asset[0]['destination']; ?> </b> </p>
            <p> From : <b> <?= $asset[0]['source']; ?> </b> </p>

            <table class="table table-hover table-bordered table-sm">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Name</th>
                        <th scope="col">Merk</th>
                        <th scope="col">Qty</th>
                        <th scope="col">Serial Number</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>
                    <?php foreach ($asset as $ast) : ?>
                        <tr>
                            <td scope="col"> <?= $i; ?> </td>
                            <td scope="col"> <?= $ast['asset_name']; ?> </td>
                            <td scope="col"> <?= $ast['merk']; ?> </td>
                            <td scope="col"> <?= $ast['qty']; ?> </td>
                            <td scope="col"> <?= $ast['serial_number']; ?> </td>
                        </tr>
                        <?php $i++; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="row">
                <div class="col-sm-6">
                    <h6> Sender </h6>
                    <p style="margin-top: 15%;"> Date </p>
                </div>

                <div class="col-sm-6">
                    <h6 style="margin-left: 40%;"> Receiver </h6>
                    <p style="margin-left: 42%; margin-top: 15%;"> Date </p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->
</div>

<script type="text/javascript">
    window.print();
</script>
<!-- End of Main Content -->