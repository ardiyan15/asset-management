<div class="container-fluid">
    <div class="flash-data" data-flashdata="<?= $this->session->flashdata('message'); ?>"></div>
    <div class="card rounded p-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h3 text-gray-800 mb-1"><?= $title; ?></h1>
                <p class="text-muted mb-0 small">Aset berkondisi <b>Rusak Berat</b> bisa diajukan untuk dihapus. Admin menyetujui atau menolak per aset, lalu BAST penghapusan bisa dicetak.</p>
            </div>
            <a href="<?= base_url('disposal/create'); ?>" class="btn btn-danger btn-sm mt-2 mt-md-0">
                <i class="fas fa-trash-alt mr-1"></i> Ajukan Penghapusan
            </a>
        </div>

        <?php if (empty($list)) : ?>
            <div class="text-center text-muted py-5">
                <i class="fas fa-recycle fa-3x mb-3 text-gray-300"></i>
                <p class="mb-0">Belum ada pengajuan penghapusan aset.</p>
            </div>
        <?php else : ?>
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Tanggal</th>
                            <th>Gedung</th>
                            <th>Metode</th>
                            <th>Diajukan oleh</th>
                            <th class="text-center">Aset</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($list as $i => $row) : ?>
                            <tr>
                                <td><?= $i + 1; ?></td>
                                <td><a href="<?= base_url('disposal/detail/' . $row['token']); ?>"><?= html_escape($row['code']); ?></a></td>
                                <td class="text-nowrap"><?= date('d M Y', strtotime($row['created_at'])); ?></td>
                                <td><?= html_escape($row['building_names'] ?: '-'); ?></td>
                                <td><?= html_escape(Disposal_model::method_label($row['method'])); ?></td>
                                <td><?= html_escape($row['requested_by_name'] ?? '-'); ?></td>
                                <td class="text-center"><?= (int) $row['total_items']; ?></td>
                                <td class="text-nowrap">
                                    <?php if ($row['total_pending'] > 0) : ?>
                                        <span class="badge badge-warning"><?= (int) $row['total_pending']; ?> menunggu</span>
                                    <?php endif; ?>
                                    <?php if ($row['total_approved'] > 0) : ?>
                                        <span class="badge badge-success"><?= (int) $row['total_approved']; ?> disetujui</span>
                                    <?php endif; ?>
                                    <?php if ($row['total_rejected'] > 0) : ?>
                                        <span class="badge badge-secondary"><?= (int) $row['total_rejected']; ?> ditolak</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right text-nowrap">
                                    <?php if ($user['role_id'] === '1' && $row['total_pending'] > 0) : ?>
                                        <a href="<?= base_url('disposal/detail/' . $row['token']); ?>" class="btn btn-sm btn-primary">Tinjau</a>
                                    <?php else : ?>
                                        <a href="<?= base_url('disposal/detail/' . $row['token']); ?>" class="btn btn-sm btn-outline-primary">Detail</a>
                                    <?php endif; ?>
                                    <?php if ($row['total_approved'] > 0) : ?>
                                        <a href="<?= base_url('disposal/print_bast/' . $row['token']); ?>" target="_blank" class="btn btn-sm btn-outline-dark" title="BAST penghapusan"><i class="fas fa-file-pdf"></i></a>
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
