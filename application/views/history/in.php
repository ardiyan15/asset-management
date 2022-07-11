<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <a href="<?= base_url('history') ?>" class="btn btn-primary btn-sm mb-3">Kembali</a>
        </div>
        <div class="text-right col-md-6">
            <img class="mr-2" src="<?= base_url('assets/img/logo_raharja.png') ?>" width="50">
            <img src="<?= base_url('assets/img/kampus_merdeka.png') ?>" width="50">
        </div>
    </div>
    <?php if ($asset) : ?>
        <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>
        <div class="row">
            <div class="col-lg-12">
                <table class="table table-hover" id="myTable">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Nama</th>
                            <th class="text-center">Merk</th>
                            <th class="text-center">Nomor Seri</th>
                            <th class="text-center">Sumber</th>
                            <th class="text-center">Tujuan</th>
                            <th class="text-center">Tanggal Penerimaan</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($asset as $ast) : ?>
                            <tr>
                                <th class="text-center"><?= $i; ?></th>
                                <td class="text-center"><?= $ast['asset_name']; ?></td>
                                <td class="text-center"><?= $ast['merk']; ?></td>
                                <td class="text-center"><?= $ast['serial_number']; ?></td>
                                <td class="text-center"><?= $ast['source']; ?></td>
                                <td class="text-center"><?= $ast['name']; ?></td>
                                <td class="text-center"><?= $ast['received']; ?></td>
                                <td class="text-center">
                                    <?php if ($ast['status'] == 1) : ?>
                                        <span class="badge badge-success">Diterima</span>
                                    <?php else : ?>
                                        <span class="badge badge-danger">Ditolak</span>
                                    <?php endif; ?>
                                </td>

                            </tr>
                            <?php $i++; ?>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <h3> Tidak Ada Riwayat Transaksi Masuk </h3>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
</div>
<!-- /.container-fluid -->
</div>
<!-- End of Main Content -->