<?php
/**
 * Form tambah & ubah user (admin). $edit = data user yang diubah, null = tambah baru.
 * Nilai diambil dari POST kalau form gagal validasi, selain itu dari $edit.
 */
$is_edit = !empty($edit);
$val = function ($field, $default = '') use ($edit) {
    $posted = get_instance()->input->post($field);
    if ($posted !== null) {
        return (string) $posted;
    }
    return (string) ($edit[$field] ?? $default);
};
$role     = $val('role', $edit['role_id'] ?? '');
$building = $val('building', $edit['building_id'] ?? '');
?>
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <h1 class="h3 mb-3 text-gray-800"><?= $title; ?></h1>
        </div>
        <div class="text-right col-md-6">
            <img class="mr-2" src="<?= base_url('assets/img/logo_raharja.png') ?>" width="50">
            <img src="<?= base_url('assets/img/kampus_merdeka.png') ?>" width="50">
        </div>
    </div>
    <?php if (!empty($error)) : ?>
        <div class="alert alert-danger" role="alert"><?= $error ?></div>
    <?php endif ?>
    <form action="<?= $is_edit ? base_url('user/update/' . $token) : base_url('user/store') ?>" method="POST" enctype="multipart/form-data">
        <?php if ($is_edit) : ?>
            <div class="d-flex align-items-center mb-3">
                <img class="img-profile rounded-circle mr-3" style="width: 56px; height: 56px; object-fit: cover;" src="<?= base_url('assets/img/profile/') . html_escape($edit['image']); ?>">
                <div class="small text-gray-600">
                    Dibuat <?= $edit['created'] ? date('d-m-Y', strtotime($edit['created'])) : '-'; ?>
                    &middot; <?= $edit['is_active'] ? 'Aktif' : 'Nonaktif'; ?>
                </div>
            </div>
        <?php endif ?>
        <div class="form-group">
            <label>Username</label>
            <input type="text" class="form-control" name="username" placeholder="Username" required value="<?= html_escape($val('username')) ?>">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" class="form-control" name="email" placeholder="nama@contoh.com" value="<?= html_escape($val('email')) ?>">
            <small class="form-text text-muted">Opsional. Dipakai untuk notifikasi kiriman masuk dan pengingat maintenance / peminjaman.</small>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" class="form-control" name="password" autocomplete="new-password" placeholder="<?= $is_edit ? 'Kosongkan kalau tidak diganti' : 'Password' ?>">
        </div>
        <div class="form-group">
            <label>Role</label>
            <?php if ($is_edit && !empty($is_self)) : ?>
                <input type="hidden" name="role" value="<?= html_escape($role) ?>">
                <input type="text" class="form-control" value="<?= $role === '1' ? 'Admin' : 'User' ?>" disabled>
                <small class="form-text text-muted">Role akun sendiri tidak bisa diubah.</small>
            <?php else : ?>
                <select name="role" id="user-role" class="custom-select">
                    <option value="" <?= $role === '' ? 'selected' : '' ?>>-- Pilih Role --</option>
                    <?php if ($is_edit) : ?>
                        <option value="1" <?= $role === '1' ? 'selected' : '' ?>>Admin</option>
                    <?php endif ?>
                    <option value="2" <?= $role === '2' ? 'selected' : '' ?>>User</option>
                </select>
            <?php endif ?>
        </div>
        <div class="form-group" id="user-building-group">
            <label>Building</label>
            <select name="building" id="user-building" class="custom-select">
                <option value="" <?= $building === '' || $building === '0' ? 'selected' : '' ?>>-- Pilih Gedung --</option>
                <?php foreach ($buildings as $b) : ?>
                    <option value="<?= $b['id'] ?>" <?= $building === (string) $b['id'] ? 'selected' : '' ?>><?= html_escape($b['name']) ?></option>
                <?php endforeach ?>
            </select>
            <small class="form-text text-muted" id="user-building-admin-note" style="display: none;">Admin bisa mengakses semua gedung, gedung tidak perlu dipilih.</small>
        </div>
        <div class="form-group">
            <label class="d-block"><?= $is_edit ? 'Ganti foto (opsional)' : 'Foto (opsional)' ?></label>
            <div class="custom-file col-md-4">
                <input type="file" class="custom-file-input" id="image" name="image" accept="image/*">
                <label class="custom-file-label" for="image">Choose file</label>
            </div>
        </div>
        <button type="submit" class="btn btn-success btn-sm"><?= $is_edit ? 'Simpan Perubahan' : 'Submit' ?></button>
        <a href="<?= base_url('admin/list_user') ?>" class="btn btn-danger btn-sm">Batal</a>
    </form>
</div>
<script>
    // Admin tidak terikat gedung: sembunyikan pilihan gedung kalau role Admin
    document.addEventListener('DOMContentLoaded', function () {
        var role = document.getElementById('user-role');
        var current = role ? role.value : '<?= $role === '1' ? '1' : '' ?>';
        function sync(value) {
            var isAdmin = value === '1';
            document.getElementById('user-building').style.display = isAdmin ? 'none' : '';
            document.getElementById('user-building-admin-note').style.display = isAdmin ? '' : 'none';
        }
        sync(current);
        if (role) {
            role.addEventListener('change', function () { sync(role.value); });
        }
    });
</script>
