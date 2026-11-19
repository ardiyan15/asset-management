<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

            <!-- Sidebar Toggle (Topbar) -->
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </button>

            <!-- Topbar Navbar -->
            <ul class="navbar-nav ml-auto">
                <?php
                // Lonceng kiriman masuk: angka dari sidebar (load->vars), daftar 5 transaksi terbaru
                $incoming_pending = $incoming_pending ?? 0;
                $incoming_list = [];
                if ($incoming_pending > 0) {
                    $ci_bell = &get_instance();
                    $ci_bell->load->library('encryption');
                    $ci_bell->load->helper('url_crypto');
                    $ci_bell->load->model('Transactions_model', 'IncomingBadge');
                    $incoming_list = $ci_bell->IncomingBadge->pending_incoming_summary($user['role_id'], $user['building_id'], 5);
                }
                ?>
                <li class="nav-item dropdown no-arrow mx-1">
                    <a class="nav-link dropdown-toggle" href="#" id="incomingDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Kiriman masuk">
                        <i class="fas fa-bell fa-fw"></i>
                        <?php if ($incoming_pending > 0) : ?>
                            <span class="badge badge-danger badge-counter"><?= $incoming_pending > 99 ? '99+' : $incoming_pending; ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="incomingDropdown">
                        <h6 class="dropdown-header">Kiriman masuk</h6>
                        <?php foreach ($incoming_list as $in) : ?>
                            <a class="dropdown-item d-flex align-items-center" href="<?= site_url('TI/detail/' . urlsafe_b64encode($ci_bell->encryption->encrypt($in['code']))); ?>">
                                <div class="mr-3">
                                    <div class="icon-circle bg-info">
                                        <i class="fas fa-truck-loading text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="small text-gray-500"><?= $in['created_at'] ? date('d-m-Y H:i', strtotime($in['created_at'])) : '-'; ?> &middot; <?= html_escape($in['code']); ?></div>
                                    <span class="font-weight-bold"><?= (int) $in['item_count']; ?> aset ke <?= html_escape($in['room_name']); ?><?= $user['role_id'] == '1' && $in['building_name'] ? ', ' . html_escape($in['building_name']) : ''; ?></span>
                                    <?php if ($in['sender']) : ?>
                                        <div class="small text-gray-600">dari <?= html_escape($in['sender']); ?></div>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                        <?php if (empty($incoming_list)) : ?>
                            <span class="dropdown-item text-center small text-gray-500">Tidak ada kiriman yang menunggu diterima</span>
                        <?php endif; ?>
                        <a class="dropdown-item text-center small text-gray-500" href="<?= base_url('TI'); ?>">Lihat semua transaksi masuk</a>
                    </div>
                </li>
                <div class="topbar-divider d-none d-sm-block"></div>
                <!-- Nav Item - User Information -->
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= $user['username']; ?></span>
                        <img class="img-profile rounded-circle" src="<?= base_url('assets/img/profile/') . $user['image']; ?>">
                    </a>
                    <!-- Dropdown - User Information -->
                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="<?= base_url('user'); ?>">
                            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                            Profil Saya
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?= base_url('auth/logout'); ?>" data-toggle="modal" data-target="#logoutModal">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                            Logout
                        </a>
                    </div>
                </li>
            </ul>
        </nav>
        <!-- End of Topbar -->