<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="card p-3">
        <div class="row">
            <div class="col-md-6">
                <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Detail Transaksi: <?= $assets[0]['transaction_id'] ?></h6>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 150px;">Created Date</th>
                                <td>: <?= $assets[0]['created_at']; ?></td>
                            </tr>
                            <tr>
                                <th>Notes</th>
                                <td>: <?= $assets[0]['notes'] ?? '-'; ?></td>
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
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1;
                            foreach ($assets as $asset): ?>
                                <tr>
                                    <td><?= $i++; ?></td>
                                    <td><?= $asset['asset_name']; ?></td>
                                    <td><?= $asset['merk']; ?></td>
                                    <td><?= $asset['serial_number']; ?></td>
                                    <td><?= $asset['source_room']; ?></td>
                                    <td><?= $asset['destination']; ?></td>
                                    <td>
                                        <?php if ($asset['status'] == 0): ?>
                                            <span class="badge badge-warning">Pending</span>
                                        <?php elseif ($asset['status'] == 1): ?>
                                            <span class="badge badge-success">Accepted</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Rejected</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($asset['status'] == 0): ?>
                                            <a href="<?= base_url('TI/acc/') . $asset['trx_token'] ?>"
                                                class="btn btn-success btn-sm rounded" title="Accept"> <i
                                                    class="fas fa-check"></i> </a>
                                            <a href="<?= base_url('TI/reject/') . $asset['trx_token'] ?>"
                                                class="btn btn-danger btn-sm rounded" title="Reject"> <i
                                                    class=" fas fa-times"></i> </a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <a href="<?= base_url('TI'); ?>" class="btn btn-secondary rounded">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End of Main Content -->