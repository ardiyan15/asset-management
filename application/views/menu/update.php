<div class="container">
    <div class="text-right col-md-12">
        <img class="mr-2" src="<?= base_url('assets/img/logo_raharja.png') ?>" width="50">
        <img src="<?= base_url('assets/img/kampus_merdeka.png') ?>" width="50">
    </div>
    <div class="row">
        <div class="col-md-8">
            <h1 class="h3 text-gray-800"><?= $title; ?></h1>
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
                <button type="submit" class="btn btn-success btn-sm">Simpan</button>
                <a href="<?= base_url('asset') ?>" class="btn btn-danger btn-sm">Batal</a>
            </form>
        </div>
    </div>
</div>