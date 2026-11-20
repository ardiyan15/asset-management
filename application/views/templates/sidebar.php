<?php
// Angka badge menu (per gedung untuk user, semua gedung untuk admin)
$ci_side = &get_instance();

// Aset kiriman yang menunggu diterima. load->vars supaya lonceng di topbar
// memakai angka yang sama tanpa query ulang.
$ci_side->load->model('Transactions_model', 'IncomingBadge');
$incoming_pending = $ci_side->IncomingBadge->count_pending_incoming($user['role_id'], $user['building_id']);
$ci_side->load->vars(['incoming_pending' => $incoming_pending]);

// Peminjaman yang terlambat / harus kembali hari ini
$ci_side->load->model('Loan_model', 'LoanBadge');
$loan_attention = $ci_side->LoanBadge->count_attention($user['role_id'], $user['building_id']);

// Jadwal maintenance yang terlambat / masuk masa pengingat
$ci_side->load->model('Maintenance_model', 'MaintenanceBadge');
$maintenance_due = $ci_side->MaintenanceBadge->count_due($user['role_id'], $user['building_id']);

// Aset yang menunggu persetujuan penghapusan (khusus admin)
$disposal_pending = 0;
if ($user['role_id'] == '1') {
    $ci_side->load->model('Disposal_model', 'DisposalBadge');
    $disposal_pending = $ci_side->DisposalBadge->count_pending();
}
?>
<!-- Sidebar -->
<ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" aria-label="Menu utama">
    <script>
        // Kembalikan mode rel (sidebar diciutkan) yang terakhir dipilih, hanya di desktop
        try {
            if (window.innerWidth >= 768 && localStorage.getItem('simanis.sidebar') === 'rail') {
                document.getElementById('accordionSidebar').classList.add('toggled');
                document.body.classList.add('sidebar-toggled');
            }
        } catch (e) {}
    </script>

    <!-- Merek -->
    <a class="sidebar-brand d-flex align-items-center" href="<?= base_url('admin') ?>" aria-label="SIMANIS, ke dashboard">
        <div class="sidebar-brand-icon">
            <img src="<?= base_url('assets/img/logo_simanis.png'); ?>" alt="" width="26" height="26">
        </div>
        <div class="sidebar-brand-text">
            <span class="brand-name">SIMANIS</span>
            <span class="brand-sub">Manajemen Aset</span>
        </div>
    </a>

    <div class="sidebar-heading">Ringkasan</div>
    <li class="nav-item <?= is_active('admin'); ?>">
        <a class="nav-link" href="<?= base_url('admin') ?>" title="Dashboard">
            <i class="fas fa-fw fa-chart-pie"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <div class="sidebar-heading">Data Master</div>
    <li class="nav-item <?= is_active('asset'); ?>">
        <a class="nav-link" href="<?= base_url('asset') ?>" title="Data Aset">
            <i class="fas fa-fw fa-box"></i>
            <span>Data Aset</span>
        </a>
    </li>
    <li class="nav-item <?= is_active('buildings'); ?>">
        <a class="nav-link" href="<?= base_url('buildings') ?>" title="Gedung & Ruangan">
            <i class="fas fa-fw fa-building"></i>
            <span>Gedung &amp; Ruangan</span>
        </a>
    </li>
    <?php if ($user['role_id'] == '1') : ?>
        <li class="nav-item <?= is_active('admin', 'list_user'); ?>">
            <a class="nav-link" href="<?= base_url('admin/list_user') ?>" title="Data Pengguna">
                <i class="fas fa-fw fa-users"></i>
                <span>Data Pengguna</span>
            </a>
        </li>
        <li class="nav-item <?= is_active('category'); ?>">
            <a class="nav-link" href="<?= base_url('category') ?>" title="Data Kategori">
                <i class="fas fa-fw fa-tags"></i>
                <span>Data Kategori</span>
            </a>
        </li>
    <?php endif; ?>

    <div class="sidebar-heading">Transaksi</div>
    <li class="nav-item <?= is_active('transaction'); ?>">
        <a class="nav-link" href="<?= base_url('transaction') ?>" title="Pindahkan Aset">
            <i class="fas fa-fw fa-exchange-alt"></i>
            <span>Pindahkan Aset</span>
        </a>
    </li>
    <li class="nav-item <?= is_active('TO'); ?>">
        <a class="nav-link" href="<?= base_url('TO') ?>" title="Transaksi Keluar">
            <i class="fas fa-fw fa-long-arrow-alt-up"></i>
            <span>Transaksi Keluar</span>
        </a>
    </li>
    <li class="nav-item <?= is_active('TI') ?: is_active('TI', 'detail'); ?>">
        <a class="nav-link" href="<?= base_url('TI') ?>" title="Transaksi Masuk">
            <i class="fas fa-fw fa-long-arrow-alt-down"></i>
            <span>Transaksi Masuk</span>
            <?php if ($incoming_pending > 0) : ?>
                <span class="badge badge-info" title="Aset menunggu diterima"><?= $incoming_pending; ?></span>
            <?php endif; ?>
        </a>
    </li>
    <li class="nav-item <?= is_active('loan'); ?>">
        <a class="nav-link" href="<?= base_url('loan') ?>" title="Peminjaman Aset">
            <i class="fas fa-fw fa-hand-holding"></i>
            <span>Peminjaman Aset</span>
            <?php if ($loan_attention > 0) : ?>
                <span class="badge badge-danger" title="Terlambat / kembali hari ini"><?= $loan_attention; ?></span>
            <?php endif; ?>
        </a>
    </li>
    <li class="nav-item <?= is_active('History'); ?>">
        <a class="nav-link" href="<?= base_url('History') ?>" title="Riwayat Transaksi">
            <i class="fas fa-fw fa-history"></i>
            <span>Riwayat Transaksi</span>
        </a>
    </li>

    <div class="sidebar-heading">Audit</div>
    <li class="nav-item <?= is_active('opname') ?: is_active('opname', 'detail'); ?>">
        <a class="nav-link" href="<?= base_url('opname') ?>" title="Stock Opname">
            <i class="fas fa-fw fa-clipboard-check"></i>
            <span>Stock Opname</span>
        </a>
    </li>
    <li class="nav-item <?= is_active('maintenance') ?: is_active('maintenance', 'history'); ?>">
        <a class="nav-link" href="<?= base_url('maintenance') ?>" title="Maintenance">
            <i class="fas fa-fw fa-tools"></i>
            <span>Maintenance</span>
            <?php if ($maintenance_due > 0) : ?>
                <span class="badge badge-warning" title="Jadwal perlu dikerjakan"><?= $maintenance_due; ?></span>
            <?php endif; ?>
        </a>
    </li>
    <li class="nav-item <?= is_active('disposal') ?: (is_active('disposal', 'detail') ?: is_active('disposal', 'create')); ?>">
        <a class="nav-link" href="<?= base_url('disposal') ?>" title="Penghapusan Aset">
            <i class="fas fa-fw fa-recycle"></i>
            <span>Penghapusan Aset</span>
            <?php if ($disposal_pending > 0) : ?>
                <span class="badge badge-danger" title="Menunggu persetujuan"><?= $disposal_pending; ?></span>
            <?php endif; ?>
        </a>
    </li>
    <?php if ($user['role_id'] == '1') : ?>
        <li class="nav-item <?= is_active('activity'); ?>">
            <a class="nav-link" href="<?= base_url('activity') ?>" title="Audit Log">
                <i class="fas fa-fw fa-user-shield"></i>
                <span>Audit Log</span>
            </a>
        </li>
    <?php endif; ?>

    <div class="sidebar-heading">Laporan</div>
    <li class="nav-item <?= is_active('report'); ?>">
        <a class="nav-link" href="<?= base_url('report') ?>" title="Rekap Aset">
            <i class="fas fa-fw fa-chart-bar"></i>
            <span>Rekap Aset</span>
        </a>
    </li>
    <li class="nav-item <?= is_active('valuation'); ?>">
        <a class="nav-link" href="<?= base_url('valuation') ?>" title="Nilai Aset">
            <i class="fas fa-fw fa-coins"></i>
            <span>Nilai Aset</span>
        </a>
    </li>

    <div class="sidebar-heading">Akun</div>
    <li class="nav-item <?= is_active('user'); ?>">
        <a class="nav-link" href="<?= base_url('user') ?>" title="Profil Saya">
            <i class="fas fa-fw fa-user"></i>
            <span>Profil Saya</span>
        </a>
    </li>
    <li class="nav-item <?= is_active('user', 'editprofile'); ?>">
        <a class="nav-link" href="<?= base_url('user/editprofile') ?>" title="Ubah Profil">
            <i class="fas fa-fw fa-user-edit"></i>
            <span>Ubah Profil</span>
        </a>
    </li>
    <li class="nav-item <?= is_active('user', 'changepassword'); ?>">
        <a class="nav-link" href="<?= base_url('user/changepassword') ?>" title="Ganti Password">
            <i class="fas fa-fw fa-key"></i>
            <span>Ganti Password</span>
        </a>
    </li>
    <li class="nav-item nav-item-logout">
        <a class="nav-link" href="<?= base_url('auth/logout'); ?>" data-toggle="modal" data-target="#logoutModal" title="Keluar">
            <i class="fas fa-fw fa-sign-out-alt"></i>
            <span>Keluar</span>
        </a>
    </li>

    <!-- Ciutkan sidebar (desktop) -->
    <div class="text-center d-none d-md-block">
        <button class="border-0" id="sidebarToggle" type="button" aria-label="Ciutkan atau lebarkan menu"></button>
    </div>

</ul>
<div class="sidebar-backdrop" id="sidebarBackdrop" aria-hidden="true"></div>
<!-- End of Sidebar -->
