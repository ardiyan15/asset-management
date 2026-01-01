<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="font-weight-bold text-gray-800"><?= $title; ?></h2>
            <p class="text-gray-600">Welcome back, <?= $user['username']; ?>. Here's what's happening today.</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Aset</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $summaries["total_assets"] ?></div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle bg-primary-light text-primary">
                                <i class="fas fa-box fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Transaksi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $summaries['total_transactions']; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Pengguna</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $summaries['total_users']; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Ruangan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $summaries["total_rooms"] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Statistik Aset</h6>
                    
                    <form action="<?= base_url('admin/index'); ?>" method="POST" class="form-inline">
                         <select id="order" name="room_id" class="form-control form-control-sm mr-2 filter-room" onchange="this.form.submit()">
                            <option value="">Semua Ruangan</option>
                            <?php foreach ($rooms as $room) : ?>
                                <option value="<?= $room['id']; ?>" <?= ($room_id == $room["id"]) ? 'selected' : ''; ?>>
                                    <?= $room['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                         <input type="hidden" name="room_id" value="<?= $room_id ?>"> <!-- Ensure form submit works -->
                    </form>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 320px;">
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Doughnut Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Kategori Aset</h6>
                </div>
                <div class="card-body">
                     <div class="chart-pie pt-4 pb-2">
                        <canvas id="categoryChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> 5 Teratas
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                     <h6 class="m-0 font-weight-bold text-primary">Aktivitas Terkini</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                         <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Aset</th>
                                    <th>Serial Number</th>
                                    <th>Dari</th>
                                    <th>Ke</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($recent_transactions)): ?>
                                    <tr><td colspan="7" class="text-center">Belum ada transaksi</td></tr>
                                <?php else: ?>
                                    <?php foreach($recent_transactions as $trans): ?>
                                    <tr>
                                        <td>#<?= $trans['id_detail_process'] ?></td>
                                        <td><?= $trans['asset_name'] ?></td>
                                        <td><?= $trans['serial_number'] ?></td>
                                        <td><?= $trans['source'] ?></td>
                                        <td><?= $trans['destination'] ?></td>
                                        <td><?= date('d M Y, H:i', $trans['created_at']) ?></td> <!-- Assuming created_at exists -->
                                        <td>
                                            <?php if($trans['deleted'] == 0): ?>
                                                 <span class="badge badge-warning">Proses</span>
                                            <?php else: ?>
                                                 <span class="badge badge-success">Selesai</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Bar Chart (Main)
    var ctx = document.getElementById("myChart");
    var myChart = new Chart(ctx, {
        type: 'bar', // Changed from horizontalBar for cleaner look, or keep horizontalBar
        data: {
            labels: [<?php foreach ($assets as $asset) { echo '"' . $asset['asset_name'] . '"' . ","; } ?>],
            datasets: [{
                label: 'Jumlah Aset',
                data: [<?php foreach ($assets as $asset) { echo $asset['total'] . ','; } ?>],
                backgroundColor: 'rgba(78, 115, 223, 0.8)', // Primary color
                hoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                borderColor: '#4e73df',
                borderWidth: 1
            }]
        },
        options: {
            maintainAspectRatio: false,
            layout: { padding: { left: 10, right: 25, top: 25, bottom: 0 } },
            scales: {
                xAxes: [{ gridLines: { display: false, drawBorder: false }, ticks: { maxTicksLimit: 6 } }],
                yAxes: [{ ticks: { min: 0, padding: 10 } , gridLines: { color: "rgb(234, 236, 244)", zeroLineColor: "rgb(234, 236, 244)", drawBorder: false, borderDash: [2], zeroLineBorderDash: [2] } }]
            },
            legend: { display: false },
            tooltips: {
                titleMarginBottom: 10,
                titleFontColor: '#6e707e',
                titleFontSize: 14,
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
            },
        }
    });

    // Doughnut Chart (Categories)
    var ctxPie = document.getElementById("categoryChart");
    var myPieChart = new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: [<?php foreach ($asset_categories as $cat) { echo '"' . $cat['asset_name'] . '"' . ","; } ?>],
            datasets: [{
                data: [<?php foreach ($asset_categories as $cat) { echo $cat['total'] . ','; } ?>],
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
            },
            legend: { display: false },
            cutoutPercentage: 80,
        },
    });
</script>
