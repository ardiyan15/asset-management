<div class="container-fluid">

     <!-- Untuk Memunculkan pop up sweetalert -->
    <div class="flash-data" data-flashdata="<?= $this->session->flashdata('message'); ?>"></div>


    <h3> <?= $title; ?> </h3>
    <a href="" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addStrLocation"> <i class="fas fa-plus mr-1"></i> Add New Store </a>
    <div class="container">
        <div class="row row-cols-4">
            <?php foreach ($buildings as $building) : ?>
                <div class="col-sm-4 mt-3">
                    <a href="<?= base_url('admin/list_rooms/'). $building['id'] ?>">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?= $building['name']; ?></h5>
                                <a href="" class="btn btn-default btn-sm" data-toggle="modal" data-target="#editStrLocation<?= $building['id']; ?>"> <i class="fas fa-edit"></i> Edit </a>
                                <?php if ($building['status'] == 1) : ?>
                                    <a href="<?= base_url('admin/deactivateStr/') . $building['id']; ?>" class="btn btn-sm deactStr-button"> <i class=" fas fa-times"></i> List Room's </a>
                                <?php else : ?>
                                    <a href="<?= base_url('admin/activateStr/') . $building['id']; ?>" class="btn actvt-button"> <i class="fas fa-check"></i>Activate</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
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
