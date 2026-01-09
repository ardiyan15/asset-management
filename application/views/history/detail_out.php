<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="card p-3">
        <div class="row">
            <div class="col-md-6">
                <h1 class="h3 mb-4 text-gray-800">
                    <?= $title; ?>
                </h1>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Detail Transaksi:
                    <?= $asset['transaction_id'] ?>
                </h6>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 150px;">Created Date</th>
                                <td>:
                                    <?= $asset['created_at']; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Notes</th>
                                <td>:
                                    <?= !empty($asset['notes']) ? $asset['notes'] : '-'; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Asset Name</th>
                                <th>Brand</th>
                                <th>Serial Number</th>
                                <th>Source Room</th>
                                <th>Destination Room</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($asset['assets'])): ?>
                                <?php $i = 1;
                                foreach ($asset['assets'] as $ast): ?>
                                    <tr>
                                        <td>
                                            <?= $i++; ?>
                                        </td>
                                        <td>
                                            <?= $ast['asset_name']; ?>
                                        </td>
                                        <td>
                                            <?= $ast['merk']; ?>
                                        </td>
                                        <td>
                                            <?= $ast['serial_number']; ?>
                                        </td>
                                        <td>
                                            <?= $ast['source_name']; ?>
                                        </td>
                                        <td>
                                            <?= $ast['destination_name']; ?>
                                        </td>
                                        <td>
                                            <?php if ($ast['status'] == 0): ?>
                                                <span class="badge badge-warning">Pending</span>
                                            <?php elseif ($ast['status'] == 1): ?>
                                                <span class="badge badge-success">Accepted</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Rejected</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No assets found for this transaction.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <a href="<?= base_url('history/asset_out'); ?>" class="btn btn-secondary rounded btn-sm">Back</a>
                    <a href="<?= base_url('history/print_bast/') . $trx_token; ?>"
                        class="btn btn-primary rounded btn-sm" target="_blank"><i class="fas fa-print"></i> Print
                        BAST</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End of Main Content -->