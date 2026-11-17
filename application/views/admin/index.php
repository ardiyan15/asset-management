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

    <!-- Kondisi aset -->
    <?php
    $condition_total = array_sum($condition_counts);
    $condition_icons = [1 => 'fa-check-circle', 2 => 'fa-tools', 3 => 'fa-times-circle'];
    ?>
    <div class="row">
        <?php foreach (asset_condition_options() as $value => $label) : ?>
            <?php
            $count = $condition_counts[$value] ?? 0;
            $pct   = $condition_total > 0 ? round($count / $condition_total * 100) : 0;
            $color = asset_condition_color($value);
            ?>
            <div class="col-md-4 mb-4">
                <a href="<?= base_url('asset?condition=' . $value); ?>" class="text-decoration-none">
                    <div class="card border-left-<?= $color; ?> shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-<?= $color; ?> text-uppercase mb-1">Kondisi <?= $label; ?></div>
                                    <div class="h5 mb-1 font-weight-bold text-gray-800"><?= $count; ?> <span class="small text-muted font-weight-normal">aset · <?= $pct; ?>%</span></div>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-<?= $color; ?>" role="progressbar" style="width: <?= $pct; ?>%" aria-valuenow="<?= $pct; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas <?= $condition_icons[$value]; ?> fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
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
                        <?php $cat_colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796']; ?>
                        <?php foreach ($asset_categories as $i => $cat) : ?>
                            <span class="mr-2 d-inline-block">
                                <i class="fas fa-circle" style="color: <?= $cat_colors[$i % count($cat_colors)]; ?>"></i>
                                <?= html_escape($cat['label']); ?> (<?= (int) $cat['total']; ?>)
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaksi per bulan & aset rusak -->
    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Transaksi Pindah Aset per Bulan</h6>
                    <span class="small text-muted">12 bulan terakhir</span>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 300px;">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Aset Rusak per <?= $user['role_id'] == '1' ? 'Gedung' : 'Ruangan'; ?></h6>
                    <a href="<?= base_url('report?group=' . ($user['role_id'] == '1' ? 'building' : 'room')); ?>" class="small">Rekap</a>
                </div>
                <div class="card-body">
                    <?php
                    $damaged = array_values(array_filter($condition_locations, function ($l) {
                        return $l[2] + $l[3] > 0;
                    }));
                    ?>
                    <?php if (empty($damaged)) : ?>
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-check-circle fa-3x text-gray-300 mb-3"></i>
                            <p class="mb-0 small">Tidak ada aset rusak. Semua aset aktif dalam kondisi baik.</p>
                        </div>
                    <?php else : ?>
                        <div style="position: relative; height: <?= max(220, min(300, count($damaged) * 34 + 70)); ?>px;">
                            <canvas id="damagedChart"></canvas>
                        </div>
                    <?php endif; ?>
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
                                    <th>Kode</th>
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
                                    <?php $trx_status = [0 => ['warning', 'Dikirim'], 1 => ['success', 'Diterima'], 2 => ['secondary', 'Ditolak']]; ?>
                                    <?php foreach($recent_transactions as $trans): ?>
                                    <?php $st = $trx_status[(int) $trans['status']] ?? ['light', '-']; ?>
                                    <tr>
                                        <td class="small text-nowrap"><?= html_escape($trans['trx_code'] ?? '-') ?></td>
                                        <td><?= html_escape($trans['asset_name'] ?? '-') ?></td>
                                        <td><code><?= html_escape($trans['serial_number'] ?? '-') ?></code></td>
                                        <td><?= html_escape($trans['source_name'] ?? '-') ?></td>
                                        <td><?= html_escape($trans['destination_name'] ?? '-') ?></td>
                                        <td class="text-nowrap"><?= date('d M Y, H:i', strtotime($trans['created_at'])) ?></td>
                                        <td><span class="badge badge-<?= $st[0] ?>"><?= $st[1] ?></span></td>
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
            labels: <?= json_encode(array_column($assets, 'asset_name')); ?>,
            datasets: [{
                label: 'Jumlah Aset',
                data: <?= json_encode(array_map('intval', array_column($assets, 'total'))); ?>,
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
            labels: <?= json_encode(array_column($asset_categories, 'label')); ?>,
            datasets: [{
                data: <?= json_encode(array_map('intval', array_column($asset_categories, 'total'))); ?>,
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'],
                hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617', '#6e707e'],
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

    // Transaksi pindah aset per bulan (dikirim / diterima / ditolak)
    var monthly = <?= json_encode($monthly_transactions); ?>;
    new Chart(document.getElementById('monthlyChart'), {
        type: 'bar',
        data: {
            labels: monthly.labels,
            datasets: [
                { label: 'Diterima', backgroundColor: '#1cc88a', data: monthly[1] },
                { label: 'Menunggu', backgroundColor: '#f6c23e', data: monthly[0] },
                { label: 'Ditolak', backgroundColor: '#858796', data: monthly[2] }
            ]
        },
        options: {
            maintainAspectRatio: false,
            legend: { position: 'bottom' },
            scales: {
                xAxes: [{ stacked: true, gridLines: { display: false } }],
                yAxes: [{
                    stacked: true,
                    ticks: { beginAtZero: true, callback: function (v) { return v % 1 === 0 ? v : ''; } },
                    gridLines: { color: 'rgb(234, 236, 244)', drawBorder: false, borderDash: [2] }
                }]
            },
            tooltips: { mode: 'index' }
        }
    });

    // Aset rusak per gedung (admin) / ruangan (user)
    var damaged = <?= json_encode(array_values(array_filter($condition_locations, function ($l) { return $l[2] + $l[3] > 0; }))); ?>;
    if (damaged.length) {
        new Chart(document.getElementById('damagedChart'), {
            type: 'horizontalBar',
            data: {
                labels: damaged.map(function (d) { return d.label; }),
                datasets: [
                    { label: 'Rusak Ringan', backgroundColor: '#f6c23e', data: damaged.map(function (d) { return d[2]; }) },
                    { label: 'Rusak Berat', backgroundColor: '#e74a3b', data: damaged.map(function (d) { return d[3]; }) }
                ]
            },
            options: {
                maintainAspectRatio: false,
                legend: { position: 'bottom' },
                scales: {
                    xAxes: [{ stacked: true, ticks: { beginAtZero: true, callback: function (v) { return v % 1 === 0 ? v : ''; } } }],
                    yAxes: [{ stacked: true, gridLines: { display: false } }]
                }
            }
        });
    }
</script>
