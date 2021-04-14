<div class="container">
    <div class="row">
        <div class="col-md-6">
            <form method="POST" action="<?= base_url('category/update') ?>">
                <input type="hidden" name="id" value="<?= $category['id'] ?>">
                <div class="form-group">
                    <label for="code">Kode Kategori</label>
                    <input type="text" name="code" class="form-control" value="<?= $category['code'] ?>"/>
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1">Namm Kategori</label>
                    <input type="text" name="name" class="form-control" value="<?= $category['name'] ?>">
                </div>
                <button type="submit" class="btn btn-success btn-sm">Simpan</button>
                <a href="<?= base_url('category') ?>" class="btn btn-danger btn-sm">Batal</a>
            </form>
        </div>
    </div>
</div>