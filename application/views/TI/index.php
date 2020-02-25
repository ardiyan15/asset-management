<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->

    <?php if ($asset) : ?>

        <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>


        <div class="row">
            <div class="col-lg-10">

                <?= $this->session->flashdata('message') ?>
                <!-- <a href="" class="btn btn-primary mb-3" data-toggle="modal" data-target="#newMenuModal"> Add New Menu </a> -->
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Name</th>
                            <th scope="col">Merk</th>
                            <th scope="col">Qty</th>
                            <th scope="col">Serial Number</th>
                            <th scope="col">From</th>
                            <?php if ($user['user_code'] == 'IT') : ?>
                                <th scope="col"> Destination </th>
                            <?php endif; ?>
                            <th scope="col">Action</th>
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
                                <td scope="col"> <?= $ast['source']; ?> </td>
                                <?php if ($user['user_code'] == 'IT') : ?>
                                    <td scope="col"> <?= $ast['destination']; ?> </td>
                                <?php endif; ?>
                                <td scope="col">
                                    <a href="<?= base_url('TI/acc/') . $ast['id_detail_process']; ?>" class="btn btn-success btn-sm takein-button"> <i class="fas fa-check"></i> Accept </a>
                                    <a href="<?= base_url('TI/reject/') . $ast['id_detail_process']; ?>" class="btn btn-warning btn-sm reject-button"> <i class=" fas fa-times"></i> Reject </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                <?php else : ?>
                    <tr>
                        <td colspan="5">
                            <h4> Not Asset take in pending </h4>
                        </td>
                    </tr>
                <?php endif; ?>
                </table>
                <?php if ($asset) : ?>
            </div>
        </div>
</div>
</div>
<?php else : ?>
    </div>
    </div>
<?php endif; ?>
<!-- End of Main Content -->