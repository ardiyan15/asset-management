<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->

    <div class="row">
        <div class="col-sm-11.8">
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
                                <button type="submit" class="ml-2 btn btn-primary btn-sm"> <i class="fas fa-search"></i> Search </button>
                                </div>
                    </form>
                </div>
            </div>

            <!-- Untuk Menampilkan pop up sweetalert -->
            <div class="flash-data" data-flashdata="<?= $this->session->flashdata('message'); ?>"></div>

            <div class="table-responsive">


                <table class="table table-sm table-hover table-fixed">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col" class="text-center">Name</th>
                            <th scope="col" class="text-center">Merk</th>
                            <th scope="col">Qty</th>
                            <th scope="col" class="text-center">Serial Number</th>
                            <th scope="col">Origin Location</th>
                            <th scope="col">Current Location</th>
                            <?php
                            if ($user['user_code'] == 'IT') : ?>
                                <th scope="col">Status</th>
                            <?php endif; ?>
                            <th class="text-center" scope="col" colspan="2">Action</th>

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
                                    <td scope="row" class="text-center"><?= $asset['qty']; ?></td>
                                    <td scope="row" class="text-center"><?= $asset['serial_number']; ?></td>
                                    <td scope="row" class="text-center"><?= $asset['origin_location']; ?></td>
                                    <td scope="row" class="text-center"><?= $asset['asset_location']; ?></td>
                                    <?php
                                    if ($user['user_code'] == 'IT') :
                                        if ($asset['status'] == 0) : ?>
                                            <td scope="row"><?= 'Good'; ?></td>
                                        <?php else : ?>
                                            <td scope="row"> <?= 'Bad'; ?>
                                            <?php endif; ?>
                                            <td scope="row">
                                                <a href="" class="btn btn-info btn-sm" data-toggle="modal" data-target="#editAsset<?= $asset['id_asset']; ?>"> <i class="fas fa-edit"></i> Edit </a>
                                                <a href="<?= base_url("asset/delete/") . $asset['id_asset']; ?>" class="btn btn-danger btn-sm delete-button"> <i class="fas fa-trash-alt"></i> Delete </a>
                                                <a href="" data-toggle="modal" data-target="#takeout<?= $asset['id_asset']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-reply"></i> Take Out </a>
                                            </td>
                                        <?php else : ?>
                                            <td>
                                                <?php if ($user['user_code'] == 'IT') : ?>
                                                    <a href="" class="btn btn-info btn-sm" data-toggle="modal" data-target="#editAsset<?= $asset['id_asset']; ?>"> <i class="fas fa-edit"></i> Edit </a>
                                                <?php endif; ?>
                                                <a href="" data-toggle="modal" data-target="#takeout<?= $asset['id_asset']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-reply"></i> Take Out </a>
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
            <div class="float-left">
                <?= $pagination; ?>
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

<!-- End Modal Add Menu -->

<!-- Start Modal Edit Menu -->
<?php

foreach ($assets as $asset) :
    $id = $asset['id_asset'];
    $name = $asset['asset_name'];
    $merk = $asset['merk'];
    $sn = $asset['serial_number'];

?>
    <div class="modal fade" id="editAsset<?= $id; ?>" tabindex="-1" role="dialog">
        <div class=" modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Books&Beyond Asset</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="<?= base_url("asset/edit_asset/") . $asset['id_asset']; ?>">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Name" value="<?= $name; ?>">
                        </div>
                        <div class="form-group">
                            <label for="merk">Merk</label>
                            <input type="text" class="form-control" id="merk" name="merk" placeholder="Merk" value="<?= $merk; ?>">
                        </div>
                        <div class="form-group">
                            <label for="sn">Serial Number</label>
                            <input type="text" class="form-control" id="sn" name="sn" placeholder="Serial Number" value="<?= $sn; ?>">
                        </div>
                        <div class="form-group">
                            <label for="loc">Location</label>
                            <select class="form-control" id="loc" name="loc" required>
                                <?php foreach ($store as $loc) : ?>
                                    <?php if ($loc['store_code'] == $asset['asset_location']) : ?>
                                        <option value="<?= $loc['store_code'] ?>" selected> <?= $loc['store_code'] . " " . $loc['store_name']; ?> </option>
                                    <?php else : ?>
                                        <option value="<?= $loc['store_code'] ?>"> <?= $loc['store_code'] . " " . $loc['store_name']; ?> </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="0"> Good </option>
                                <option value="1"> Bad </option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php endforeach; ?>

<!-- End Modal Edit Menu -->

<!-- Start Modal Take out -->

<?php
foreach ($assets as $asset) :
    $id = $asset['id_asset'];
    $name = $asset['asset_name'];
    $crnt_location = $asset['asset_location'];
    $merk = $asset['merk'];
    $sn = $asset['serial_number'];

    // foreach ($store as $loc) :

?>
    <div class="modal fade" id="takeout<?= $id; ?>" tabindex="-1" role="dialog">
        <div class=" modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Take Out Your Asset</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="<?= base_url("to/to_process/") . $asset['id_asset']; ?>">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id" value="<?= $id; ?>" />
                        <input type="hidden" name="source" id="source" value="<?= $crnt_location; ?>">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Name" value="<?= $name; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="merk">Merk</label>
                            <input type="text" class="form-control" id="merk" name="merk" placeholder="Merk" value="<?= $merk; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="sn">Serial Number</label>
                            <input type="text" class="form-control" id="sn" name="sn" placeholder="Serial Number" value="<?= $sn; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="loc">Location</label>
                            <select class="form-control" id="loc" name="loc" required>
                                <?php foreach ($store as $loc) : ?>
                                    <?php if ($loc['user_code'] == $asset['asset_location']) : ?>
                                        <option value="<?= $loc['store_code'] ?>" selected> <?= $loc['store_code'] . " " . $loc['user_name']; ?> </option>
                                    <?php else : ?>
                                        <option value="<?= $loc['store_code'] ?>"> <?= $loc['store_code'] . " " . $loc['store_name']; ?> </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="loc">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="0"> Good </option>
                                <option value="1"> Bad </option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Take Out</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
    </div>
<?php endforeach; ?>



<!-- End Modal Take Out -->