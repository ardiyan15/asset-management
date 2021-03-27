<div class="container-fluid">
    <!-- Untuk Memunculkan pop up sweetalert -->
    <div class="flash-data" data-flashdata="<?= $this->session->flashdata('message'); ?>"></div>
    <div class="container">
        <h3> <?= $title; ?> </h3>
        <a href="" class="btn btn-success btn-sm" data-toggle="modal" data-target="#addStrLocation"> <i class="fas fa-plus mr-1"></i> Tambah Lantai Baru </a>
            <div class="row row-cols-4">
                <?php foreach ($floors as $floor) : ?>
                    <div class="col-sm-4 mt-3"> 
                        <a href="<?= base_url('room/'). $floor['id'] ?>">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title"><?= $floor['name']; ?></h5>
                                    <a href="<?= base_url('floors/edit/').$floor['id'] ?>" class="btn btn-default btn-sm"> <i class="fas fa-edit"></i> Edit </a>
                                    <?php if ($floor['status'] == 1) : ?>
                                        <a href="<?= base_url('admin/deactivateStr/') . $floor['id']; ?>" class="btn btn-sm deactStr-button"> <i class=" fas fa-times"></i> List Room's </a>
                                    <?php else : ?>
                                        <a href="<?= base_url('admin/activateStr/') . $floor['id']; ?>" class="btn actvt-button"> <i class="fas fa-check"></i>Activate</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

<!-- Start Modal Add Store Location -->
<div class="modal fade" id="addStrLocation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Lokasi Bangunan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="<?= base_url('Floors/store'); ?>">
            <input type="hidden" name="building_id" value="<?= $building_id ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nama"> Nama </label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Nama Bangunan" required>
                    </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-success rounded"><i class="fas fa-plus mr-1"></i>Tambah</button>
                    <button type="button" class="btn btn-sm btn-secondary rounded" data-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End Modal Add Store Location -->
