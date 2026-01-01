<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <h2> Daftar Pengguna </h2>
            <a href="<?= base_url('user/create') ?>" class="btn btn-success btn-sm mb-3">
                <i class="fas fa-plus mr-1"></i> Tambah User Baru
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
            <table class="table table-hover table-bordered" id="table-asset">
                <thead>
                    <tr>
                        <th class="text-center"> No </th>
                        <th class="text-center"> Username </th>
                        <th class="text-center"> Bangunan </th>
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
                            <td class="text-center">
                                <a href="javascript:;"
                                    data-id="<?= $user['id_user'] ?>"
                                    data-username="<?= $user['username'] ?>"
                                    data-building="<?= $user['building_id'] ?>"
                                    class="btn btn-info btn-sm rounded btn-edit-user mr-1"
                                    data-toggle="modal" data-target="#editUserModal">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a class="btn btn-danger btn-sm rounded activate-user" href="<?= base_url('admin/deleteUser/') . $user['id_user']; ?>"> <i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        <?php $i++; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
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
                            <?php foreach ($buildings as $building) : ?>
                                <option value="<?= $building['id']; ?>"> <?= $building['name'] ?></option>
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

<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserLabel">Ubah User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="<?= base_url('admin/edit_user'); ?>">
                <div class="modal-body">
                    <input type="hidden" name="id_user" id="id_user"> 
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep current password">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>