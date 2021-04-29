<div class="container-fluid">

     <!-- Untuk Memunculkan pop up sweetalert -->
    <div class="flash-data" data-flashdata="<?= $this->session->flashdata('message'); ?>"></div>

    <div class="container">
        <h3> <?= $title; ?> : <b> <?= $floor['name'] ?> </b></h3>
        <a href="<?= base_url('floors/').$floor['building_id'] ?>" class="btn btn-sm btn-secondary">Kembali</a>
        <?php if($role_id == '1'): ?>
            <a href="" class="btn btn-success btn-sm" data-toggle="modal" data-target="#addStrLocation">
                <i class="fas fa-plus mr-1"></i> Tambah Ruangan Baru
            </a>
        <?php endif; ?>
        <div class="row row-cols-4">
            <?php foreach ($rooms as $room) : ?>
                <div class="col-sm-4 mt-3">
                    <div class="card">
                        <div class="card-body">
                            <a href="<?= base_url('asset/location/'). $room['id'] ?>">
                                <h5 class="card-title d-inline mr-2"><?= $room['name']; ?></h5>
                            </a>
                            <label style="font-size: 12px;">( Jumlah Aset: </label> <label class="font-weight-bold" style="font-size: 12px;"><?= $room['total'] ?></label> )
                            <p><?= $room['description'] ?></p>
                            <a href="<?= base_url('room/edit/'). $room['id'] ?>" class="btn btn-default btn-sm"> <i class="fas fa-edit"></i> Edit </a>
                            <!-- <?php if ($room['status'] == 1) : ?>
                                <a href="<?= base_url('admin/deactivateStr/') . $room['id']; ?>" class="btn btn-sm deactStr-button"> <i class=" fas fa-times"></i> List Room's </a>
                            <?php else : ?>
                                <a href="<?= base_url('admin/activateStr/') . $room['id']; ?>" class="btn actvt-button"> <i class="fas fa-check"></i>Activate</a>
                            <?php endif; ?> -->
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>


<div class="modal fade" id="addStrLocation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Lokasi Ruangan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="<?= base_url('Room/store'); ?>">
            <input type="hidden" name="floor_id" value="<?= $floor_id ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nama"> Kode Ruangan </label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Kode Ruangan" required>
                    </div>
                    <div class="form-group">
                        <label for="description"> Keterangan </label>
                        <input type="text" class="form-control" id="name" name="description" placeholder="Keterangan" required>
                    </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-success rounded"><i class="fas fa-plus mr-1"></i>Tambah</button>
                    <button type="button" class="btn btn-sm btn-secondary rounded" data-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>