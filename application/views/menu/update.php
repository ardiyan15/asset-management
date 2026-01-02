<div class="card rounded p-3 m-4">
    <div class="d-flex justify-content-between">
        <h1 class="h3 text-gray-800"><?= $title; ?></h1>
        <div class="text-right">
            <img class="mr-2" src="<?= base_url('assets/img/logo_raharja.png') ?>" width="50">
            <img src="<?= base_url('assets/img/kampus_merdeka.png') ?>" width="50">
        </div>
    </div>
    <div class="row">
    <div class="col-md-8">
        <form action="<?= base_url('asset/edit_asset') ?>" method="POST">
            <input type="hidden" id="id" name="id" value="<?= $asset['id_asset'] ?>">
            <div class="form-group">
                <label for="nama" class="font-weight-bold">Nama</label>
                <input type="text" class="form-control" name="nama" id="nama" value="<?= $asset['asset_name'] ?>">
            </div>
            <div class="form-group">
                <label for="merk" class="font-weight-bold">Merk</label>
                <input type="text" class="form-control" name="merk" id="merk" value="<?= $asset['merk'] ?>">
            </div>
            <div class="form-group">
                <label for="seri" class="font-weight-bold">Prefix Nomor Seri</label>
                <input type="text" class="form-control" name="seri" id="seri" value="<?= $serial_number ?>" maxlength="4">
            </div>
            <button type="submit" class="btn btn-success btn-sm rounded">Simpan</button>
            <a href="<?= base_url('asset') ?>" class="btn btn-danger btn-sm rounded">Batal</a>
        </form>
    </div>
</div>