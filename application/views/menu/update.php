<div class="card rounded p-3 m-4">
    <div class="flash-data" data-flashdata="<?= $this->session->flashdata('message'); ?>"></div>
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
                <input type="text" class="form-control" name="nama" id="nama" value="<?= html_escape($asset['asset_name']) ?>">
            </div>
            <div class="form-group">
                <label for="merk" class="font-weight-bold">Merk</label>
                <input type="text" class="form-control" name="merk" id="merk" value="<?= html_escape($asset['merk']) ?>">
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="seri" class="font-weight-bold">Prefix Nomor Seri</label>
                    <input type="text" class="form-control" name="seri" id="seri" value="<?= html_escape($serial_number) ?>" maxlength="4">
                </div>
                <div class="form-group col-md-6">
                    <label for="category_id" class="font-weight-bold">Kategori</label>
                    <select class="form-control" name="category_id" id="category_id">
                        <option value="">-- Tanpa kategori --</option>
                        <?php foreach ($categories as $category) : ?>
                            <option value="<?= $category['id']; ?>" <?= (int) $asset['category_id'] === (int) $category['id'] ? 'selected' : ''; ?>>
                                <?= html_escape($category['code'] . ' - ' . $category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="form-text text-muted">Menentukan masa manfaat untuk penyusutan.</small>
                </div>
            </div>

            <h2 class="h6 font-weight-bold text-gray-800 mt-2 mb-3 border-top pt-3">Data Pembelian</h2>
            <?php $this->load->view('menu/_purchase_fields', [
                'purchase'        => $asset,
                'warranty_months' => $warranty_months,
            ]); ?>

            <button type="submit" class="btn btn-success btn-sm rounded">Simpan</button>
            <a href="<?= base_url('asset') ?>" class="btn btn-danger btn-sm rounded">Batal</a>
        </form>
    </div>
    <?php $depr = $depreciation; ?>
    <?php if ($depr) : ?>
        <div class="col-md-4">
            <div class="card border-left-primary mt-4">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Nilai buku hari ini</div>
                    <div class="h4 font-weight-bold text-gray-800 mb-2"><?= rupiah($depr['book_value']); ?></div>
                    <?php if ($depr['configured']) : ?>
                        <div class="progress progress-sm mb-2">
                            <div class="progress-bar bg-primary" style="width: <?= $depr['percent_depreciated']; ?>%"></div>
                        </div>
                        <div class="small text-muted">
                            Umur <?= asset_age_label($depr['age_months']); ?> dari <?= $depr['life_months'] / 12; ?> tahun ·
                            susut <?= rupiah($depr['monthly']); ?>/bulan ·
                            akumulasi <?= rupiah($depr['accumulated']); ?>
                        </div>
                    <?php else : ?>
                        <div class="small text-muted">Masa manfaat kategori belum diatur, aset tidak disusutkan.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <div class="col-12 mt-4">
        <h2 class="h6 font-weight-bold text-gray-800 border-top pt-3 mb-3">Foto Aset <span class="text-muted font-weight-normal">(<?= count($photos); ?>)</span></h2>
        <?php $this->load->view('photos/_gallery', [
            'asset'    => $asset,
            'photos'   => $photos,
            'can_edit' => true,
            'back'     => 'asset/update/' . (int) $asset['id_asset'],
        ]); ?>
    </div>
</div>
