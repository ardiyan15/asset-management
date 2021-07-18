<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->

    <div class="row">
        <div class="col-sm">
            <div class="row">
                <div class="col-sm">
                    <h1 class="h3 text-gray-800"><?= $title; ?></h1>
                    <?php if ($user['role_id'] == '1') : ?>
                        <button type="button" class="btn btn-success btn-sm mb-3" data-toggle="modal" data-target="#addAsset">
                            <i class="fas fa-plus mr-1"></i> Tambah Asset Baru
                        </button>
                    <?php endif; ?>
                </div>
                <div class="col-sm">

                    <form action="<?= base_url('asset'); ?>" method="post">
                        <div class="input-group">
                            <input type="text" name="keyword" class="form-control" placeholder="Filter Aset">
                            <button type="submit" class="ml-2 btn btn-success btn-sm"> <i class="fas fa-search"></i> Filter </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Untuk Menampilkan pop up sweetalert -->
            <div class="flash-data" data-flashdata="<?= $this->session->flashdata('message'); ?>"></div>

            <div class="table-responsive mt-4">
                <table class="table table-border table-sm table-hover" id="table-asset">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Nama</th>
                            <th class="text-center">Merk</th>
                            <th class="text-center">Nomor Seri</th>
                            <th class="text-center">Lokasi Saat Ini</th>
                            <th class="text-center">Tahun Input</th>
                            <?php if($user['role_id'] == '1'): ?>
                                <th class="text-center">Aksi</th>
                            <?php endif ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($assets) : ?>
                            <?php $i = $this->uri->segment(3) + 1; ?>
                            <?php foreach ($assets as $asset) : ?>
                                <tr>
                                    <td scope="row" class="text-center"> <?= $i; ?></td>
                                    <td scope="row" class="text-center"><?= $asset['asset_name']; ?></td>
                                    <td scope="row" class="text-center"><?= $asset['merk']; ?></td>
                                    <td scope="row" class="text-center"><?= $asset['serial_number']; ?></td>
                                    <?php if($asset['placement_status'] == 1): ?>
                                        <td scope="row" class="text-center"><?= $asset['name']; ?></td>
                                    <?php else: ?>
                                        <td scope="row" class="text-center">Sedang Dipindahkan</td>
                                    <?php endif; ?>
                                    <td scope="row" class="text-center"><?= $asset['created']; ?></td>
                                    <?php if ($user['role_id'] == '1') : ?>
                                        <td scope="row" class="text-center">
                                            <a href="<?= base_url('asset/update/').$asset['id_asset'] ?>" class="btn btn-info btn-sm"> <i class="fas fa-edit"></i> Ubah </a>
                                            <a href="<?= base_url("asset/delete/") . $asset['id_asset']; ?>" class="btn btn-danger btn-sm delete-button"> <i class="fas fa-trash-alt"></i> Hapus </a>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                                <?php $i++; ?>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="5">
                                    <h4> <?= $error; ?> </h4>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->
</div>
<!-- End of Main Content -->

<!-- Start Modal Add Menu -->
<div class="modal fade" id="addAsset" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Aset</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="<?= base_url('Asset/add_asset'); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Nama</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Name" required>
                    </div>
                    <div class="form-group">
                        <label for="merk">Merk</label>
                        <input type="text" class="form-control" id="merk" name="merk" placeholder="Merk" required>
                    </div>
                    <div class="form-group">
                        <label for="category">Kategori</label>
                        <select class="form-control filter-room" id="category" name="category">
                            <option value=""> -- Pilih Kategori -- </option>
                            <?php foreach ($categories as $categorie) : ?>
                                <option value="<?= $categorie['code']; ?>"> <?= $categorie['name'] ?> </option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('loc', '<small class="text-danger pl-3">', '</small>'); ?>
                    </div>
                    <div class="form-group">
                        <label for="loc">Lokasi</label>
                        <select class="form-control filter-room" id="loc" name="loc">
                            <option value=""> -- Pilih Lokasi -- </option>
                            <?php foreach ($rooms as $room) : ?>
                                <option value="<?= $room['id']; ?>"> <?= $room['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('loc', '<small class="text-danger pl-3">', '</small>'); ?>
                    </div>
                    <div class="form-group">
                        <label for="name">Jumlah</label>
                        <input type="text" class="form-control" id="qty" name="qty" placeholder="Masukkan Jumlah" value="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                </div>
            </form>
        </div>
    </div>
</div>