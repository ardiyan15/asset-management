<div class="container-fluid">


    <!-- Untuk Memunculkan pop up sweetalert -->
    <div class="flash-data" data-flashdata="<?= $this->session->flashdata('message'); ?>"></div>


    <h3> <?= $title; ?> </h3>
    <a href="" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addStrLocation"> <i class="fas fa-plus mr-1"></i> Add New Store </a>
    <div class="container">
        <div class="row row-cols-4">
            <?php foreach ($strLocations as $str) : ?>
                <div class="col-sm-4 mt-3">
                    <div class="card">
                        <h5 class="card-header"><?= $str['store_code']; ?></h5>
                        <div class="card-body">
                            <h5 class="card-title"><?= $str['store_name']; ?></h5>
                            <p class="card-text"><?= $str['address']; ?></p>
                            <a href="" class="btn btn-default btn-sm" data-toggle="modal" data-target="#editStrLocation<?= $str['id_location']; ?>"> <i class="fas fa-edit"></i> Edit </a>
                            <?php if ($str['status_location'] == 1) : ?>
                                <a href="<?= base_url('admin/deactivateStr/') . $str['id_location']; ?>" class="btn btn-sm deactStr-button"> <i class=" fas fa-times"></i> Deactivate </a>
                            <?php else : ?>
                                <a href="<?= base_url('admin/activateStr/') . $str['id_location']; ?>" class="btn actvt-button"> <i class="fas fa-check"></i>Activate</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
</div>

<!-- Start Modal Add Store Location -->
<div class="modal fade" id="addStrLocation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Books&Beyond Store Location</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="<?= base_url('Admin/addStrLocation'); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="strCode"> Store Code</label>
                        <input type="text" class="form-control" id="strCode" name="strCode" placeholder="Store Code" required>
                    </div>
                    <div class="form-group">
                        <label for="strName">Store Name</label>
                        <input type="text" class="form-control" id="strName" name="strName" placeholder="Store Name" required>
                    </div>
                    <div class="form-group">
                        <label for="address"> Address </label>
                        <input type="text" class="form-control" id="address" name="address" placeholder="Address Store" required>
                    </div>
                    <div class="form-group">
                        <label for="loc">Handphone</label>
                        <input type="text" class="form-control" id="hp" name="handphone" placeholder="Handphone Store">
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
<!-- End Modal Add Store Location -->


<!-- Start Edit Store Location -->
<?php foreach ($strLocations as $str) :
    $id = $str['id_location'];
    $strCode = $str['store_code'];
    $strName = $str['store_name'];
    $address = $str['address'];
    $handphone = $str['handphone'];

?>
    <div class="modal fade" id="editStrLocation<?= $id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Books&Beyond Store Location</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="<?= base_url('Admin/editStrLocation/') . $id; ?>">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="strCode"> Store Code</label>
                            <input type="text" class="form-control" id="strCode" name="strCode" placeholder="Store Code" value="<?= $strCode; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="strName">Store Name</label>
                            <input type="text" class="form-control" id="strName" name="strName" placeholder="Store Name" value="<?= $strName; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="address"> Address </label>
                            <input type="text" class="form-control" id="address" name="address" placeholder="Address Store" value="<?= $address; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="loc">Handphone</label>
                            <input type="text" class="form-control" id="hp" name="handphone" placeholder="Handphone Store" value="<?= $handphone; ?>" required>
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
<!-- End Modal Edit Store Location -->