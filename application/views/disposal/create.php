<div class="container-fluid">
    <div class="flash-data" data-flashdata="<?= $this->session->flashdata('message'); ?>"></div>
    <a href="<?= base_url('disposal'); ?>" class="small d-inline-block mb-2"><i class="fas fa-arrow-left mr-1"></i> Daftar pengajuan</a>

    <form method="post" action="<?= base_url('disposal/store'); ?>" id="disposalForm">
        <div class="card rounded p-3 mb-3">
            <h1 class="h4 text-gray-800 mb-1"><?= $title; ?></h1>
            <p class="small text-muted mb-3">
                Hanya aset dengan kondisi <span class="badge badge-danger">Rusak Berat</span> yang sedang berada di ruangan
                (tidak sedang dipindahkan) dan belum pernah diajukan yang muncul di daftar ini.
                Ubah kondisi aset dulu di <a href="<?= base_url('asset'); ?>">Data Asset</a> kalau aset yang dicari belum ada.
            </p>

            <?php if (empty($assets)) : ?>
                <div class="text-center text-muted py-4">
                    <i class="fas fa-check-circle fa-3x mb-3 text-gray-300"></i>
                    <p class="mb-0">Tidak ada aset Rusak Berat yang bisa diajukan.</p>
                </div>
            <?php else : ?>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width: 2rem;"><input type="checkbox" id="checkAll" aria-label="Pilih semua"></th>
                                <th>Nama</th>
                                <th>Merk</th>
                                <th>Nomor Seri</th>
                                <th>Lokasi</th>
                                <th>Kondisi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($assets as $asset) : ?>
                                <tr>
                                    <td><input type="checkbox" class="asset-check" name="asset_ids[]" value="<?= $asset['id_asset']; ?>" <?= $selected === (int) $asset['id_asset'] ? 'checked' : ''; ?>></td>
                                    <td><?= html_escape($asset['asset_name']); ?></td>
                                    <td><?= html_escape($asset['merk']); ?></td>
                                    <td><code><?= html_escape($asset['serial_number']); ?></code></td>
                                    <td>
                                        <?= html_escape($asset['room_name']); ?>
                                        <div class="small text-muted"><?= html_escape($asset['building_name'] . ' · ' . $asset['floor_name']); ?></div>
                                    </td>
                                    <td><?= asset_condition_badge($asset['asset_condition']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($assets)) : ?>
            <div class="card rounded p-3 mb-4">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="method">Metode penghapusan</label>
                        <select class="form-control" id="method" name="method" required>
                            <?php foreach ($methods as $value => $label) : ?>
                                <option value="<?= $value; ?>"><?= $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-8">
                        <label for="reason">Alasan penghapusan</label>
                        <textarea class="form-control" id="reason" name="reason" rows="2" maxlength="2000" required placeholder="mis. Kompresor rusak total, biaya perbaikan melebihi nilai aset"></textarea>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="small text-muted"><b id="selectedCount">0</b> aset dipilih</span>
                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-paper-plane mr-1"></i> Kirim Pengajuan</button>
                </div>
            </div>
        <?php endif; ?>
    </form>
</div>

<script>
    (function() {
        function refreshCount() {
            $('#selectedCount').text($('.asset-check:checked').length);
        }
        $('#checkAll').on('change', function() {
            $('.asset-check').prop('checked', $(this).is(':checked'));
            refreshCount();
        });
        $('.asset-check').on('change', refreshCount);
        refreshCount();

        $('#disposalForm').on('submit', function(e) {
            if ($('.asset-check:checked').length === 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'info',
                    title: 'Belum ada aset dipilih',
                    text: 'Centang aset yang ingin diajukan untuk dihapus.'
                });
            }
        });
    })();
</script>
