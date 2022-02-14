<!-- Begin Page Content -->
<div class="container-fluid">
    <?php if ($asset) : ?>
        <div class="row">
            <div class="col-md-6">
                <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>
            </div>
            <div class="text-right col-md-6">
                <img class="mr-2" src="<?= base_url('assets/img/logo_raharja.png') ?>" width="50">
                <img src="<?= base_url('assets/img/kampus_merdeka.png') ?>" width="50">
            </div>
        </div>
        <div class="row">
            <div class="col-lg">
                <?= $this->session->flashdata('message') ?>
                <table class="table table-hover" id="table-asset">
                    <thead>
                        <tr>
                            <th class="text-center" scope="col">No</th>
                            <th class="text-center" scope="col">Nama</th>
                            <th class="text-center" scope="col">Merk</th>
                            <th class="text-center" scope="col">Nomor Seri</th>
                            <th class="text-center" scope="col">Sumber</th>
                            <th class="text-center">Tujuan</th>
                            <th class="text-center">Aksi</th>
                            <th class="text-center">
                                <button class="btn btn-success btn-sm" id="btn-acc">Terima</button>
                            </th>
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
                                <td class="text-center" scope="col"> <?= $ast['name']; ?> </td>
                                <td class="text-center" scope="col"> <?= $ast['destination']; ?> </td>
                                <td class="text-center" scope="col">
                                    <a href="<?= base_url('TI/acc/') . $ast['id'] ?>" class="btn btn-success btn-sm takein-button"> <i class="fas fa-check"></i> Terima </a>
                                    <!-- <a href="<?= base_url('TI/reject/') ?>" class="btn btn-danger btn-sm reject-button"> <i class=" fas fa-times"></i> Tolak </a> -->
                                </td>
                                <td class="text-center">
                                    <div class="form-check">
                                        <input class="form-check-input" name="check_in" type="checkbox" value="<?= $ast['id_asset'] ?>">
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                <?php else : ?>
                    <tr>
                        <td colspan="5">
                            <h4> Tidak Ada Proses Transaksi Masuk </h4>
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