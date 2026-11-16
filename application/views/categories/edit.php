<div class="container">
    <div class="flash-data" data-flashdata="<?= $this->session->flashdata('message'); ?>"></div>
    <div class="row">
        <div class="col-md-6">
            <form method="POST" action="<?= base_url('category/update') ?>">
                <input type="hidden" name="id" value="<?= $category['id'] ?>">
                <div class="form-group">
                    <label for="code">Kode Kategori</label>
                    <input type="text" name="code" class="form-control" value="<?= $category['code'] ?>" />
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1">Nama Kategori</label>
                    <input type="text" name="name" class="form-control" value="<?= $category['name'] ?>">
                </div>
                <div class="form-row">
                    <div class="form-group col-6">
                        <label for="useful_life_years">Masa Manfaat (tahun)</label>
                        <input type="number" class="form-control" id="useful_life_years" name="useful_life_years" min="1" max="50" step="1" value="<?= html_escape($category['useful_life_years'] ?? ''); ?>" placeholder="mis. 4">
                        <small class="form-text text-muted">Kosongkan kalau tidak disusutkan.</small>
                    </div>
                    <div class="form-group col-6">
                        <label for="residual_percent">Nilai Sisa (%)</label>
                        <input type="number" class="form-control" id="residual_percent" name="residual_percent" min="0" max="100" step="0.01" value="<?= (float) $category['residual_percent']; ?>" placeholder="0">
                        <small class="form-text text-muted">Persen dari harga beli di akhir masa manfaat.</small>
                    </div>
                </div>
                <button type="submit" class="btn btn-success btn-sm">Simpan</button>
                <a href="<?= base_url('category') ?>" class="btn btn-danger btn-sm">Batal</a>
            </form>
        </div>
        <div class="text-right col-md-6">
            <img class="mr-2" src="<?= base_url('assets/img/logo_raharja.png') ?>" width="50">
            <img src="<?= base_url('assets/img/kampus_merdeka.png') ?>" width="50">
        </div>
    </div>
</div>