<div class="container-fluid">
    <!-- Page Heading -->

    <div class="row">
        <div class="col-sm">
            <h2> Daftar Pengguna </h2>
            <a href="<?= base_url('user/create') ?>" class="btn btn-success btn-sm mb-3">
                <i class="fas fa-plus mr-1"></i> Tambah User Baru
            </a>
        </div>
    </div>

    <!-- Div untuk menampilkan pop up sweetalert -->
    <div class="flash-data" data-flashdata="<?= $this->session->flashdata('message'); ?>"></div>

    <div class="row mt-3">
        <div class="col-sm">
            <table class="table table-hover" id="table-asset">
                <thead>
                    <tr>
                        <th class="text-center"> No </th>
                        <th class="text-center"> Username </th>
                        <th class="text-center"> Bangunan </th>
                        <th class="text-center"> Status </th>
                        <th class="text-center"> Aksi </th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = $this->uri->segment(3) + 1; ?>
                    <?php foreach ($all_users as $user) : ?>
                        <tr>
                            <th class="text-center"> <?= $i ?></th>
                            <td class="text-center"> <?= $user['username']; ?></td>
                            <td class="text-center"> <?= $user['name']; ?></td>
                            <?php if ($user['is_active'] == 1) : ?>
                                <td class="text-center"> Aktif </td>
                            <?php else : ?>
                                <td class="text-center"> Tidak Aktif </td>
                            <?php endif; ?>
                            <td class="text-center">
                                <?php if ($user['is_active'] == 1) : ?>
                                    <a class="btn btn-danger btn-sm deactivate-user" href="<?= base_url('admin/deactivated/') . $user['id_user']; ?>"> <i class="fas fa-times"></i> Non-aktifkan </a>
                                <?php else : ?>
                                    <a class="btn btn-success btn-sm activate-user" href="<?= base_url('admin/activated/') . $user['id_user']; ?>"> <i class="fas fa-check"></i> Aktifkan </a>
                                <?php endif; ?>
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

<div class="modal fade" id="addUser" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="<?= base_url('Asset/add_asset'); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Name" required>
                    </div>
                    <div class="form-group">
                        <label for="code">User Code</label>
                        <input type="text" class="form-control" id="code" name="code" placeholder="code" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" class="form-control" id="email" name="email" placeholder="email" required>
                        <?= form_error('loc', '<small class="text-danger pl-3">', '</small>'); ?>
                    </div>
                    <div class="form-group">
                        <label for="loc">Lokasi</label>
                        <select class="form-control" id="loc" name="loc">
                            <option value=""> -- Pilih Lokasi -- </option>
                            <?php foreach ($rooms as $room) : ?>
                                <option value="<?= $room['id']; ?>"> <?= $room['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('loc', '<small class="text-danger pl-3">', '</small>'); ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Add</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>