<div class="container-fluid">
    <div class="card p-3">
        <?php if ($asset): ?>
            <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>
            <div class="row">
                <div class="col-lg-12">
                    <table class="table table-hover" id="myTable">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Transaction ID</th>
                                <th class="text-center">Created Date</th>
                                <th class="text-center">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($asset as $ast): ?>
                                <tr>
                                    <th class="text-center"><?= $i; ?></th>
                                    <td class="text-center">
                                        <a href="<?= base_url('history/detail_out/') . $ast['trx_token'] ?>">
                                            <?= $ast['transaction_id']; ?>
                                        </a>
                                    </td>
                                    <td class="text-center"><?= $ast['created_at']; ?></td>
                                    <td class="text-center"><?= !empty($ast['notes']) ? $ast['notes'] : '-' ?></td>
                                </tr>
                                <?php $i++; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <h3> Tidak Ada Riwayat Transaksi Keluar </h3>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-md-6">
                        <a href="<?= base_url('history') ?>" class="btn btn-secondary rounded btn-sm mb-3">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->
</div>
<!-- End of Main Content -->