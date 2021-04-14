<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

    <div class="row">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    Asset Keluar
                </div>
                <div class="card-body">
                    <h5 class="card-title">Riwayat Transaksi Keluar</h5>
                    <a href="<?= base_url('history/asset_out'); ?>" class="btn btn-primary btn-sm"> Detail </a>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    Asset Masuk
                </div>
                <div class="card-body">
                    <h5 class="card-title">Riwayat Transaksi Masuk</h5>
                    <a href="<?= base_url('history/asset_in'); ?>" class="btn btn-primary btn-sm">Detail</a>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- /.container-fluid -->
</div>
<!-- End of Main Content -->