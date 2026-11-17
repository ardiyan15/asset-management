<?php
$back       = 'loan?' . http_build_query(array_filter(['status' => $status, 'building_id' => $building_filter]));
$tab_counts = [
    'open'     => $counts['open'] ?? 0,
    'overdue'  => $counts['overdue'] ?? 0,
    'returned' => $counts['returned'] ?? 0,
    'all'      => $counts['total'] ?? 0,
];
$slip_token = $this->session->flashdata('loan_slip');
?>
<div class="container-fluid">
    <div class="flash-data" data-flashdata="<?= $this->session->flashdata('message'); ?>"></div>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 text-gray-800 mb-1"><?= $title; ?></h1>
            <p class="small text-muted mb-0">Pinjam sementara (proyektor, laptop, dll). Lokasi aset di sistem tidak berubah; yang dicatat peminjam & batas kembali.</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm mt-2 mt-md-0" data-toggle="modal" data-target="#loanModal"><i class="fas fa-hand-holding mr-1"></i> Pinjamkan Aset</button>
    </div>

    <?php if ($slip_token) : ?>
        <div class="alert alert-success d-flex justify-content-between align-items-center">
            <span><i class="fas fa-check-circle mr-1"></i> Peminjaman tercatat. Cetak bukti untuk ditandatangani peminjam.</span>
            <a href="<?= base_url('loan/print_slip/' . $slip_token); ?>" target="_blank" class="btn btn-sm btn-success"><i class="fas fa-print mr-1"></i> Cetak bukti</a>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Sedang dipinjam</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= (int) ($counts['open'] ?? 0); ?> <span class="small text-muted font-weight-normal">aset</span></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Terlambat</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= (int) ($counts['overdue'] ?? 0); ?> <span class="small text-muted font-weight-normal">aset</span></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Kembali hari ini</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= (int) ($counts['due_today'] ?? 0); ?> <span class="small text-muted font-weight-normal">aset</span></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Dikembalikan bulan ini</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= (int) ($counts['returned_this_month'] ?? 0); ?> <span class="small text-muted font-weight-normal">kali</span></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card rounded p-3 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <ul class="nav nav-pills mb-2 mb-md-0">
                <?php foreach ($tabs as $key => $label) : ?>
                    <li class="nav-item">
                        <a class="nav-link py-1 <?= $status === $key ? 'active' : ''; ?>" href="<?= base_url('loan?' . http_build_query(array_filter(['status' => $key, 'building_id' => $building_filter]))); ?>">
                            <?= $label; ?> <span class="badge <?= $status === $key ? 'badge-light' : ($key === 'overdue' && $tab_counts[$key] > 0 ? 'badge-danger' : 'badge-secondary'); ?>"><?= $tab_counts[$key]; ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php if ($user['role_id'] === '1') : ?>
                <form method="get" class="form-inline">
                    <input type="hidden" name="status" value="<?= html_escape($status); ?>">
                    <select name="building_id" class="form-control form-control-sm" onchange="this.form.submit()">
                        <option value="">Semua gedung</option>
                        <?php foreach ($buildings as $b) : ?>
                            <option value="<?= $b['id']; ?>" <?= $building_filter === (int) $b['id'] ? 'selected' : ''; ?>><?= html_escape($b['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            <?php endif; ?>
        </div>

        <?php if (empty($loans)) : ?>
            <div class="text-center text-muted py-5">
                <i class="fas fa-hand-holding fa-3x mb-3 text-gray-300"></i>
                <p class="mb-0"><?= $status === 'overdue' ? 'Tidak ada peminjaman yang terlambat. 👍' : 'Belum ada peminjaman di daftar ini.'; ?></p>
            </div>
        <?php else : ?>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Aset</th>
                            <th>Peminjam</th>
                            <th>Dipinjam</th>
                            <th>Batas kembali</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($loans as $loan) : ?>
                            <?php $st = $loan['loan_state']; $open = (int) $loan['status'] === 0; ?>
                            <tr>
                                <td class="small text-nowrap"><?= html_escape($loan['code']); ?></td>
                                <td>
                                    <?= html_escape($loan['asset_name']); ?> <span class="small text-muted"><?= html_escape($loan['merk']); ?></span>
                                    <div><a href="<?= base_url('scan/' . rawurlencode($loan['serial_number'])); ?>" target="_blank"><code class="small"><?= html_escape($loan['serial_number']); ?></code></a></div>
                                    <div class="small text-muted">dari <?= html_escape($loan['room_name'] . ', ' . $loan['building_name']); ?></div>
                                </td>
                                <td>
                                    <b><?= html_escape($loan['borrower_name']); ?></b>
                                    <?php if ($loan['borrower_unit']) : ?><div class="small"><?= html_escape($loan['borrower_unit']); ?></div><?php endif; ?>
                                    <?php if ($loan['borrower_phone']) : ?><div class="small text-muted"><i class="fas fa-phone-alt mr-1"></i><?= html_escape($loan['borrower_phone']); ?></div><?php endif; ?>
                                    <?php if ($loan['purpose']) : ?><div class="small text-muted"><?= html_escape($loan['purpose']); ?></div><?php endif; ?>
                                </td>
                                <td class="small text-nowrap"><?= date('d M Y', strtotime($loan['loaned_at'])); ?><div class="text-muted">oleh <?= html_escape($loan['created_by_name'] ?? '-'); ?></div></td>
                                <td class="text-nowrap">
                                    <?= date('d M Y', strtotime($loan['due_date'])); ?>
                                    <?php if ((int) $loan['extended_count'] > 0) : ?><span class="small text-muted">(diperpanjang <?= (int) $loan['extended_count']; ?>×)</span><?php endif; ?>
                                    <div><span class="badge badge-<?= $st['color']; ?>"><?= $st['label']; ?></span></div>
                                    <?php if (!$open) : ?>
                                        <div class="small text-muted">kembali <?= date('d M Y', strtotime($loan['returned_at'])); ?> · <?= html_escape(asset_condition_label($loan['condition_in'])); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right text-nowrap">
                                    <?php if ($open) : ?>
                                        <button type="button" class="btn btn-success btn-sm js-return"
                                            data-token="<?= html_escape($loan['token']); ?>"
                                            data-title="<?= html_escape($loan['asset_name'] . ' — ' . $loan['borrower_name']); ?>"
                                            data-loaned="<?= substr($loan['loaned_at'], 0, 10); ?>"
                                            data-condition="<?= (int) $loan['condition_out']; ?>"><i class="fas fa-undo mr-1"></i> Kembalikan</button>
                                        <button type="button" class="btn btn-light btn-sm js-extend" title="Perpanjang"
                                            data-token="<?= html_escape($loan['token']); ?>"
                                            data-title="<?= html_escape($loan['asset_name'] . ' — ' . $loan['borrower_name']); ?>"
                                            data-due="<?= html_escape($loan['due_date']); ?>"><i class="fas fa-calendar-plus"></i></button>
                                    <?php endif; ?>
                                    <a href="<?= base_url('loan/print_slip/' . $loan['token']); ?>" target="_blank" class="btn btn-light btn-sm" title="Cetak bukti"><i class="fas fa-print"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal pinjamkan -->
<div class="modal fade" id="loanModal" role="dialog" aria-labelledby="loanModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loanModalLabel">Pinjamkan Aset</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form method="post" action="<?= base_url('loan/store'); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="loan_asset">Aset</label>
                        <select class="form-control js-asset" id="loan_asset" name="asset_id" required style="width: 100%;">
                            <option value="">-- Pilih aset yang tersedia --</option>
                            <?php foreach ($assets as $a) : ?>
                                <option value="<?= $a['id_asset']; ?>" <?= $preselect === (int) $a['id_asset'] ? 'selected' : ''; ?>>
                                    <?= html_escape($a['asset_name'] . ' — ' . $a['serial_number'] . ' (' . $a['room_name'] . ', ' . $a['building_name'] . ')' . ((int) $a['asset_condition'] !== 1 ? ' · ' . asset_condition_label($a['asset_condition']) : '')); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">Aset yang sedang dipinjam, dipindahkan, atau diajukan hapus tidak muncul.</small>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="borrower_name">Nama peminjam</label>
                            <input type="text" class="form-control" id="borrower_name" name="borrower_name" maxlength="100" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="borrower_unit">Unit / divisi</label>
                            <input type="text" class="form-control" id="borrower_unit" name="borrower_unit" maxlength="100" placeholder="opsional">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="borrower_phone">No. HP</label>
                            <input type="tel" class="form-control" id="borrower_phone" name="borrower_phone" maxlength="30" placeholder="opsional">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="purpose">Keperluan</label>
                            <input type="text" class="form-control" id="purpose" name="purpose" maxlength="255" placeholder="mis. Presentasi di aula">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-6">
                            <label for="loaned_date">Tanggal pinjam</label>
                            <input type="date" class="form-control" id="loaned_date" name="loaned_date" max="<?= date('Y-m-d'); ?>" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div class="form-group col-6">
                            <label for="due_date">Batas kembali</label>
                            <input type="date" class="form-control" id="due_date" name="due_date" min="<?= date('Y-m-d'); ?>" value="<?= date('Y-m-d', strtotime('+3 days')); ?>" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal kembalikan -->
<div class="modal fade" id="returnModal" role="dialog" aria-labelledby="returnModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="returnModalLabel">Pengembalian Aset</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form method="post" action="<?= base_url('loan/return_loan'); ?>">
                <input type="hidden" name="token" value="">
                <input type="hidden" name="back" value="<?= html_escape($back); ?>">
                <div class="modal-body">
                    <p class="font-weight-bold text-gray-800 js-loan-title"></p>
                    <div class="form-row">
                        <div class="form-group col-6">
                            <label for="returned_date">Tanggal kembali</label>
                            <input type="date" class="form-control" id="returned_date" name="returned_date" max="<?= date('Y-m-d'); ?>" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div class="form-group col-6">
                            <label for="condition_in">Kondisi saat kembali</label>
                            <select class="form-control" id="condition_in" name="condition_in">
                                <?php foreach (asset_condition_options() as $value => $label) : ?>
                                    <option value="<?= $value; ?>"><?= $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label for="return_notes">Catatan</label>
                        <input type="text" class="form-control" id="return_notes" name="return_notes" maxlength="255" placeholder="mis. kabel HDMI tidak ikut dikembalikan">
                        <small class="form-text text-muted">Kalau kondisi berbeda dari saat dipinjam, kondisi aset ikut diperbarui.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm">Simpan pengembalian</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal perpanjang -->
<div class="modal fade" id="extendModal" role="dialog" aria-labelledby="extendModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="extendModalLabel">Perpanjang Peminjaman</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form method="post" action="<?= base_url('loan/extend'); ?>">
                <input type="hidden" name="token" value="">
                <input type="hidden" name="back" value="<?= html_escape($back); ?>">
                <div class="modal-body">
                    <p class="small font-weight-bold text-gray-800 js-loan-title"></p>
                    <label for="extend_due">Batas kembali baru</label>
                    <input type="date" class="form-control" id="extend_due" name="due_date" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Perpanjang</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(function() {
        if ($.fn.select2) {
            $('#loan_asset').select2({
                theme: 'bootstrap',
                dropdownParent: $('#loanModal')
            });
        }

        // ?asset=<id> dari halaman scan langsung membuka form pinjam
        <?php if ($preselect) : ?>
            $('#loanModal').modal('show');
        <?php endif; ?>

        $('.js-return').on('click', function() {
            const d = $(this).data();
            const $m = $('#returnModal');
            $m.find('[name="token"]').val(d.token);
            $m.find('.js-loan-title').text(d.title);
            $m.find('#returned_date').attr('min', d.loaned);
            $m.find('#condition_in').val(d.condition);
            $m.find('#return_notes').val('');
            $m.modal('show');
        });

        $('.js-extend').on('click', function() {
            const d = $(this).data();
            const $m = $('#extendModal');
            const today = new Date().toISOString().slice(0, 10);
            const min = d.due >= today ? new Date(new Date(d.due).getTime() + 86400000).toISOString().slice(0, 10) : today;
            $m.find('[name="token"]').val(d.token);
            $m.find('.js-loan-title').text(d.title);
            $m.find('#extend_due').attr('min', min).val(min);
            $m.modal('show');
        });
    });
</script>
