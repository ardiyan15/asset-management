<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->

    <div class="row">
        <div class="col-sm">
            <div class="row">
                <div class="col-sm" style="margin-top: 4.5%">
                    <h1 class="h3 text-gray-800"><?= $title; ?></h1>
                    <?php if ($user['user_code'] == 'IT') : ?>
                        <button type="button" class="btn btn-primary btn-sm mb-3" data-toggle="modal" data-target="#addAsset">
                            <i class="fas fa-plus mr-1"></i> Add Asset
                        </button>
                    <?php endif; ?>
                    <p style="font-size: 12px"> Total Asset's : <?= $amount_data; ?> </p>
                </div>
                <div class="col-sm">

                    <form action="<?= base_url('asset'); ?>" method="post">
                        <?php if ($user['user_code'] == 'IT') : ?>
                            <div class="input-group" style="margin-top: 25%;">
                            <?php else : ?>
                                <div class="input-group" style="margin-top: 17%;">
                                <?php endif; ?>
                                <input type="text" name="keyword" class="form-control" placeholder="search your asset" aria-describedby="basic-addon2">
                                <button type="submit" class="ml-2 btn btn-primary btn-sm"> <i class="fas fa-search"></i> Filter </button>
                                </div>
                    </form>
                </div>
            </div>

            <!-- Untuk Menampilkan pop up sweetalert -->
            <div class="flash-data" data-flashdata="<?= $this->session->flashdata('message'); ?>"></div>

            <div class="table-responsive mt-3">
                <table class="table table-border table-sm table-hover" id="table-asset">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th class="text-center">Name</th>
                            <th class="text-center">Merk</th>
                            <th class="text-center">Serial Number</th>
                            <th>Current Location</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($assets) : ?>
                            <?php $i = $this->uri->segment(3) + 1; ?>
                            <?php foreach ($assets as $asset) : ?>
                                <tr>
                                    <td scope="row"> <?= $i; ?></td>
                                    <td scope="row" class="text-center"><?= $asset['asset_name']; ?></td>
                                    <td scope="row" class="text-center"><?= $asset['merk']; ?></td>
                                    <td scope="row" class="text-center"><?= $asset['serial_number']; ?></td>
                                    <td scope="row" class="text-center"><?= $asset['asset_location']; ?></td>
                                    <?php
                                    if ($user['user_code'] == 'IT') : ?>
                                            <td scope="row">
                                                <a href="" class="btn btn-info btn-sm" data-toggle="modal" data-target="#editAsset<?= $asset['id_asset']; ?>"> <i class="fas fa-edit"></i> Edit </a>
                                                <a href="<?= base_url("asset/delete/") . $asset['id_asset']; ?>" class="btn btn-danger btn-sm delete-button"> <i class="fas fa-trash-alt"></i> Delete </a>
                                                <!-- <a href="" data-toggle="modal" data-target="#takeout<?= $asset['id_asset']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-reply"></i> Take Out </a> -->
                                            </td>
                                        <?php else : ?>
                                            <td>
                                                <?php if ($user['user_code'] == 'IT') : ?>
                                                    <a href="" class="btn btn-info btn-sm" data-toggle="modal" data-target="#editAsset<?= $asset['id_asset']; ?>"> <i class="fas fa-edit"></i> Edit </a>
                                                <?php endif; ?>
                                                <!-- <a href="" data-toggle="modal" data-target="#takeout<?= $asset['id_asset']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-reply"></i> Take Out </a> -->
                                            </td>
                                        <?php endif; ?>
                                </tr>
                                <?php $i++; ?>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="5">
                                    <h4> <?= $error; ?> </h4>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->
</div>
<!-- End of Main Content -->

<!-- Start Modal Add Menu -->
<div class="modal fade" id="addAsset" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Books&Beyond Asset</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="<?= base_url('Asset/add_asset'); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Name" required>
                    </div>
                    <div class="form-group">
                        <label for="merk">Merk</label>
                        <input type="text" class="form-control" id="merk" name="merk" placeholder="Merk" required>
                    </div>
                    <div class="form-group">
                        <label for="sn">Serial Number</label>
                        <input type="text" class="form-control" id="sn" name="sn" placeholder="Serial Number" required>
                    </div>
                    <div class="form-group">
                        <label for="origin">Origin Location</label>
                        <select class="form-control" id="origin" name="origin" required>
                            <option value=""> Origin Location </option>
                            <?php foreach ($store as $str) : ?>
                                <option value="<?= $str['store_code']; ?>"> <?= $str['store_code'] ?> - <?= $str['store_name'] ?> </option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('loc', '<small class="text-danger pl-3">', '</small>'); ?>
                    </div>
                    <div class="form-group">
                        <label for="loc">Location</label>
                        <select class="form-control" id="loc" name="loc" required>
                            <option value=""> Location </option>
                            <?php foreach ($store as $str) : ?>
                                <option value="<?= $str['store_code']; ?>"> <?= $str['store_code'] ?> - <?= $str['store_name']; ?> </option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('loc', '<small class="text-danger pl-3">', '</small>'); ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Add</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>