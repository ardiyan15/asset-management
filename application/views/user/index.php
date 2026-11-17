<div class="container-fluid">
    <div class="row">
        <div class="col-md-5">
            <?= $this->session->flashdata('message'); ?>
        </div>
    </div>
    <div class="card mb-3 shadow-sm p-3 mb-5 bg-white rounded" style="max-width: 540px;">
        <div class="col-md-6">
            <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>
        </div>
        <div class="row no-gutters">
            <div class="col-md-4">
                <img src="<?= base_url('assets/img/profile/') . $user['image']; ?>" class="card-img" height="99%"
                    name="image">
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <h5 class="card-title"><?= $user['username']; ?></h5>
                    <p class="card-text"><small class="text-muted">Terdaftar sejak <?= $user['created']; ?></small></p>
                    <?php if (!empty($user['email'])) : ?>
                        <p class="card-text small mb-0"><i class="fas fa-envelope mr-1 text-muted"></i><?= html_escape($user['email']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm p-3 mb-5 bg-white rounded" style="max-width: 760px;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h2 class="h6 font-weight-bold text-gray-800 mb-0">Aktivitas saya</h2>
            <span class="small text-muted">15 terakhir</span>
        </div>
        <?php if (empty($activities)) : ?>
            <p class="small text-muted mb-0">Belum ada aktivitas tercatat.</p>
        <?php else : ?>
            <ul class="list-unstyled mb-0">
                <?php foreach ($activities as $act) : ?>
                    <li class="d-flex py-2 border-bottom small">
                        <span class="text-muted text-nowrap mr-3" style="min-width: 8.5rem;"><?= date('d M Y H:i', strtotime($act['created_at'])); ?></span>
                        <span class="flex-grow-1">
                            <?= html_escape($act['description']); ?>
                            <?php if ($act['entity_label']) : ?><span class="text-muted">· <?= html_escape($act['entity_label']); ?></span><?php endif; ?>
                        </span>
                        <?php if ($act['action'] === 'auth.login' && $act['ip_address']) : ?>
                            <span class="text-muted ml-2 text-nowrap"><?= html_escape($act['ip_address']); ?></span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p class="small text-muted mt-2 mb-0">Kalau ada login atau aktivitas yang bukan dari Anda, segera ganti password dan hubungi admin.</p>
        <?php endif; ?>
    </div>
</div>
</div>