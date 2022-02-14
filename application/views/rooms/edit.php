<div class="container">
    <div class="row">
        <div class="col-md-6">
            <form method="POST" action="<?= base_url("room/update/") . $room['floor_id'] ?>">
                <input type="hidden" name="id" value="<?= $room['id'] ?>">
                <input type="hidden" name="floor_id" value="<?= $room['floor_id'] ?>">
                <div class="form-group">
                    <label>Kode Ruangan</label>
                    <input type="text" name="name" class="form-control" value="<?= $room['name'] ?>">
                </div>
                <div class="form-group">
                    <label>Keterangan</label>
                    <input type="text" name="description" class="form-control" value="<?= $room['description'] ?>">
                </div>
                <button type="submit" class="btn btn-success btn-sm">Simpan</button>
                <a href="<?= base_url('room/') . $room['floor_id'] ?>" class="btn btn-danger btn-sm">Batal</a>
            </form>
        </div>
        <div class="text-right col-md-6">
            <img class="mr-2" src="<?= base_url('assets/img/logo_raharja.png') ?>" width="50">
            <img src="<?= base_url('assets/img/kampus_merdeka.png') ?>" width="50">
        </div>
    </div>
</div>