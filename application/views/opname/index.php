<div class="container-fluid">
    <div class="flash-data" data-flashdata="<?= $this->session->flashdata('message'); ?>"></div>
    <div class="card rounded p-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h3 text-gray-800 mb-1"><?= $title; ?></h1>
                <p class="text-muted mb-0 small">Audit fisik aset per ruangan: scan label QR, sistem mencatat aset yang ditemukan, hilang, atau salah ruangan.</p>
            </div>
            <button type="button" class="btn btn-primary btn-sm mt-2 mt-md-0" data-toggle="modal" data-target="#startOpname">
                <i class="fas fa-clipboard-check mr-1"></i> Mulai Opname
            </button>
        </div>

        <?php if (empty($list)) : ?>
            <div class="text-center text-muted py-5">
                <i class="fas fa-clipboard-list fa-3x mb-3 text-gray-300"></i>
                <p class="mb-0">Belum ada stock opname. Klik <b>Mulai Opname</b> untuk mengaudit ruangan.</p>
            </div>
        <?php else : ?>
            <div class="table-responsive">
                <table class="table table-sm table-hover" id="opnameTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Ruangan</th>
                            <th>Petugas</th>
                            <th>Mulai</th>
                            <th>Status</th>
                            <th class="text-center">Ditemukan</th>
                            <th class="text-center">Tidak Ditemukan</th>
                            <th class="text-center">Salah Ruangan</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($list as $i => $row) : ?>
                            <?php $open = (int) $row['status'] === 0; ?>
                            <tr>
                                <td><?= $i + 1; ?></td>
                                <td><a href="<?= base_url('opname/detail/' . $row['token']); ?>"><?= html_escape($row['code']); ?></a></td>
                                <td>
                                    <b><?= html_escape($row['room_name']); ?></b>
                                    <div class="small text-muted"><?= html_escape($row['building_name'] . ' · ' . $row['floor_name']); ?></div>
                                </td>
                                <td><?= html_escape($row['username'] ?? '-'); ?></td>
                                <td><?= date('d M Y H:i', strtotime($row['started_at'])); ?></td>
                                <td>
                                    <?php if ($open) : ?>
                                        <span class="badge badge-warning">Berjalan</span>
                                    <?php else : ?>
                                        <span class="badge badge-success">Selesai</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center"><?= (int) $row['total_found']; ?> / <?= (int) $row['total_expected']; ?></td>
                                <td class="text-center <?= !$open && $row['total_missing'] > 0 ? 'text-danger font-weight-bold' : ''; ?>"><?= $open ? '-' : (int) $row['total_missing']; ?></td>
                                <td class="text-center <?= $row['total_wrong_room'] > 0 ? 'text-warning font-weight-bold' : ''; ?>"><?= (int) $row['total_wrong_room']; ?></td>
                                <td class="text-right text-nowrap">
                                    <a href="<?= base_url('opname/detail/' . $row['token']); ?>" class="btn btn-sm <?= $open ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                        <?= $open ? '<i class="fas fa-barcode mr-1"></i> Lanjutkan' : 'Detail'; ?>
                                    </a>
                                    <?php if (!$open) : ?>
                                        <a href="<?= base_url('opname/print_report/' . $row['token']); ?>" target="_blank" class="btn btn-sm btn-outline-dark" title="Berita acara"><i class="fas fa-file-pdf"></i></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="startOpname" role="dialog" aria-labelledby="startOpnameLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="startOpnameLabel">Mulai Stock Opname</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form method="post" action="<?= base_url('opname/start'); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="opname_room">Ruangan yang diaudit</label>
                        <select class="form-control" id="opname_room" name="room_id" required>
                            <option value="">-- Pilih Ruangan --</option>
                            <?php foreach ($rooms as $room) : ?>
                                <option value="<?= $room['id']; ?>">
                                    <?= html_escape($room['name'] . ' — ' . $room['building_name'] . ' / ' . $room['floor_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">Semua aset yang tercatat di ruangan ini akan jadi daftar periksa.</small>
                    </div>
                    <div class="form-group mb-0">
                        <label for="opname_notes">Catatan <span class="text-muted">(opsional)</span></label>
                        <textarea class="form-control" id="opname_notes" name="notes" rows="2" maxlength="500" placeholder="mis. Opname rutin semester ganjil"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-play mr-1"></i> Mulai</button>
                </div>
            </form>
        </div>
    </div>
</div>
