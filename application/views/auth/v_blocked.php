<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="theme-color" content="#111517">
    <title>Akses ditolak · SIMANIS</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/logo_simanis.png') ?>">
    <link href="<?= base_url('assets/'); ?>vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="<?= base_url('assets/'); ?>css/sb-admin-2.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/'); ?>css/simanis.css?v=<?= @filemtime(FCPATH . 'assets/css/simanis.css'); ?>" rel="stylesheet">
</head>
<body class="auth-body">
    <main class="auth-single">
        <section class="auth-card blocked-card enter" aria-labelledby="blockedTitle">
            <span class="blocked-code">403</span>
            <h1 id="blockedTitle" class="auth-title">Halaman ini khusus admin</h1>
            <p class="auth-lede">Akun Anda tidak punya hak akses ke halaman tersebut. Percobaan ini tercatat di audit log.</p>
            <a class="btn btn-primary btn-block btn-lg" href="<?= base_url('admin'); ?>"><i class="fas fa-arrow-left" aria-hidden="true"></i> Kembali ke dashboard</a>
        </section>
    </main>
</body>
</html>
