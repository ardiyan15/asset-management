<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <form action="<?= base_url('buildings/update') ?>" method="POST">
                <input type="hidden" name="id" value="<?= $result['id'] ?>">
                <div class="form-group">
                    <label for="name">Nama Bangunan</label>
                    <input type="text" name="name" class="form-control" value="<?= $result['name'] ?>">
                </div>
                <button type="submit" class="btn btn-success btn-sm rounded">Update</button>
                <a href="<?= base_url('buildings') ?>" class="btn btn-danger btn-sm rounded">Batal</a>
            </form>
        </div>
        <div class="text-right col-md-6">
            <img class="mr-2" src="<?= base_url('assets/img/logo_raharja.png') ?>" width="50">
            <img src="<?= base_url('assets/img/kampus_merdeka.png') ?>" width="50">
        </div>
    </div>
</div>