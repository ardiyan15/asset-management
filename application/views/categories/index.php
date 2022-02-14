<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <h2> Daftar Kategori </h2>
            <a href="" data-toggle="modal" data-target="#addCategories" class="btn btn-success btn-sm mb-3">
                <i class="fas fa-plus mr-1"></i> Tambah Kategori Baru
            </a>
        </div>
        <div class="text-right col-md-6">
            <img class="mr-2" src="<?= base_url('assets/img/logo_raharja.png') ?>" width="50">
            <img src="<?= base_url('assets/img/kampus_merdeka.png') ?>" width="50">
        </div>
    </div>

    <!-- Div untuk menampilkan pop up sweetalert -->
    <div class="flash-data" data-flashdata="<?= $this->session->flashdata('message'); ?>"></div>
    <div class="row mt-3">
        <div class="col-sm">
            <table class="table table-hover" id="table-asset">
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Kode Kategori</th>
                        <th class="text-center">Nama Kategori</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = $this->uri->segment(3) + 1; ?>
                    <?php foreach ($categories as $category) : ?>
                        <tr>
                            <th class="text-center"> <?= $i ?></th>
                            <td class="text-center"> <?= $category['code']; ?></td>
                            <td class="text-center"> <?= $category['name']; ?></td>
                            <?php if ($category['status'] == 1) : ?>
                                <td class="text-center"> Aktif </td>
                            <?php else : ?>
                                <td class="text-center"> Tidak Aktif </td>
                            <?php endif; ?>
                            <td class="text-center">
                                <a href="<?= base_url('category/edit/') . $category['id'] ?>" class="btn btn-info btn-sm"><i class="fas fa-edit"></i> Ubah</a>
                                <a href="<?= base_url("category/delete/") . $category['id']; ?>" class="btn btn-danger btn-sm delete-button"> <i class="fas fa-trash-alt"></i> Hapus </a>
                            </td>
                        </tr>
                        <?php $i++; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- /.container-fluid -->
</div>
<!-- End of Main Content -->

<div class="modal fade" id="addCategories" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Kategori</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="<?= base_url('category/store'); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Kode Kategori</label>
                        <input type="text" class="form-control" id="code" name="code" placeholder="Kode Kategori" required>
                    </div>
                    <div class="form-group">
                        <label for="code">Nama Kategori</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Nama Kategori" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-sm">Simpan</button>
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>