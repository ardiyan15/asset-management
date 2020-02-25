<div class="container-fluid" style="margin-top: 5%;">
    <!-- Page Heading -->

    <div class="row">
        <div class="col-sm">
            <h2> User's List </h2>
        </div>
        <div class="col-md" style="margin-right: 18%;">
            <form action="<?= base_url('admin/list_user'); ?>" method="post">
                <div class="input-group">
                    <input type="text" name="keyword" class="form-control input-sm" placeholder="search user's" aria-label="Recipient's username" aria-describedby="basic-addon2">
                    <button type="submit" class="ml-2 btn btn-primary btn-sm"> <i class="fas fa-search"></i> Search </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Div untuk menampilkan pop up sweetalert -->
    <div class="flash-data" data-flashdata="<?= $this->session->flashdata('message'); ?>"></div>

    <div class="row">
        <div class="col-sm-10">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th class="center" scope="col"> User Code </th>
                        <th class="text-center" scope="col"> Fullname </th>
                        <th class="text-center" scope="col"> Email </th>
                        <th scope="col"> Status </th>
                        <th class="text-center" scope="col"> Action </th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = $this->uri->segment(3) + 1; ?>
                    <?php foreach ($all_users as $user) : ?>
                        <tr>
                            <th> <?= $i ?></th>
                            <td> <?= $user['user_code']; ?></td>
                            <td> <?= $user['fullname']; ?></td>
                            <td> <?= $user['email']; ?></td>
                            <?php if ($user['is_active'] == 1) : ?>
                                <td> Active </td>
                            <?php else : ?>
                                <td> Inactive </td>
                            <?php endif; ?>
                            <td>
                                <?php if ($user['is_active'] == 1) : ?>
                                    <a class="btn btn-info btn-sm deactivate-user" href="<?= base_url('admin/deactivated/') . $user['id_user']; ?>"> <i class="fas fa-times"></i> Deactivate </a>
                                <?php else : ?>
                                    <a class="btn btn-success btn-sm activate-user" href="<?= base_url('admin/activated/') . $user['id_user']; ?>"> <i class="fas fa-check"></i> Activate </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php $i++; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?= $pagination; ?>
        </div>
    </div>
</div>
<!-- /.container-fluid -->
</div>
<!-- End of Main Content -->