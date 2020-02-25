<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <?php if ($asset) : ?>
        <h1 class="h3 text-gray-800"><?= $title; ?></h1>
        <div class="row">
            <div class="col-lg-8">
                <?= $this->session->flashdata('message') ?>
                <!-- <a href="" class="btn btn-primary mb-3" data-toggle="modal" data-target="#newMenuModal"> Add New Menu </a> -->
                <!-- <a href="<?= base_url('to/print_page/') . $asset[0]['destination']; ?>" class="float-right" target="_blank"> Print </a> -->
                <form action="<?= base_url('to/print_page'); ?>" method="POST">
                    <button class="float-right" id="btn"> Print </button>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Name</th>
                                <th scope="col">Merk</th>
                                <th scope="col">Qty</th>
                                <th scope="col">Serial Number</th>
                                <th scope="col"> Destination </th>
                                <th scope="col"> Create Time </th>
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
                                    <td scope="col"> <?= $ast['destination']; ?> </td>
                                    <td scope="col"> <?= $ast['createtime']; ?> </td>
                                    <td scope="col">
                                        <input type="checkbox" id="ast" name="sndAst[]" value="<?= $ast['id_detail_process']; ?>" />
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                </form>
            <?php else : ?>
                <tr>
                    <td colspan="5">
                        <h4> Not asset take out process </h4>
                    </td>
                </tr>
            <?php endif; ?>
            </table>
            </div>
        </div>
        <?php if ($asset) : ?>
</div>
</div>

<?php endif; ?>

<script>
    var checker = document.getElementById('ast');
    var print = document.getElementById('btn');

    checker.onchange = function() {
        print.removeAttribute('disabled');
    }
</script>