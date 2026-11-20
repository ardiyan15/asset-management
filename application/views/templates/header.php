<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="SIMANIS — pencatatan lokasi, kondisi, dan perpindahan aset per gedung.">
    <meta name="theme-color" content="#111517">

    <title><?= html_escape($title); ?> · SIMANIS</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/logo_raharja.png') ?>">

    <script src="<?= base_url('assets/'); ?>vendor/jquery/jquery.min.js"></script>

    <!-- Ikon & huruf -->
    <link href="<?= base_url('assets/'); ?>vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.css" integrity="sha512-CbQfNVBSMAYmnzP3IC+mZZmYMP2HUnVkV4+PwuhpiMUmITtSpS7Prr3fNncV1RBOnWxzz4pYQ5EAGG4ck46Oig==" crossorigin="anonymous" />

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">

    <!-- Kerangka SB Admin 2 + tema SIMANIS (tema harus dimuat terakhir) -->
    <link href="<?= base_url('assets/'); ?>css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/'); ?>css/animate.css">
    <link href="<?= base_url('assets/'); ?>css/simanis.css?v=<?= @filemtime(FCPATH . 'assets/css/simanis.css'); ?>" rel="stylesheet">

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
    <script>
        // Palet grafik yang sama dengan tema (dipakai dashboard, laporan, dll.)
        window.SIMANIS_COLORS = {
            accent: '#0f5f73', success: '#2e7d4f', warning: '#c98a1b', danger: '#b4321f',
            info: '#3d6596', muted: '#9a9da3', line: '#e4e1d8', ink: '#17191c',
            series: ['#0f5f73', '#c98a1b', '#2e7d4f', '#b4321f', '#3d6596', '#7a8a3a', '#8c5a3c', '#9a9da3']
        };
        if (window.Chart) {
            Chart.defaults.global.defaultFontFamily = "'Geist', ui-sans-serif, system-ui, sans-serif";
            Chart.defaults.global.defaultFontColor = '#6b6f77';
            Chart.defaults.global.defaultFontSize = 12;
            Chart.defaults.global.legend.labels.boxWidth = 10;
            Chart.defaults.global.tooltips.backgroundColor = '#17191c';
            Chart.defaults.global.tooltips.titleFontStyle = '600';
            Chart.defaults.global.tooltips.xPadding = 10;
            Chart.defaults.global.tooltips.yPadding = 8;
            Chart.defaults.global.tooltips.cornerRadius = 6;
        }
    </script>

</head>

<body id="page-top">
    <a class="skip-link" href="#content">Lewati ke konten</a>

    <!-- Page Wrapper -->
    <div id="wrapper">
