<main class="auth-single">
    <section class="auth-card enter" aria-labelledby="forgotTitle">
        <a class="auth-brand" href="<?= base_url('auth'); ?>">
            <span class="auth-brand-mark"><img src="<?= base_url('assets/img/logo_raharja.png'); ?>" alt="" width="26" height="26"></span>
            <span class="auth-brand-text">
                <strong>SIMANIS</strong>
                <span>Manajemen Aset</span>
            </span>
        </a>
        <h1 id="forgotTitle" class="auth-title">Atur ulang password</h1>
        <p class="auth-lede">Masukkan email akun Anda. Kami kirim tautan untuk membuat password baru.</p>

        <?php if ($this->session->flashdata('message')) : ?>
            <div class="auth-flash" role="alert"><?= $this->session->flashdata('message'); ?></div>
        <?php endif; ?>

        <form action="<?= base_url('auth/forgotpassword'); ?>" method="post" class="auth-form">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= set_value('email'); ?>" autocomplete="email" required>
                <?= form_error('email', '<small class="field-error">', '</small>'); ?>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg">Kirim tautan reset</button>
        </form>
        <p class="auth-help"><a href="<?= base_url('auth') ?>"><i class="fas fa-arrow-left" aria-hidden="true"></i> Kembali ke halaman masuk</a></p>
    </section>
</main>
