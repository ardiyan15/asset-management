<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= base_url('/admin') ?>">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-book"></i>
        </div>
        <div class="sidebar-brand-text mx-3" style="font-size: 10px;"> Asset Management </div>
    </a>


    <!-- Divider -->
    <hr class="sidebar-divider">
    <div class="sidebar-heading">
        Data Master
    </div>
    
    <li class="nav-item <?= is_active('admin');  ?>">
        <a class="nav-link" href="<?= base_url('admin') ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span> Dashboard </span>
        </a>
    </li>
    <li class="nav-item <?= is_active('asset');  ?>">
        <a class="nav-link" href="<?= base_url('asset') ?>">
            <i class="fas fa-fw fa-box"></i>
            <span> Data Asset </span>
        </a>
    </li>
    <li class="nav-item <?= is_active('buildings'); ?>">
        <a class="nav-link" href="<?= base_url('buildings') ?>">
            <i class="fas fa-fw fa-building"></i>
            <span> Data Ruangan </span>
        </a>
    </li>
    <?php if($user['role_id'] == '1'): ?>
        <li class="nav-item <?= is_active('admin', 'list_user');  ?>">
            <a class="nav-link" href="<?= base_url('admin/list_user') ?>">
                <i class="fas fa-fw fa-users"></i>
                <span> Data Pengguna </span>
            </a>
        </li>    
        <li class="nav-item <?= is_active('category');  ?>">
            <a class="nav-link" href="<?= base_url('category') ?>">
                <i class="fas fa-fw fa-list"></i>
                <span> Data Kategori </span>
            </a>
        </li>
    <?php endif; ?>
    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Transaksi
    </div>
    <li class="nav-item <?= is_active('transaction');  ?>">
        <a class="nav-link" href="<?= base_url('transaction') ?>">
            <i class="fas fa-fw fa-exchange-alt"></i>
            <span> Transaksi Asset </span>
        </a>
    </li>
    <li class="nav-item <?= is_active('TO');  ?>">
        <a class="nav-link" href="<?= base_url('TO') ?>">
            <i class="fas fa-fw fa-minus"></i>
            <span> Transaksi Keluar </span>
        </a>
    </li>
    <li class="nav-item <?= is_active('TI');  ?>">
        <a class="nav-link" href="<?= base_url('TI') ?>">
            <i class="fas fa-fw fa-plus"></i>
            <span> Transaksi Masuk </span>
        </a>
    </li>
    <li class="nav-item <?= is_active('History');  ?>">
        <a class="nav-link" href="<?= base_url('History') ?>">
            <i class="fas fa-fw fa-history"></i>
            <span> Riwayat Transaksi </span>
        </a>
    </li>
    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Audit
    </div>
    <li class="nav-item <?= is_active('opname') ?: is_active('opname', 'detail'); ?>">
        <a class="nav-link" href="<?= base_url('opname') ?>">
            <i class="fas fa-fw fa-clipboard-check"></i>
            <span> Stock Opname </span>
        </a>
    </li>
    <?php
    // Badge jumlah aset yang menunggu persetujuan penghapusan (khusus admin)
    $disposal_pending = 0;
    if ($user['role_id'] == '1') {
        $ci = &get_instance();
        $ci->load->model('Disposal_model', 'DisposalBadge');
        $disposal_pending = $ci->DisposalBadge->count_pending();
    }
    ?>
    <li class="nav-item <?= is_active('disposal') ?: (is_active('disposal', 'detail') ?: is_active('disposal', 'create')); ?>">
        <a class="nav-link" href="<?= base_url('disposal') ?>">
            <i class="fas fa-fw fa-recycle"></i>
            <span> Penghapusan Aset </span>
            <?php if ($disposal_pending > 0) : ?>
                <span class="badge badge-danger badge-counter ml-1" style="position: static;"><?= $disposal_pending; ?></span>
            <?php endif; ?>
        </a>
    </li>
    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Laporan
    </div>
    <li class="nav-item <?= is_active('report'); ?>">
        <a class="nav-link" href="<?= base_url('report') ?>">
            <i class="fas fa-fw fa-chart-bar"></i>
            <span> Rekap Aset </span>
        </a>
    </li>
    <li class="nav-item <?= is_active('valuation'); ?>">
        <a class="nav-link" href="<?= base_url('valuation') ?>">
            <i class="fas fa-fw fa-coins"></i>
            <span> Nilai Aset </span>
        </a>
    </li>
    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        User
    </div>
    <li class="nav-item <?= is_active('user');  ?>">
        <a class="nav-link" href="<?= base_url('user') ?>">
            <i class="fas fa-fw fa-user"></i>
            <span> Profil Saya </span>
        </a>
    </li>
    <li class="nav-item <?= is_active('user', 'editprofile');  ?>">
        <a class="nav-link" href="<?= base_url('user/editprofile') ?>">
            <i class="fas fa-fw fa-user-edit"></i>
            <span> Ubah Profil </span>
        </a>
    </li>
    <li class="nav-item <?= is_active('user', 'changepassword');  ?>">
        <a class="nav-link" href="<?= base_url('user/changepassword') ?>">
            <i class="fas fa-fw fa-key"></i>
            <span> Ganti Password </span>
        </a>
    </li>
    <hr class="sidebar-divider">

     <li class="nav-item">
        <a class="nav-link" href="<?= base_url('auth/logout'); ?>">
            <i class="fas fa-fw fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </li>

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->
