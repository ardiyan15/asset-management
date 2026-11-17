<?php
$back = 'maintenance?' . http_build_query(array_filter(['status' => $status, 'building_id' => $building_filter]));
$tab_counts = [
    'due'      => $counts['due'] ?? 0,
    'upcoming' => $counts['upcoming'] ?? 0,
    'inactive' => $counts['inactive'] ?? 0,
    'all'      => $counts['total'] ?? 0,
];
?>
<div class="container-fluid">
    <div class="flash-data" data-flashdata="<?= $this->session->flashdata('message'); ?>"></div>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 text-gray-800 mb-1"><?= $title; ?></h1>
            <p class="small text-muted mb-0">Perawatan berkala aset (mis. servis AC tiap 3 bulan). Jadwal otomatis maju ke periode berikutnya setiap servis dicatat.</p>
        </div>
        <div class="mt-2 mt-md-0">
            <a href="<?= base_url('maintenance/history'); ?>" class="btn btn-outline-primary btn-sm"><i class="fas fa-history mr-1"></i> Riwayat & Biaya</a>
            <button type="button" class="btn btn-outline-danger btn-sm js-record" data-mode="repair"><i class="fas fa-tools mr-1"></i> Catat Perbaikan</button>
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#scheduleModal"><i class="fas fa-plus mr-1"></i> Tambah Jadwal</button>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Terlambat / hari ini</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= (int) ($counts['overdue'] ?? 0); ?> <span class="small text-muted font-weight-normal">jadwal</span></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Segera jatuh tempo</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= (int) ($counts['soon'] ?? 0); ?> <span class="small text-muted font-weight-normal">jadwal</span></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Terjadwal</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= (int) ($counts['upcoming'] ?? 0); ?> <span class="small text-muted font-weight-normal">jadwal</span></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="<?= base_url('maintenance/history'); ?>" class="text-decoration-none">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Biaya <?= date('Y'); ?></div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= rupiah($year_cost); ?></div>
                        <div class="small text-muted"><?= $year_count; ?> kali perawatan/perbaikan</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="card rounded p-3 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <ul class="nav nav-pills mb-2 mb-md-0">
                <?php foreach ($tabs as $key => $label) : ?>
                    <li class="nav-item">
                        <a class="nav-link py-1 <?= $status === $key ? 'active' : ''; ?>" href="<?= base_url('maintenance?' . http_build_query(array_filter(['status' => $key, 'building_id' => $building_filter]))); ?>">
                            <?= $label; ?> <span class="badge <?= $status === $key ? 'badge-light' : 'badge-secondary'; ?>"><?= $tab_counts[$key]; ?></span>
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

        <?php if (empty($schedules)) : ?>
            <div class="text-center text-muted py-5">
                <i class="fas fa-calendar-check fa-3x mb-3 text-gray-300"></i>
                <p class="mb-0"><?= $status === 'due' ? 'Tidak ada jadwal yang perlu dikerjakan. 👍' : 'Belum ada jadwal di daftar ini.'; ?></p>
            </div>
        <?php else : ?>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Jadwal</th>
                            <th>Aset</th>
                            <th>Lokasi</th>
                            <th>Jatuh tempo</th>
                            <th>Terakhir</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($schedules as $s) : ?>
                            <tr class="<?= (int) $s['is_active'] === 0 ? 'text-muted' : ''; ?>">
                                <td>
                                    <b><?= html_escape($s['title']); ?></b>
                                    <div class="small text-muted"><?= maintenance_interval_label($s['interval_value'], $s['interval_unit']); ?><?= $s['notes'] ? ' · ' . html_escape($s['notes']) : ''; ?></div>
                                </td>
                                <td>
                                    <?= html_escape($s['asset_name']); ?>
                                    <div><a href="<?= base_url('scan/' . rawurlencode($s['serial_number'])); ?>" target="_blank"><code class="small"><?= html_escape($s['serial_number']); ?></code></a></div>
                                </td>
                                <td><?= html_escape($s['room_name']); ?><div class="small text-muted"><?= html_escape($s['building_name']); ?></div></td>
                                <td class="text-nowrap">
                                    <?= date('d M Y', strtotime($s['next_due_date'])); ?>
                                    <div><span class="badge badge-<?= $s['due']['color']; ?>"><?= $s['due']['label']; ?></span></div>
                                </td>
                                <td class="small text-nowrap"><?= $s['last_done_date'] ? date('d M Y', strtotime($s['last_done_date'])) : '<span class="text-muted">belum pernah</span>'; ?></td>
                                <td class="text-right text-nowrap">
                                    <?php if ((int) $s['is_active'] === 1) : ?>
                                        <button type="button" class="btn btn-success btn-sm js-record" data-mode="schedule"
                                            data-token="<?= html_escape($s['token']); ?>"
                                            data-title="<?= html_escape($s['title'] . ' — ' . $s['asset_name'] . ' (' . $s['serial_number'] . ')'); ?>">
                                            <i class="fas fa-check mr-1"></i> Catat servis
                                        </button>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-light btn-sm js-edit" title="Ubah jadwal"
                                        data-token="<?= html_escape($s['token']); ?>"
                                        data-title="<?= html_escape($s['title']); ?>"
                                        data-asset="<?= html_escape($s['asset_name'] . ' (' . $s['serial_number'] . ')'); ?>"
                                        data-interval-value="<?= (int) $s['interval_value']; ?>"
                                        data-interval-unit="<?= html_escape($s['interval_unit']); ?>"
                                        data-next-due="<?= html_escape($s['next_due_date']); ?>"
                                        data-remind="<?= (int) $s['remind_days_before']; ?>"
                                        data-notes="<?= html_escape($s['notes'] ?? ''); ?>"><i class="fas fa-pen"></i></button>
                                    <form method="post" action="<?= base_url('maintenance/toggle_schedule'); ?>" class="d-inline">
                                        <input type="hidden" name="token" value="<?= html_escape($s['token']); ?>">
                                        <input type="hidden" name="back" value="<?= html_escape($back); ?>">
                                        <button type="submit" class="btn btn-light btn-sm" title="<?= (int) $s['is_active'] === 1 ? 'Nonaktifkan' : 'Aktifkan'; ?>">
                                            <i class="fas <?= (int) $s['is_active'] === 1 ? 'fa-pause' : 'fa-play'; ?>"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal tambah / ubah jadwal -->
<div class="modal fade" id="scheduleModal" role="dialog" aria-labelledby="scheduleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scheduleModalLabel">Tambah Jadwal Maintenance</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form method="post" action="<?= base_url('maintenance/store_schedule'); ?>" id="scheduleForm">
                <input type="hidden" name="token" value="">
                <input type="hidden" name="back" value="<?= html_escape($back); ?>">
                <div class="modal-body">
                    <div class="form-group js-asset-select">
                        <label for="schedule_asset">Aset</label>
                        <select class="form-control js-asset" id="schedule_asset" name="asset_id" style="width: 100%;">
                            <option value="">-- Pilih aset --</option>
                            <?php foreach ($assets as $a) : ?>
                                <option value="<?= $a['id_asset']; ?>"><?= html_escape($a['asset_name'] . ' — ' . $a['serial_number'] . ' (' . $a['room_name'] . ', ' . $a['building_name'] . ')'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <p class="font-weight-bold text-gray-800 js-asset-label d-none"></p>
                    <div class="form-group">
                        <label for="schedule_title">Nama perawatan</label>
                        <input type="text" class="form-control" id="schedule_title" name="title" maxlength="100" required placeholder="mis. Servis & cuci AC">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-6">
                            <label for="interval_value">Setiap</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="interval_value" name="interval_value" min="1" max="365" value="3" required>
                                <select class="custom-select" name="interval_unit" id="interval_unit">
                                    <?php foreach ($units as $value => $label) : ?>
                                        <option value="<?= $value; ?>" <?= $value === 'month' ? 'selected' : ''; ?>><?= $label; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-6">
                            <label for="next_due_date">Jatuh tempo berikutnya</label>
                            <input type="date" class="form-control" id="next_due_date" name="next_due_date" required value="<?= date('Y-m-d', strtotime('+1 month')); ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-6">
                            <label for="remind_days_before">Ingatkan (hari sebelum)</label>
                            <input type="number" class="form-control" id="remind_days_before" name="remind_days_before" min="0" max="365" value="7">
                        </div>
                        <div class="form-group col-6">
                            <label for="schedule_notes">Catatan</label>
                            <input type="text" class="form-control" id="schedule_notes" name="notes" maxlength="255" placeholder="opsional">
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

<?php $this->load->view('maintenance/_record_modal', ['assets' => $assets, 'types' => $types, 'back' => $back]); ?>

<script>
    $(function() {
        const storeUrl = <?= json_encode(base_url('maintenance/store_schedule')); ?>;
        const updateUrl = <?= json_encode(base_url('maintenance/update_schedule')); ?>;
        const $modal = $('#scheduleModal');
        const $form = $('#scheduleForm');

        if ($.fn.select2) {
            $('.js-asset').each(function() {
                $(this).select2({
                    theme: 'bootstrap',
                    dropdownParent: $(this).closest('.modal')
                });
            });
        }

        // Tombol "Tambah Jadwal": kosongkan form
        $('[data-target="#scheduleModal"]').on('click', function() {
            $form.attr('action', storeUrl)[0].reset();
            $form.find('[name="token"]').val('');
            $('#scheduleModalLabel').text('Tambah Jadwal Maintenance');
            $form.find('.js-asset-select').removeClass('d-none');
            $form.find('.js-asset-label').addClass('d-none');
            $('#schedule_asset').val('').trigger('change');
        });

        $('.js-edit').on('click', function() {
            const d = $(this).data();
            $form.attr('action', updateUrl);
            $form.find('[name="token"]').val(d.token);
            $('#scheduleModalLabel').text('Ubah Jadwal Maintenance');
            $form.find('.js-asset-select').addClass('d-none');
            $form.find('.js-asset-label').removeClass('d-none').text(d.asset);
            $('#schedule_title').val(d.title);
            $('#interval_value').val(d.intervalValue);
            $('#interval_unit').val(d.intervalUnit);
            $('#next_due_date').val(d.nextDue);
            $('#remind_days_before').val(d.remind);
            $('#schedule_notes').val(d.notes);
            $modal.modal('show');
        });
    });
</script>
