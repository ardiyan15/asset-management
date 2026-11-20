<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content" tabindex="-1">
        <?php
        $ci_top = &get_instance();

        // Cakupan data user: admin = semua gedung, user = gedungnya sendiri
        $is_admin_top = $user['role_id'] == '1';
        $scope_label  = 'Semua gedung';
        if (!$is_admin_top && !empty($user['building_id'])) {
            $scope_row   = $ci_top->db->select('name')->get_where('buildings', ['id' => $user['building_id']])->row_array();
            $scope_label = $scope_row['name'] ?? 'Gedung tidak diketahui';
        }

        // Lonceng kiriman masuk: angka dari sidebar (load->vars), daftar 5 transaksi terbaru
        $incoming_pending = $incoming_pending ?? 0;
        $incoming_list = [];
        if ($incoming_pending > 0) {
            $ci_top->load->library('encryption');
            $ci_top->load->helper('url_crypto');
            $ci_top->load->model('Transactions_model', 'IncomingBadge');
            $incoming_list = $ci_top->IncomingBadge->pending_incoming_summary($user['role_id'], $user['building_id'], 5);
        }
        ?>

        <!-- Topbar -->
        <nav class="navbar navbar-expand topbar static-top" aria-label="Bilah atas">

            <!-- Buka menu (mobile) -->
            <button id="sidebarToggleTop" class="btn btn-link d-md-none" type="button" aria-label="Buka menu" aria-controls="accordionSidebar">
                <i class="fa fa-bars"></i>
            </button>

            <span class="topbar-scope d-none d-sm-inline-flex" title="Cakupan data yang Anda lihat">
                <i class="fas fa-building"></i>
                <?= html_escape($scope_label); ?>
                <span class="scope-role"><?= $is_admin_top ? 'Admin' : 'Pengguna'; ?></span>
            </span>

            <form class="topbar-search ml-sm-2" action="<?= base_url('asset'); ?>" method="get" role="search">
                <i class="fas fa-search" aria-hidden="true"></i>
                <label for="topbarSearch" class="sr-only">Cari aset</label>
                <input type="search" id="topbarSearch" name="q" placeholder="Cari nama aset atau serial…" autocomplete="off" value="<?= html_escape((string) $ci_top->input->get('q')); ?>">
                <kbd aria-hidden="true">/</kbd>
            </form>

            <!-- Topbar Navbar -->
            <ul class="navbar-nav ml-auto align-items-center">
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="incomingDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="Kiriman masuk<?= $incoming_pending > 0 ? ', ' . $incoming_pending . ' menunggu' : ''; ?>">
                        <i class="fas fa-bell fa-fw"></i>
                        <?php if ($incoming_pending > 0) : ?>
                            <span class="badge badge-danger badge-counter"><?= $incoming_pending > 99 ? '99+' : $incoming_pending; ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="dropdown-list dropdown-menu dropdown-menu-right animated--grow-in" aria-labelledby="incomingDropdown">
                        <h6 class="dropdown-header">Kiriman masuk</h6>
                        <?php foreach ($incoming_list as $in) : ?>
                            <a class="dropdown-item d-flex align-items-start" href="<?= site_url('TI/detail/' . urlsafe_b64encode($ci_top->encryption->encrypt($in['code']))); ?>">
                                <div class="mr-3">
                                    <div class="icon-circle">
                                        <i class="fas fa-truck-loading"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="font-weight-bold text-gray-800"><?= (int) $in['item_count']; ?> aset ke <?= html_escape($in['room_name']); ?><?= $is_admin_top && $in['building_name'] ? ', ' . html_escape($in['building_name']) : ''; ?></div>
                                    <div class="small text-muted">
                                        <span class="mono-code"><?= html_escape($in['code']); ?></span>
                                        · <?= $in['created_at'] ? date('d-m-Y H:i', strtotime($in['created_at'])) : '-'; ?>
                                    </div>
                                    <?php if ($in['sender']) : ?>
                                        <div class="small text-muted">dari <?= html_escape($in['sender']); ?></div>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                        <?php if (empty($incoming_list)) : ?>
                            <span class="dropdown-item text-center small text-muted py-3">Tidak ada kiriman yang menunggu diterima</span>
                        <?php endif; ?>
                        <a class="dropdown-item text-center small font-weight-bold text-primary" href="<?= base_url('TI'); ?>">Lihat semua transaksi masuk</a>
                    </div>
                </li>
                <div class="topbar-divider d-none d-sm-block"></div>
                <!-- Pengguna -->
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="Menu akun <?= html_escape($user['username']); ?>">
                        <span class="user-name d-none d-lg-flex">
                            <strong><?= html_escape($user['username']); ?></strong>
                            <span><?= $is_admin_top ? 'Administrator' : html_escape($scope_label); ?></span>
                        </span>
                        <img class="img-profile rounded-circle" src="<?= base_url('assets/img/profile/') . $user['image']; ?>" alt="">
                    </a>
                    <div class="dropdown-menu dropdown-menu-right animated--grow-in" aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="<?= base_url('user'); ?>">
                            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                            Profil Saya
                        </a>
                        <a class="dropdown-item" href="<?= base_url('user/changepassword'); ?>">
                            <i class="fas fa-key fa-sm fa-fw mr-2 text-gray-400"></i>
                            Ganti Password
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?= base_url('auth/logout'); ?>" data-toggle="modal" data-target="#logoutModal">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                            Keluar
                        </a>
                    </div>
                </li>
            </ul>
        </nav>
        <!-- End of Topbar -->
