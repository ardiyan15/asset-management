<!-- Begin Page Content -->
<div class="container-fluid">
    <?php if ($asset) : ?>
        <div class="row">
            <div class="col-md-6">
                <h3 class="mb-5 text-gray-800"><?= $title; ?></h3>
            </div>
            <div class="text-right col-md-6">
                <img class="mr-2" src="<?= base_url('assets/img/logo_raharja.png') ?>" width="50">
                <img src="<?= base_url('assets/img/kampus_merdeka.png') ?>" width="50">
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <?= $this->session->flashdata('message') ?>
                <form action="<?= base_url('to/print_page'); ?>" method="POST">
                    <table class="table table-hover" id="table-asset">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Name</th>
                                <th class="text-center">Merk</th>
                                <th class="text-center">Serial Number</th>
                                <th class="text-center">Sumber</th>
                                <th class="text-center">Tujuan</th>
                                <th class="text-center">Tanggal Pengiriman</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($asset as $ast) : ?>
                                <tr>
                                    <td class="text-center"> <?= $i; ?> </td>
                                    <td class="text-center"> <?= $ast['asset_name']; ?> </td>
                                    <td class="text-center"> <?= $ast['merk']; ?> </td>
                                    <td class="text-center"> <?= $ast['serial_number']; ?> </td>
                                    <td class="text-center"> <?= $ast['source_name']; ?> </td>
                                    <td class="text-center"> <?= $ast['name']; ?> </td>
                                    <td class="text-center"> <?= $ast['sent']; ?> </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                </form>
            <?php else : ?>
                <tr>
                    <td colspan="5">
                        <h4> Tidak Ada Proses Transaksi Keluar </h4>
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