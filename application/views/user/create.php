<div class="container">
    <form action="<?= base_url('user/store') ?>" method="POST">
        <div class="form-group">
            <label for="code">User Code</label>
            <input type="text" class="form-control" name="code" placeholder="User Code">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="text" class="form-control" name="email" placeholder="Email">
        </div>
        <div class="form-group">
            <label>Fullname</label>
            <input type="text" class="form-control" name="fullname" placeholder="Fullname">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" class="form-control" name="password" placeholder="Password">
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">Role</label>
            <select name="role" class="custom-select">
                <option selected>-- Pilih Role --</option>
                <option value="1">Admin</option>
                <option value="2">User</option>
            </select>
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">Building</label>
            <select name="building" class="custom-select">
                <option selected>-- Pilih Gedung --</option>
                <?php foreach($buildings as $building): ?>
                    <option value="<?= $building['id'] ?>"><?= $building['name'] ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Submit</button>
        <a href="<?= base_url('admin/list_user') ?>" class="btn btn-danger">Batal</a>
    </form>
</div>