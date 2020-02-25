<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <?php if ($asset) : ?>

        <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

        <div class="row">
            <div class="col-lg-9">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Name</th>
                            <th scope="col">Merk</th>
                            <th scope="col">Qty</th>
                            <th scope="col">Serial Number</th>
                            <th scope="col">Source</th>
                            <th scope="col">Receive Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($asset as $ast) : ?>
                            <tr>
                                <th scope="row"><?= $i; ?></th>
                                <td><?= $ast['asset_name']; ?></td>
                                <td><?= $ast['merk']; ?></td>
                                <td><?= $ast['qty']; ?></td>
                                <td><?= $ast['serial_number']; ?></td>
                                <td><?= $ast['source']; ?></td>
                                <td><?= $ast['acctime']; ?></td>
                            </tr>
                            <?php $i++; ?>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <h3> Data not found </h3>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
</div>
<!-- /.container-fluid -->
</div>
<!-- End of Main Content -->