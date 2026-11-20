<?php
// QR asli untuk label contoh di panel kanan (mengarah ke aplikasi ini).
$demo_qr = null;
try {
    $ci_qr = &get_instance();
    $ci_qr->load->library('qr');
    $demo_qr = $ci_qr->qr->png_data_uri(base_url(), 4);
} catch (Throwable $e) {
    $demo_qr = null;
}
?>
<main class="auth-shell">

    <!-- Panel form -->
    <section class="auth-panel" aria-labelledby="loginTitle">
        <div class="auth-panel-inner enter" style="--d: 80ms">
            <a class="auth-brand" href="<?= base_url('auth'); ?>">
                <span class="auth-brand-mark"><img src="<?= base_url('assets/img/logo_simanis.png'); ?>" alt="" width="26" height="26"></span>
                <span class="auth-brand-text">
                    <strong>SIMANIS</strong>
                    <span>Manajemen Aset</span>
                </span>
            </a>

            <h1 id="loginTitle" class="auth-title">Masuk ke register aset</h1>
            <p class="auth-lede">Pakai akun yang dibuat admin untuk gedung Anda.</p>

            <?php if ($this->session->flashdata('message')) : ?>
                <div class="auth-flash" role="alert">
                    <?= $this->session->flashdata('message'); ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('auth'); ?>" method="post" class="auth-form" id="loginForm" novalidate>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control<?= form_error('username') ? ' is-invalid' : ''; ?>" id="username" name="username" value="<?= set_value('username'); ?>" autocomplete="username" autocapitalize="none" spellcheck="false" autofocus required>
                    <?= form_error('username', '<small class="field-error">', '</small>'); ?>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="pw-field">
                        <input type="password" class="form-control<?= form_error('password') ? ' is-invalid' : ''; ?>" id="password" name="password" autocomplete="current-password" required>
                        <button type="button" class="pw-toggle" id="pwToggle" aria-label="Tampilkan password" aria-pressed="false">
                            <i class="far fa-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                    <small class="caps-hint" id="capsHint" hidden><i class="fas fa-arrow-up" aria-hidden="true"></i> Caps Lock aktif</small>
                    <?= form_error('password', '<small class="field-error">', '</small>'); ?>
                </div>
                <button type="submit" class="btn btn-primary btn-block btn-lg" id="loginSubmit">
                    <span class="label">Masuk</span>
                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                </button>
            </form>

            <p class="auth-help">Lupa password atau akun belum aktif? Hubungi admin SIMANIS di gedung Anda.</p>
        </div>
    </section>

    <!-- Panel produk: label aset seperti yang tertempel di barang -->
    <section class="auth-showcase" aria-label="Tentang SIMANIS">
        <div class="auth-showcase-inner">
            <p class="showcase-kicker enter" style="--d: 0ms">Register aset per gedung</p>
            <p class="showcase-title enter" style="--d: 60ms">Setiap barang punya label, lokasi, dan riwayat yang bisa dilacak.</p>

            <div class="tag-stack enter" style="--d: 160ms" aria-hidden="true">
                <div class="tag tag-ghost"></div>
                <article class="tag">
                    <span class="tag-hole"></span>
                    <header class="tag-head">
                        <span class="tag-org">SIMANIS · Label aset</span>
                        <span class="tag-cond"><span class="dot"></span>Baik</span>
                    </header>
                    <div class="tag-body">
                        <div class="tag-qr">
                            <?php if ($demo_qr) : ?>
                                <img src="<?= $demo_qr; ?>" alt="" width="96" height="96">
                            <?php else : ?>
                                <i class="fas fa-qrcode"></i>
                            <?php endif; ?>
                        </div>
                        <div class="tag-info">
                            <span class="tag-name">Proyektor Epson EB-X51</span>
                            <span class="tag-serial">R301PRJ261100042</span>
                            <span class="tag-path">Gedung A <i class="fas fa-chevron-right"></i> Lantai 3 <i class="fas fa-chevron-right"></i> <b>R.301</b></span>
                        </div>
                    </div>
                    <ol class="tag-trail">
                        <li><span class="when">12 Nov</span><span class="what">Diterima di R.301</span><span class="st st-ok">Diterima</span></li>
                        <li><span class="when">10 Nov</span><span class="what">Dikirim dari Gudang Umum</span><span class="st">TRX/AST</span></li>
                        <li><span class="when">03 Nov</span><span class="what">Servis rutin selesai</span><span class="st">Maintenance</span></li>
                    </ol>
                </article>
            </div>

            <ul class="showcase-facts enter" style="--d: 260ms">
                <li><i class="fas fa-qrcode" aria-hidden="true"></i> Label QR untuk tiap aset</li>
                <li><i class="fas fa-route" aria-hidden="true"></i> Pindah antar ruangan dengan BAST</li>
                <li><i class="fas fa-clipboard-check" aria-hidden="true"></i> Stock opname per ruangan</li>
            </ul>
        </div>
    </section>
</main>

<script>
    (function () {
        var pw = document.getElementById('password');
        var toggle = document.getElementById('pwToggle');
        var caps = document.getElementById('capsHint');
        var form = document.getElementById('loginForm');
        var submit = document.getElementById('loginSubmit');

        toggle.addEventListener('click', function () {
            var show = pw.type === 'password';
            pw.type = show ? 'text' : 'password';
            toggle.setAttribute('aria-pressed', show ? 'true' : 'false');
            toggle.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
            toggle.querySelector('i').className = show ? 'far fa-eye-slash' : 'far fa-eye';
            pw.focus();
        });

        ['keydown', 'keyup'].forEach(function (ev) {
            pw.addEventListener(ev, function (e) {
                if (e.getModifierState) caps.hidden = !e.getModifierState('CapsLock');
            });
        });

        form.addEventListener('submit', function () {
            submit.disabled = true;
            submit.querySelector('.label').textContent = 'Memeriksa…';
        });
    })();
</script>
