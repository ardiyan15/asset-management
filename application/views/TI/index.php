<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="card p-3">
        <div class="row">
            <div class="col-md-6">
                <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>
            </div>
        </div>
        <div class="row">
            <div class="col-lg">
                <div class="flash-data" data-flashdata="<?= $this->session->flashdata('message'); ?>"></div>
                <table class="table table-hover" id="myTable">
                    <thead>
                        <tr>
                            <th class="text-center" scope="col">No</th>
                            <th class="text-center" scope="col">Transaction ID</th>
                            <th class="text-center" scope="col">Created Date</th>
                            <th class="text-center" scope="col">Notes</th>
                            <th class="text-center" scope="col">Aksi</th>
                            <th class="text-center" scope="col">
                                <button class="btn btn-success btn-sm rounded" id="btn-acc">Terima</button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($asset as $ast): ?>
                            <tr>
                                <td class="text-center" scope="col"> <?= $i; ?> </td>
                                <td class="text-center" scope="col">
                                    <a href="<?= site_url('TI/detail/' . $ast['trx_token']) ?>">
                                        <?= $ast['transaction_id']; ?>
                                    </a>
                                </td>
                                <td class="text-center" scope="col"> <?= $ast['created_at']; ?> </td>
                                <td class="text-center" scope="col"> <?= $ast['notes'] ?? '-' ?> </td>
                                <td class="text-center" scope="col">
                                    <a href="<?= base_url('TI/acc/') . $ast['id'] ?>"
                                        class="btn btn-success btn-sm takein-button rounded"> <i class="fas fa-check"></i>
                                        Terima </a>
                                    <a href="<?= base_url('TI/reject/') . $ast['id'] ?>"
                                        class="btn btn-danger btn-sm reject-button rounded"> <i class=" fas fa-times"></i>
                                        Tolak </a>
                                </td>
                                <td class="text-center">
                                    <div class="form-check">
                                        <input class="form-check-input" name="check_in" type="checkbox"
                                            value="<?= $ast['id_asset'] ?>">
                                    </div>
                                </td>
                            </tr>
                            <?php $i++; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- End of Main Content -->