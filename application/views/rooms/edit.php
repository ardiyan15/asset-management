<div class="container">
    <div class="row">
        <div class="col-md-5">
            <form method="POST" action="<?= base_url("room/update/") . $room['floor_id'] ?>">
                <input type="hidden" name="id" value="<?= $room['id'] ?>">
                <input type="hidden" name="floor_id" value="<?= $room['floor_id'] ?>">
                <div class="form-group">
                    <label>Nama Ruangan</label>
                    <input type="text" name="name" class="form-control" value="<?= $room['name'] ?>">
                </div>
                <button type="submit" class="btn btn-success btn-sm">Simpan</button>
                <a href="<?= base_url('room/'). $room['floor_id'] ?>" class="btn btn-danger btn-sm">Batal</a>
            </form>
        </div>
    </div>
</div>