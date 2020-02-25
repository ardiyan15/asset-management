<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <?php if ($asset) : ?>

        <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

        <div class="row">
            <div class="col-lg-10">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th class="text-center" scope="col">Name</th>
                            <th class="text-center" scope="col">Merk</th>
                            <th class="text-center" scope="col">Qty</th>
                            <th class="text-center" scope="col">Serial Number</th>
                            <th class="text-center" scope="col">Source</th>
                            <th class="text-center" scope="col">Destination</th>
                            <th class="text-center" scope="col">Send Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($asset as $ast) : ?>
                            <tr>
                                <th scope="row"><?= $i; ?></th>
                                <td class="text-center"><?= $ast['asset_name']; ?></td>
                                <td class="text-center"><?= $ast['merk']; ?></td>
                                <td class="text-center"><?= $ast['qty']; ?></td>
                                <td class="text-center"><?= $ast['serial_number']; ?></td>
                                <td class="text-center"><?= $ast['source']; ?></td>
                                <td class="text-center"><?= $ast['destination']; ?></td>
                                <td class="text-center"><?= $ast['createtime']; ?></td>
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