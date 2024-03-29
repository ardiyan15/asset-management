<div class="container">
    <div class="row">
        <div class="col-md-6">
            <form method="POST" action="<?= base_url("floors/update/") . $floor['building_id'] ?>">
                <input type="hidden" name="id" value="<?= $floor['id'] ?>">
                <input type="hidden" name="building_id" value="<?= $floor['building_id'] ?>">
                <div class="form-group">
                    <label>Nama Lantai</label>
                    <input type="text" name="name" class="form-control" value="<?= $floor['name'] ?>">
                </div>
                <button type="submit" class="btn btn-success btn-sm">Simpan</button>
                <a href="<?= base_url('floors/') . $floor['building_id'] ?>" class="btn btn-danger btn-sm">Batal</a>
            </form>
        </div>
        <div class="text-right col-md-6">
            <img class="mr-2" src="<?= base_url('assets/img/logo_raharja.png') ?>" width="50">
            <img src="<?= base_url('assets/img/kampus_merdeka.png') ?>" width="50">
        </div>
    </div>
</div>