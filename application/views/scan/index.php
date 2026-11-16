<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="noindex, nofollow">
    <title><?= html_escape($asset ? $asset['asset_name'] . ' - ' . $asset['serial_number'] : 'Aset tidak ditemukan'); ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/logo_raharja.png') ?>">
    <link href="<?= base_url('assets/vendor/fontawesome-free/css/all.min.css'); ?>" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700,800" rel="stylesheet">
    <link href="<?= base_url('assets/css/sb-admin-2.min.css'); ?>" rel="stylesheet">
    <style>
        body { background: #f3f5fb; }
        .scan-wrap { max-width: 560px; margin: 0 auto; padding: 16px; }
        .scan-brand { font-size: .8rem; letter-spacing: .08em; text-transform: uppercase; color: #fff; opacity: .85; }
        .scan-hero { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); color: #fff; border-radius: 1rem; padding: 1.25rem; }
        .scan-hero h1 { font-size: 1.5rem; font-weight: 800; margin: .25rem 0; color: #fff; word-break: break-word; }
        .scan-serial { font-family: SFMono-Regular, Menlo, Consolas, monospace; font-size: .95rem; background: rgba(255,255,255,.15); border-radius: .4rem; padding: .15rem .5rem; display: inline-block; }
        .scan-qr { background: #fff; border-radius: .5rem; padding: 4px; width: 84px; height: 84px; }
        .scan-card { border: 0; border-radius: 1rem; box-shadow: 0 .15rem 1rem rgba(58,59,69,.08); }
        .scan-label { font-size: .72rem; text-transform: uppercase; letter-spacing: .06em; color: #858796; margin-bottom: .1rem; }
        .scan-value { font-weight: 700; color: #3a3b45; }
        .loc-path { display: flex; flex-wrap: wrap; align-items: center; gap: .35rem; }
        .loc-path .chip { background: #eaecf4; border-radius: 2rem; padding: .25rem .75rem; font-weight: 600; color: #3a3b45; font-size: .9rem; }
        .loc-path .chip.current { background: #4e73df; color: #fff; }
        .timeline { list-style: none; padding: 0; margin: 0; }
        .timeline li { position: relative; padding: 0 0 1rem 1.5rem; border-left: 2px solid #e3e6f0; margin-left: .4rem; }
        .timeline li:last-child { border-left-color: transparent; padding-bottom: 0; }
        .timeline li::before { content: ""; position: absolute; left: -7px; top: 3px; width: 12px; height: 12px; border-radius: 50%; background: #4e73df; }
        .timeline li.st-0::before { background: #f6c23e; }
        .timeline li.st-2::before { background: #e74a3b; }
        .timeline .when { font-size: .8rem; color: #858796; }
    </style>
</head>

<body>
    <div class="scan-wrap">
        <?php if (!empty($opname_result)) : ?>
            <?php $opn_alert = ['found' => 'success', 'already' => 'info', 'wrong_room' => 'warning']; ?>
            <div class="alert alert-<?= $opn_alert[$opname_result['status']] ?? 'danger'; ?> scan-card mb-3">
                <?= html_escape($opname_result['message']); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($opname) && $asset) : ?>
            <div class="card scan-card mb-3 border-left-warning" style="border-left: .3rem solid #f6c23e !important;">
                <div class="card-body py-3">
                    <div class="scan-label mb-1"><i class="fas fa-clipboard-check mr-1"></i> Stock opname berjalan</div>
                    <div class="font-weight-bold text-gray-800 mb-2">
                        <?= html_escape($opname['code']); ?> · Ruang <?= html_escape($opname['room_name']); ?>
                        <span class="small text-muted">(<?= (int) $opname['total_found']; ?>/<?= (int) $opname['total_expected']; ?> ditemukan)</span>
                    </div>
                    <?php if ($opname['asset_result'] === 1 || $opname['asset_result'] === 2) : ?>
                        <div class="text-success font-weight-bold"><i class="fas fa-check-circle mr-1"></i> Sudah tercatat di opname ini<?= $opname['asset_result'] === 2 ? ' (salah ruangan)' : ''; ?>.</div>
                    <?php else : ?>
                        <form method="post" action="<?= base_url('opname/mark_from_scan'); ?>">
                            <input type="hidden" name="token" value="<?= html_escape($opname['token']); ?>">
                            <input type="hidden" name="serial" value="<?= html_escape($asset['serial_number']); ?>">
                            <button type="submit" class="btn btn-warning btn-block font-weight-bold">
                                <i class="fas fa-check mr-1"></i> Tandai ditemukan di <?= html_escape($opname['room_name']); ?>
                            </button>
                        </form>
                    <?php endif; ?>
                    <a href="<?= base_url('opname/detail/' . $opname['token']); ?>" class="small d-inline-block mt-2">Buka sesi opname</a>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!$asset) : ?>
            <div class="card scan-card mt-4">
                <div class="card-body text-center py-5">
                    <i class="fas fa-search fa-3x text-gray-300 mb-3"></i>
                    <h1 class="h4 text-gray-800 font-weight-bold">Aset tidak ditemukan</h1>
                    <p class="text-muted mb-0">Nomor seri pada label tidak terdaftar atau sudah dihapus dari sistem.</p>
                </div>
            </div>
        <?php else : ?>
            <div class="scan-hero mb-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="pr-3">
                        <div class="scan-brand"><i class="fas fa-box mr-1"></i> Asset Management</div>
                        <h1><?= html_escape($asset['asset_name']); ?></h1>
                        <div class="mb-2"><?= html_escape($asset['merk']); ?></div>
                        <span class="scan-serial"><?= html_escape($asset['serial_number']); ?></span>
                    </div>
                    <img class="scan-qr" src="<?= $qr_image; ?>" alt="QR <?= html_escape($asset['serial_number']); ?>">
                </div>
            </div>

            <?php
            $is_disposed      = (int) $asset['is_disposed'] === 1;
            $disposal_pending = $disposal && (int) $disposal['status'] === 0;
            ?>
            <?php if ($is_disposed) : ?>
                <div class="alert alert-danger scan-card mb-3">
                    <div class="font-weight-bold"><i class="fas fa-ban mr-1"></i> Aset ini sudah dihapus dari daftar aset aktif</div>
                    <div class="small mt-1">
                        <?= $asset['disposed_at'] ? 'Disetujui ' . date('d M Y', strtotime($asset['disposed_at'])) : ''; ?>
                        <?php if ($disposal && (int) $disposal['status'] === 1) : ?>
                            · <?= html_escape($disposal['code']); ?> · <?= html_escape(Disposal_model::method_label($disposal['method'])); ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php elseif ($disposal_pending) : ?>
                <div class="alert alert-warning scan-card mb-3">
                    <div class="font-weight-bold"><i class="fas fa-hourglass-half mr-1"></i> Sedang diajukan untuk dihapus</div>
                    <div class="small mt-1"><?= html_escape($disposal['code']); ?> · menunggu persetujuan admin. Aset tidak bisa dipindahkan sementara.</div>
                </div>
            <?php elseif ($can_history && (int) $asset['asset_condition'] === 3) : ?>
                <div class="card scan-card mb-3" style="border-left: .3rem solid #e74a3b !important;">
                    <div class="card-body py-3">
                        <div class="font-weight-bold text-gray-800 mb-1">Aset rusak berat</div>
                        <div class="small text-muted mb-2">
                            <?php if ($disposal && (int) $disposal['status'] === 2) : ?>
                                Pengajuan sebelumnya (<?= html_escape($disposal['code']); ?>) ditolak<?= !empty($disposal['review_notes']) ? ': ' . html_escape($disposal['review_notes']) : ''; ?>.
                            <?php else : ?>
                                Kalau tidak bisa diperbaiki, ajukan penghapusan supaya admin bisa menyetujui.
                            <?php endif; ?>
                        </div>
                        <a href="<?= base_url('disposal/create?asset=' . (int) $asset['id_asset']); ?>" class="btn btn-outline-danger btn-block"><i class="fas fa-recycle mr-1"></i> Ajukan penghapusan</a>
                    </div>
                </div>
            <?php endif; ?>

            <div class="card scan-card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="scan-label mb-0"><?= $is_disposed ? 'Lokasi terakhir' : 'Lokasi saat ini'; ?></div>
                        <?php if ($is_disposed) : ?>
                            <span class="badge badge-danger px-2 py-1"><i class="fas fa-ban mr-1"></i> Dihapus</span>
                        <?php elseif ((int) $asset['placement_status'] === 1) : ?>
                            <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Di ruangan</span>
                        <?php else : ?>
                            <span class="badge badge-warning px-2 py-1"><i class="fas fa-truck mr-1"></i> Sedang dipindahkan</span>
                        <?php endif; ?>
                    </div>
                    <div class="loc-path mb-2">
                        <span class="chip"><i class="fas fa-building mr-1"></i><?= html_escape($asset['building_name']); ?></span>
                        <i class="fas fa-chevron-right text-gray-400 small"></i>
                        <span class="chip"><?= html_escape($asset['floor_name']); ?></span>
                        <i class="fas fa-chevron-right text-gray-400 small"></i>
                        <span class="chip current"><i class="fas fa-door-open mr-1"></i><?= html_escape($asset['room_name']); ?></span>
                    </div>
                    <?php if (!empty($asset['room_description'])) : ?>
                        <div class="text-muted small"><?= html_escape($asset['room_description']); ?></div>
                    <?php endif; ?>

                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <div class="scan-label">Kondisi</div>
                            <div class="scan-value">
                                <span class="badge badge-<?= asset_condition_color($asset['asset_condition']); ?> px-2 py-1" style="font-size: .85rem;">
                                    <?= html_escape(asset_condition_label($asset['asset_condition'])); ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="scan-label">Tanggal input</div>
                            <div class="scan-value"><?= $asset['created'] ? date('d M Y', strtotime($asset['created'])) : '-'; ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($can_history && ($asset['purchase_date'] || $asset['purchase_price'] !== null || $asset['vendor'])) : ?>
                <?php
                $depr     = asset_depreciation($asset['purchase_price'], $asset['purchase_date'], $asset['useful_life_years'], $asset['residual_percent'] ?? 0);
                $warranty = warranty_status($asset['warranty_until']);
                ?>
                <div class="card scan-card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="scan-label mb-0">Nilai aset</div>
                            <?php if ($asset['category_name']) : ?>
                                <span class="badge badge-light"><?= html_escape($asset['category_name']); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if ($depr) : ?>
                            <div class="h4 font-weight-bold text-gray-800 mb-1"><?= rupiah($depr['book_value']); ?></div>
                            <div class="small text-muted mb-2">
                                Nilai buku hari ini<?= $depr['configured'] ? ' · susut ' . $depr['percent_depreciated'] . '% (' . asset_age_label($depr['age_months']) . ' dari ' . ($depr['life_months'] / 12) . ' th)' : ' · tidak disusutkan'; ?>
                            </div>
                            <?php if ($depr['configured']) : ?>
                                <div class="progress mb-3" style="height: .4rem;">
                                    <div class="progress-bar bg-primary" style="width: <?= $depr['percent_depreciated']; ?>%"></div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-6 mb-2">
                                <div class="scan-label">Harga perolehan</div>
                                <div class="scan-value"><?= rupiah($asset['purchase_price']); ?></div>
                            </div>
                            <div class="col-6 mb-2">
                                <div class="scan-label">Tanggal perolehan</div>
                                <div class="scan-value"><?= $asset['purchase_date'] ? date('d M Y', strtotime($asset['purchase_date'])) : '-'; ?></div>
                            </div>
                            <div class="col-6">
                                <div class="scan-label">Vendor</div>
                                <div class="scan-value"><?= html_escape($asset['vendor'] ?: '-'); ?></div>
                            </div>
                            <div class="col-6">
                                <div class="scan-label">Garansi</div>
                                <div class="scan-value">
                                    <?= $warranty ? '<span class="badge badge-' . $warranty['color'] . '">' . html_escape($warranty['label']) . '</span>' : '-'; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($can_history && !$is_disposed) : ?>
                <div class="card scan-card mb-3">
                    <div class="card-body">
                        <div class="scan-label mb-2">Laporkan kondisi</div>

                        <?php if (!empty($condition_result)) : ?>
                            <?php $cond_alert = ['updated' => 'success', 'unchanged' => 'info']; ?>
                            <div class="alert alert-<?= $cond_alert[$condition_result['status']] ?? 'danger'; ?> py-2"><?= html_escape($condition_result['message']); ?></div>
                        <?php endif; ?>

                        <form method="post" action="<?= base_url('asset/update_condition'); ?>">
                            <input type="hidden" name="asset_id" value="<?= (int) $asset['id_asset']; ?>">
                            <div class="btn-group btn-group-toggle d-flex mb-2" data-toggle="buttons">
                                <?php foreach (asset_condition_options() as $value => $label) : ?>
                                    <?php $active = (int) $asset['asset_condition'] === $value; ?>
                                    <label class="btn btn-outline-<?= asset_condition_color($value); ?> flex-fill <?= $active ? 'active' : ''; ?>">
                                        <input type="radio" name="condition" value="<?= $value; ?>" autocomplete="off" <?= $active ? 'checked' : ''; ?>> <?= $label; ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <input type="text" name="notes" class="form-control mb-2" maxlength="255" placeholder="Keterangan (opsional), mis. layar retak">
                            <button type="submit" class="btn btn-primary btn-block">Simpan kondisi</button>
                        </form>

                        <?php if (!empty($condition_logs)) : ?>
                            <hr>
                            <div class="scan-label mb-2">Riwayat kondisi</div>
                            <ul class="timeline">
                                <?php
                                $source_label = ['manual' => 'Daftar aset', 'scan' => 'Scan QR', 'opname' => 'Stock opname'];
                                foreach ($condition_logs as $log) :
                                ?>
                                    <li class="st-<?= (int) $log['new_condition'] === 1 ? 1 : ((int) $log['new_condition'] === 2 ? 0 : 2); ?>">
                                        <div class="font-weight-bold text-gray-800">
                                            <?= html_escape(asset_condition_label($log['old_condition'])); ?>
                                            <i class="fas fa-long-arrow-alt-right mx-1 text-gray-500"></i>
                                            <?= html_escape(asset_condition_label($log['new_condition'])); ?>
                                        </div>
                                        <?php if (!empty($log['notes'])) : ?>
                                            <div class="small"><?= html_escape($log['notes']); ?></div>
                                        <?php endif; ?>
                                        <div class="when">
                                            <?= date('d M Y H:i', strtotime($log['created_at'])); ?>
                                            · <?= html_escape($log['username'] ?? '-'); ?>
                                            · <?= html_escape($source_label[$log['source']] ?? $log['source']); ?><?= !empty($log['opname_code']) ? ' ' . html_escape($log['opname_code']) : ''; ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="card scan-card mb-3">
                <div class="card-body">
                    <div class="scan-label mb-3">Riwayat perpindahan</div>

                    <?php if ($can_history) : ?>
                        <?php if (empty($movements)) : ?>
                            <p class="text-muted mb-0">Aset ini belum pernah dipindahkan.</p>
                        <?php else : ?>
                            <ul class="timeline">
                                <?php
                                $status_label = [0 => 'Dikirim, menunggu diterima', 1 => 'Diterima', 2 => 'Ditolak'];
                                foreach ($movements as $move) :
                                    $st = (int) $move['status'];
                                ?>
                                    <li class="st-<?= $st; ?>">
                                        <div class="font-weight-bold text-gray-800">
                                            <?= html_escape($move['source_name'] ?? '-'); ?>
                                            <i class="fas fa-long-arrow-alt-right mx-1 text-gray-500"></i>
                                            <?= html_escape($move['destination_name'] ?? '-'); ?>
                                        </div>
                                        <div class="small"><?= $status_label[$st] ?? '-'; ?>
                                            <?php if (!empty($move['trx_code'])) : ?>
                                                &middot; <span class="text-muted"><?= html_escape($move['trx_code']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="when">
                                            Dikirim <?= $move['sent'] ? date('d M Y', strtotime($move['sent'])) : '-'; ?>
                                            <?php if (!empty($move['received'])) : ?>
                                                &middot; <?= $st === 2 ? 'Ditolak' : 'Diterima'; ?> <?= date('d M Y', strtotime($move['received'])); ?>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($move['notes'])) : ?>
                                            <div class="small text-muted mt-1"><i class="fas fa-sticky-note mr-1"></i><?= html_escape($move['notes']); ?></div>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    <?php elseif ($user) : ?>
                        <p class="text-muted mb-0"><i class="fas fa-lock mr-1"></i> Aset ini berada di luar gedung yang Anda kelola.</p>
                    <?php else : ?>
                        <p class="text-muted">Login untuk melihat riwayat perpindahan aset ini.</p>
                        <a href="<?= base_url('scan/login/' . rawurlencode($asset['serial_number'])); ?>" class="btn btn-primary btn-block">
                            <i class="fas fa-sign-in-alt mr-1"></i> Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($can_history) : ?>
                <a href="<?= base_url('asset/location/' . $asset['room_id']); ?>" class="btn btn-outline-primary btn-block mb-3">
                    <i class="fas fa-door-open mr-1"></i> Buka ruangan <?= html_escape($asset['room_name']); ?>
                </a>
            <?php endif; ?>
        <?php endif; ?>

        <p class="text-center small text-muted mt-3 mb-0">Dipindai dari label aset &middot; <?= date('d M Y H:i'); ?></p>
    </div>
    <script>
        // Halaman ini tidak memuat JS Bootstrap; tandai tombol kondisi yang dipilih secara manual
        document.querySelectorAll('.btn-group-toggle input[type="radio"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                radio.closest('.btn-group-toggle').querySelectorAll('label').forEach(function(label) {
                    label.classList.toggle('active', label.contains(radio));
                });
            });
        });
    </script>
</body>

</html>
