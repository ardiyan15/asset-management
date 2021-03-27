<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->

    <?php if ($asset) : ?>

        <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>


        <div class="row">
            <div class="col-lg">

                <?= $this->session->flashdata('message') ?>
                <table class="table table-hover" id="table-asset">
                    <thead>
                        <tr>
                            <th class="text-center" scope="col">No</th>
                            <th class="text-center" scope="col">Name</th>
                            <th class="text-center" scope="col">Merk</th>
                            <th class="text-center" scope="col">Serial Number</th>
                            <?php if ($user['user_code'] == 'IT') : ?>
                                <th scope="col"> Destination </th>
                            <?php endif; ?>
                            <th class="text-center" scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($asset as $ast) : ?>
                            <tr>
                                <td class="text-center" scope="col"> <?= $i; ?> </td>
                                <td class="text-center" scope="col"> <?= $ast['asset_name']; ?> </td>
                                <td class="text-center" scope="col"> <?= $ast['merk']; ?> </td>
                                <td class="text-center" scope="col"> <?= $ast['serial_number']; ?> </td>
                                <td class="text-center" scope="col">
                                    <a href="<?= base_url('TI/acc/'). $ast['id'] ?>" class="btn btn-success btn-sm takein-button"> <i class="fas fa-check"></i> Accept </a>
                                    <a href="<?= base_url('TI/reject/') ?>" class="btn btn-warning btn-sm reject-button"> <i class=" fas fa-times"></i> Reject </a>
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