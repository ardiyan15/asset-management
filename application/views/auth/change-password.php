<main class="auth-single">
    <section class="auth-card enter" aria-labelledby="changeTitle">
        <a class="auth-brand" href="<?= base_url('auth'); ?>">
            <span class="auth-brand-mark"><img src="<?= base_url('assets/img/logo_raharja.png'); ?>" alt="" width="26" height="26"></span>
            <span class="auth-brand-text">
                <strong>SIMANIS</strong>
                <span>Manajemen Aset</span>
            </span>
        </a>
        <h1 id="changeTitle" class="auth-title">Buat password baru</h1>
        <p class="auth-lede">Untuk akun <strong><?= html_escape($this->session->userdata('reset_email')); ?></strong>.</p>

        <?php if ($this->session->flashdata('message')) : ?>
            <div class="auth-flash" role="alert"><?= $this->session->flashdata('message'); ?></div>
        <?php endif; ?>

        <form action="<?= base_url('auth/changePassword'); ?>" method="post" class="auth-form">
            <div class="form-group">
                <label for="password1">Password baru</label>
                <input type="password" class="form-control" id="password1" name="password1" autocomplete="new-password" required>
                <?= form_error('password1', '<small class="field-error">', '</small>'); ?>
            </div>
            <div class="form-group">
                <label for="password2">Ulangi password baru</label>
                <input type="password" class="form-control" id="password2" name="password2" autocomplete="new-password" required>
                <?= form_error('password2', '<small class="field-error">', '</small>'); ?>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg">Simpan password</button>
        </form>
    </section>
</main>
