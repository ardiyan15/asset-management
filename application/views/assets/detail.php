<?php
$a        = $asset;
$primary  = $photos[0] ?? null;
$cond     = (int) $a['asset_condition'];
$back     = 'asset/detail/' . (int) $a['id_asset'];
$scan_url = base_url('scan/' . rawurlencode($a['serial_number']));
$counts   = array_count_values(array_column($timeline, 'type'));
$transfers = $counts['transfer'] ?? 0;
?>
<style>
    .ad-photo { width: 168px; height: 168px; object-fit: cover; border-radius: .75rem; background: #eaecf4; }
    .ad-photo-empty { width: 168px; height: 168px; border-radius: .75rem; background: #eaecf4; color: #b7b9cc; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; }
    .ad-qr { width: 88px; height: 88px; border: 1px solid #e3e6f0; border-radius: .5rem; padding: 3px; background: #fff; }
    .ad-serial { font-family: SFMono-Regular, Menlo, Consolas, monospace; background: #eef1fb; padding: .1rem .5rem; border-radius: .35rem; }
    .ad-actions .btn { margin: 0 .25rem .4rem 0; }
    .ad-dl dt { font-weight: 600; color: #858796; font-size: .78rem; text-transform: uppercase; letter-spacing: .03em; }
    .ad-dl dd { color: #3a3b45; margin-bottom: .75rem; }
    .ad-stat { border-left: .25rem solid #4e73df; border-radius: .5rem; }
    .ad-timeline { list-style: none; padding: 0; margin: 0; }
    .ad-timeline li { position: relative; padding: 0 0 1.1rem 2.4rem; }
    .ad-timeline li::before { content: ""; position: absolute; left: .95rem; top: 1.6rem; bottom: 0; width: 2px; background: #e3e6f0; }
    .ad-timeline li:last-child::before { display: none; }
    .ad-timeline .ad-dot { position: absolute; left: 0; top: 0; width: 2rem; height: 2rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: .8rem; }
    .ad-timeline .ad-when { font-size: .78rem; color: #858796; }
    .ad-filter .btn { margin: 0 .25rem .4rem 0; }
</style>

<div class="container-fluid">
    <div class="flash-data" data-flashdata="<?= $this->session->flashdata('message'); ?>"></div>
    <a href="<?= base_url('asset'); ?>" class="small d-inline-block mb-2"><i class="fas fa-arrow-left mr-1"></i> Data Asset</a>

    <!-- Header -->
    <div class="card rounded p-3 mb-3">
        <div class="d-flex flex-wrap">
            <div class="mr-4 mb-3">
                <?php if ($primary) : ?>
                    <a href="<?= Photo_model::url($primary['asset_id'], $primary['file_name']); ?>" target="_blank" rel="noopener">
                        <img src="<?= Photo_model::url($primary['asset_id'], $primary['thumb_name']); ?>" class="ad-photo" alt="Foto <?= html_escape($a['asset_name']); ?>">
                    </a>
                <?php else : ?>
                    <div class="ad-photo-empty" title="Belum ada foto"><i class="fas fa-image"></i></div>
                <?php endif; ?>
            </div>
            <div class="flex-grow-1 mb-3" style="min-width: 260px;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h1 class="h3 text-gray-800 mb-1"><?= html_escape($a['asset_name']); ?></h1>
                        <div class="text-muted mb-2">
                            <?= html_escape($a['merk']); ?>
                            <?php if ($a['category_name']) : ?> · <?= html_escape($a['category_name']); ?><?php endif; ?>
                        </div>
                        <span class="ad-serial"><?= html_escape($a['serial_number']); ?></span>
                    </div>
                    <a href="<?= $scan_url; ?>" target="_blank" title="Halaman scan QR"><img src="<?= $qr_image; ?>" class="ad-qr ml-3" alt="QR"></a>
                </div>

                <div class="mt-3">
                    <?= asset_condition_badge($cond); ?>
                    <?php if ($is_disposed) : ?>
                        <span class="badge badge-dark"><i class="fas fa-ban mr-1"></i>Dihapus <?= $a['disposed_at'] ? date('d M Y', strtotime($a['disposed_at'])) : ''; ?></span>
                    <?php elseif ($pending_transfer) : ?>
                        <span class="badge badge-warning"><i class="fas fa-truck mr-1"></i>Sedang dipindahkan ke <?= html_escape($pending_transfer['dest_name']); ?></span>
                    <?php else : ?>
                        <span class="badge badge-light"><i class="fas fa-door-open mr-1"></i>Di ruangan</span>
                    <?php endif; ?>
                    <?php if ($open_loan) : ?>
                        <span class="badge badge-<?= $open_loan['state']['key'] === 'overdue' ? 'danger' : 'info'; ?>"><i class="fas fa-hand-holding mr-1"></i>Dipinjam · <?= $open_loan['state']['label']; ?></span>
                    <?php endif; ?>
                    <?php if ($disposal && (int) $disposal['status'] === 0) : ?>
                        <span class="badge badge-warning"><i class="fas fa-recycle mr-1"></i>Diajukan hapus</span>
                    <?php endif; ?>
                </div>
                <div class="small text-muted mt-2">
                    <i class="fas fa-building mr-1"></i><?= html_escape($a['building_name'] . ' · ' . $a['floor_name'] . ' · Ruang ' . $a['room_name']); ?>
                    <?php if ($a['room_description']) : ?>(<?= html_escape($a['room_description']); ?>)<?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Aksi -->
        <div class="ad-actions border-top pt-3">
            <?php if ($can['edit']) : ?>
                <a href="<?= base_url('asset/update/' . (int) $a['id_asset']); ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit mr-1"></i> Edit</a>
                <button type="button" class="btn btn-sm btn-outline-warning" data-toggle="modal" data-target="#conditionModal"><i class="fas fa-heartbeat mr-1"></i> Ubah kondisi</button>
            <?php endif; ?>
            <?php if ($can['move']) : ?>
                <button type="button" class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#moveModal"><i class="fas fa-exchange-alt mr-1"></i> Pindahkan</button>
            <?php endif; ?>
            <?php if ($can['lend']) : ?>
                <a href="<?= base_url('loan?asset=' . (int) $a['id_asset']); ?>" class="btn btn-sm btn-outline-info"><i class="fas fa-hand-holding mr-1"></i> Pinjamkan</a>
            <?php endif; ?>
            <?php if ($can['edit']) : ?>
                <a href="<?= base_url('maintenance?record=' . (int) $a['id_asset']); ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-tools mr-1"></i> Catat perbaikan</a>
                <a href="<?= base_url('asset/print_labels?ids=' . (int) $a['id_asset']); ?>" target="_blank" class="btn btn-sm btn-outline-dark"><i class="fas fa-qrcode mr-1"></i> Cetak label</a>
            <?php endif; ?>
            <?php if ($can['dispose']) : ?>
                <a href="<?= base_url('disposal/create?asset=' . (int) $a['id_asset']); ?>" class="btn btn-sm btn-outline-danger"><i class="fas fa-recycle mr-1"></i> Ajukan hapus</a>
            <?php endif; ?>
            <a href="<?= $scan_url; ?>" target="_blank" class="btn btn-sm btn-light"><i class="fas fa-mobile-alt mr-1"></i> Tampilan scan</a>
        </div>
    </div>

    <!-- Info status penting -->
    <?php if ($open_loan) : ?>
        <div class="alert alert-<?= $open_loan['state']['key'] === 'overdue' ? 'danger' : 'info'; ?> d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <i class="fas fa-hand-holding mr-1"></i> Dipinjam <b><?= html_escape($open_loan['borrower_name']); ?></b><?= $open_loan['borrower_unit'] ? ' (' . html_escape($open_loan['borrower_unit']) . ')' : ''; ?>
                sejak <?= date('d M Y', strtotime($open_loan['loaned_at'])); ?>, batas kembali <b><?= date('d M Y', strtotime($open_loan['due_date'])); ?></b> · <?= $open_loan['state']['label']; ?>
            </div>
            <a href="<?= base_url('loan'); ?>" class="btn btn-sm btn-light mt-2 mt-md-0">Kelola peminjaman</a>
        </div>
    <?php endif; ?>
    <?php if ($is_disposed && $disposal && (int) $disposal['status'] === 1) : ?>
        <div class="alert alert-dark"><i class="fas fa-ban mr-1"></i> Aset ini sudah dihapus lewat pengajuan <b><?= html_escape($disposal['code']); ?></b> (<?= html_escape(Disposal_model::method_label($disposal['method'])); ?>). Data tetap disimpan untuk riwayat.</div>
    <?php endif; ?>

    <!-- Tab -->
    <ul class="nav nav-tabs" id="assetTabs" role="tablist">
        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#ringkasan" role="tab">Ringkasan</a></li>
        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#riwayat" role="tab">Riwayat <span class="badge badge-secondary"><?= count($timeline); ?></span></a></li>
        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#foto" role="tab">Foto <span class="badge badge-secondary"><?= count($photos); ?></span></a></li>
        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#maintenance" role="tab">Maintenance <span class="badge badge-secondary"><?= count($maintenance_logs); ?></span></a></li>
        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#peminjaman" role="tab">Peminjaman <span class="badge badge-secondary"><?= count($loan_history); ?></span></a></li>
    </ul>

    <div class="tab-content card rounded-bottom border-top-0 p-3 mb-4">
        <!-- Ringkasan -->
        <div class="tab-pane fade show active" id="ringkasan" role="tabpanel">
            <div class="row">
                <div class="col-lg-4">
                    <h2 class="h6 font-weight-bold text-gray-800 mb-3">Identitas</h2>
                    <dl class="ad-dl">
                        <dt>Nama</dt><dd><?= html_escape($a['asset_name']); ?></dd>
                        <dt>Merk</dt><dd><?= html_escape($a['merk'] ?: '-'); ?></dd>
                        <dt>Nomor seri</dt><dd><?= html_escape($a['serial_number']); ?></dd>
                        <dt>Kategori</dt><dd><?= html_escape($a['category_name'] ? $a['category_code'] . ' - ' . $a['category_name'] : '-'); ?></dd>
                        <dt>Tanggal input</dt><dd><?= $a['created'] ? date('d M Y', strtotime($a['created'])) : '-'; ?></dd>
                    </dl>
                </div>
                <div class="col-lg-4">
                    <h2 class="h6 font-weight-bold text-gray-800 mb-3">Lokasi</h2>
                    <dl class="ad-dl">
                        <dt>Gedung</dt><dd><?= html_escape($a['building_name']); ?></dd>
                        <dt>Lantai</dt><dd><?= html_escape($a['floor_name']); ?></dd>
                        <dt>Ruangan</dt>
                        <dd>
                            <a href="<?= base_url('asset/location/' . (int) $a['room_id']); ?>"><?= html_escape($a['room_name']); ?></a>
                            <?php if ($a['room_description']) : ?><div class="small text-muted"><?= html_escape($a['room_description']); ?></div><?php endif; ?>
                        </dd>
                        <dt>Pindah ruangan</dt><dd><?= $transfers ? $transfers . ' kejadian' : 'Belum pernah'; ?></dd>
                    </dl>
                </div>
                <div class="col-lg-4">
                    <h2 class="h6 font-weight-bold text-gray-800 mb-3">Pembelian</h2>
                    <dl class="ad-dl">
                        <dt>Tanggal perolehan</dt><dd><?= $a['purchase_date'] ? date('d M Y', strtotime($a['purchase_date'])) : '-'; ?></dd>
                        <dt>Harga perolehan</dt><dd><?= rupiah($a['purchase_price']); ?></dd>
                        <dt>Vendor</dt><dd><?= html_escape($a['vendor'] ?: '-'); ?></dd>
                        <dt>Garansi</dt><dd><?= $warranty ? '<span class="badge badge-' . $warranty['color'] . '">' . html_escape($warranty['label']) . '</span>' : '-'; ?></dd>
                    </dl>
                    <?php if ($can['edit'] && !$a['purchase_date']) : ?>
                        <a href="<?= base_url('asset/update/' . (int) $a['id_asset']); ?>" class="small">Lengkapi data pembelian</a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-4 mb-3">
                    <div class="card ad-stat p-3 h-100">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Nilai buku hari ini</div>
                        <?php if ($depreciation) : ?>
                            <div class="h5 font-weight-bold text-gray-800 mb-1"><?= rupiah($is_disposed ? 0 : $depreciation['book_value']); ?></div>
                            <?php if ($depreciation['configured'] && !$is_disposed) : ?>
                                <div class="progress progress-sm mb-1"><div class="progress-bar bg-primary" style="width: <?= $depreciation['percent_depreciated']; ?>%"></div></div>
                                <div class="small text-muted">Umur <?= asset_age_label($depreciation['age_months']); ?> dari <?= $depreciation['life_months'] / 12; ?> th · susut <?= rupiah($depreciation['monthly']); ?>/bln</div>
                            <?php elseif (!$is_disposed) : ?>
                                <div class="small text-muted">Kategori belum punya masa manfaat.</div>
                            <?php endif; ?>
                        <?php else : ?>
                            <div class="small text-muted">Data pembelian belum lengkap.</div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card ad-stat p-3 h-100" style="border-left-color: #f6c23e;">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Biaya maintenance</div>
                        <div class="h5 font-weight-bold text-gray-800 mb-1"><?= rupiah($maintenance_cost); ?></div>
                        <div class="small text-muted"><?= count($maintenance_logs); ?> pekerjaan · <?= count(array_filter($schedules, function ($s) { return (int) $s['is_active'] === 1; })); ?> jadwal aktif</div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card ad-stat p-3 h-100" style="border-left-color: #36b9cc;">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Peminjaman</div>
                        <div class="h5 font-weight-bold text-gray-800 mb-1"><?= count($loan_history); ?> <span class="small text-muted font-weight-normal">kali</span></div>
                        <div class="small text-muted"><?= $open_loan ? 'Sedang dipinjam' : 'Tersedia di ruangan'; ?></div>
                    </div>
                </div>
            </div>

            <?php if ($timeline) : ?>
                <h2 class="h6 font-weight-bold text-gray-800 mt-2 mb-3">Aktivitas terakhir</h2>
                <?php $this->load->view('assets/_timeline', ['events' => array_slice($timeline, 0, 5), 'types' => $timeline_types]); ?>
                <a href="#riwayat" class="small js-open-tab">Lihat semua riwayat (<?= count($timeline); ?>)</a>
            <?php endif; ?>
        </div>

        <!-- Riwayat -->
        <div class="tab-pane fade" id="riwayat" role="tabpanel">
            <?php if (empty($timeline)) : ?>
                <p class="text-muted mb-0">Belum ada riwayat.</p>
            <?php else : ?>
                <div class="ad-filter mb-3">
                    <button type="button" class="btn btn-sm btn-primary js-filter" data-type="">Semua (<?= count($timeline); ?>)</button>
                    <?php foreach ($timeline_types as $key => $t) : ?>
                        <?php if (empty($counts[$key])) continue; ?>
                        <button type="button" class="btn btn-sm btn-light border js-filter" data-type="<?= $key; ?>"><i class="fas <?= $t['icon']; ?> mr-1"></i><?= $t['label']; ?> (<?= $counts[$key]; ?>)</button>
                    <?php endforeach; ?>
                </div>
                <?php $this->load->view('assets/_timeline', ['events' => $timeline, 'types' => $timeline_types]); ?>
            <?php endif; ?>
        </div>

        <!-- Foto -->
        <div class="tab-pane fade" id="foto" role="tabpanel">
            <?php $this->load->view('photos/_gallery', [
                'asset'    => $a,
                'photos'   => $photos,
                'can_edit' => $can['edit'],
                'back'     => $back . '#foto',
            ]); ?>
        </div>

        <!-- Maintenance -->
        <div class="tab-pane fade" id="maintenance" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h2 class="h6 font-weight-bold text-gray-800 mb-0">Jadwal</h2>
                <a href="<?= base_url('maintenance?status=all'); ?>" class="small">Kelola jadwal</a>
            </div>
            <?php if (empty($schedules)) : ?>
                <p class="small text-muted">Belum ada jadwal maintenance untuk aset ini.</p>
            <?php else : ?>
                <div class="table-responsive mb-3">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Perawatan</th><th>Interval</th><th>Terakhir</th><th>Berikutnya</th></tr></thead>
                        <tbody>
                            <?php foreach ($schedules as $s) : ?>
                                <tr class="<?= (int) $s['is_active'] === 1 ? '' : 'text-muted'; ?>">
                                    <td><?= html_escape($s['title']); ?></td>
                                    <td><?= maintenance_interval_label($s['interval_value'], $s['interval_unit']); ?></td>
                                    <td><?= $s['last_done_date'] ? date('d M Y', strtotime($s['last_done_date'])) : '-'; ?></td>
                                    <td><?= date('d M Y', strtotime($s['next_due_date'])); ?> <span class="badge badge-<?= $s['due']['color']; ?>"><?= $s['due']['label']; ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <h2 class="h6 font-weight-bold text-gray-800 mb-2">Riwayat pekerjaan <span class="font-weight-normal text-muted">· total <?= rupiah($maintenance_cost); ?></span></h2>
            <?php if (empty($maintenance_logs)) : ?>
                <p class="small text-muted mb-0">Belum ada pekerjaan maintenance tercatat.</p>
            <?php else : ?>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Tanggal</th><th>Jenis</th><th>Pekerjaan</th><th>Vendor</th><th class="text-right">Biaya</th></tr></thead>
                        <tbody>
                            <?php foreach ($maintenance_logs as $m) : ?>
                                <tr>
                                    <td class="text-nowrap"><?= date('d M Y', strtotime($m['performed_at'])); ?></td>
                                    <td><span class="badge <?= $m['type'] === 'rutin' ? 'badge-info' : 'badge-danger'; ?>"><?= $m['type'] === 'rutin' ? 'Rutin' : 'Perbaikan'; ?></span></td>
                                    <td><?= html_escape($m['description']); ?></td>
                                    <td><?= html_escape($m['vendor'] ?: '-'); ?></td>
                                    <td class="text-right text-nowrap"><?= rupiah($m['cost']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Peminjaman -->
        <div class="tab-pane fade" id="peminjaman" role="tabpanel">
            <?php if (empty($loan_history)) : ?>
                <p class="small text-muted mb-0">Aset ini belum pernah dipinjam.</p>
            <?php else : ?>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Kode</th><th>Peminjam</th><th>Dipinjam</th><th>Batas</th><th>Kembali</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php foreach ($loan_history as $l) : ?>
                                <?php $st = loan_status($l); ?>
                                <tr>
                                    <td class="small"><?= html_escape($l['code']); ?></td>
                                    <td><?= html_escape($l['borrower_name']); ?><?= $l['borrower_unit'] ? '<div class="small text-muted">' . html_escape($l['borrower_unit']) . '</div>' : ''; ?></td>
                                    <td class="text-nowrap"><?= date('d M Y', strtotime($l['loaned_at'])); ?></td>
                                    <td class="text-nowrap"><?= date('d M Y', strtotime($l['due_date'])); ?></td>
                                    <td class="text-nowrap"><?= $l['returned_at'] ? date('d M Y', strtotime($l['returned_at'])) . '<div class="small text-muted">' . html_escape(asset_condition_label($l['condition_in'])) . '</div>' : '-'; ?></td>
                                    <td><span class="badge badge-<?= $st['color']; ?>"><?= $st['label']; ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($can['edit']) : ?>
    <!-- Modal ubah kondisi -->
    <div class="modal fade" id="conditionModal" role="dialog" aria-labelledby="conditionModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="conditionModalLabel">Ubah Kondisi Aset</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <form id="conditionForm">
                    <input type="hidden" name="asset_id" value="<?= (int) $a['id_asset']; ?>">
                    <div class="modal-body">
                        <div class="form-group">
                            <?php foreach (asset_condition_options() as $value => $label) : ?>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="dcond<?= $value; ?>" name="condition" value="<?= $value; ?>" class="custom-control-input" <?= $cond === $value ? 'checked' : ''; ?>>
                                    <label class="custom-control-label text-<?= asset_condition_color($value); ?> font-weight-bold" for="dcond<?= $value; ?>"><?= $label; ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <input type="text" class="form-control" name="notes" maxlength="255" placeholder="Keterangan (opsional)">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if ($can['move']) : ?>
    <!-- Modal pindahkan -->
    <div class="modal fade" id="moveModal" role="dialog" aria-labelledby="moveModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="moveModalLabel">Pindahkan Aset</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <form method="post" action="<?= base_url('Transaction/store'); ?>">
                    <input type="hidden" name="asset_id" value="<?= (int) $a['id_asset']; ?>">
                    <input type="hidden" name="back" value="<?= $back; ?>">
                    <div class="modal-body">
                        <p class="small text-muted">Dari <b><?= html_escape($a['room_name']); ?></b>. Aset berstatus "sedang dipindahkan" sampai diterima di ruangan tujuan.</p>
                        <div class="form-group">
                            <label for="move_room">Ruangan tujuan</label>
                            <select class="form-control" id="move_room" name="room" required style="width: 100%;">
                                <option value="">-- Pilih ruangan --</option>
                                <?php foreach ($rooms as $room) : ?>
                                    <?php if ((int) $room['id'] === (int) $a['room_id']) continue; ?>
                                    <option value="<?= $room['id']; ?>"><?= html_escape($room['name'] . ($room['description'] ? ' — ' . $room['description'] : '')); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <input type="text" class="form-control" name="note" maxlength="255" placeholder="Catatan (opsional)">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info btn-sm">Pindahkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    $(function() {
        // Buka tab dari hash (#foto, #riwayat, ...) supaya kembali ke tab yang sama setelah aksi
        const openTab = function(hash) {
            const $link = $('#assetTabs a[href="' + hash + '"]');
            if ($link.length) $link.tab('show');
        };
        if (location.hash) openTab(location.hash);
        $('#assetTabs a').on('shown.bs.tab', function(e) {
            history.replaceState(null, '', e.target.hash);
        });
        $('.js-open-tab').on('click', function(e) {
            e.preventDefault();
            openTab($(this).attr('href'));
            window.scrollTo(0, $('#assetTabs').offset().top - 80);
        });

        // Filter timeline per jenis kejadian
        $('.js-filter').on('click', function() {
            const type = $(this).data('type');
            $('.js-filter').removeClass('btn-primary').addClass('btn-light border');
            $(this).removeClass('btn-light border').addClass('btn-primary');
            $('#riwayat .ad-timeline li').each(function() {
                $(this).toggle(!type || $(this).data('type') === type);
            });
        });

        if ($.fn.select2) {
            $('#move_room').select2({
                theme: 'bootstrap',
                dropdownParent: $('#moveModal')
            });
        }

        $('#conditionForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: <?= json_encode(base_url('asset/update_condition')); ?>,
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json'
            }).done(function(res) {
                $('#conditionModal').modal('hide');
                Swal.fire({
                    title: res.status === 'failed' ? 'Gagal' : 'Berhasil',
                    text: res.message,
                    icon: res.status === 'failed' ? 'error' : (res.status === 'unchanged' ? 'info' : 'success')
                }).then(function() {
                    if (res.status === 'updated') location.reload();
                });
            }).fail(function() {
                Swal.fire('Gagal', 'Gagal menghubungi server.', 'error');
            });
        });
    });
</script>
