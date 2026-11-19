<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| Contoh pengaturan library Email (pengingat maintenance/peminjaman & notifikasi kiriman masuk).
| Salin file ini menjadi application/config/email.php lalu pilih SALAH SATU blok di bawah.
| email.php sudah ada di .gitignore, jangan commit password SMTP.
|
| Tanpa email.php, CI memakai fungsi mail() PHP. Di Laragon itu diteruskan ke
| bin/sendmail (Mail Catcher): email tidak terkirim, tapi tersimpan sebagai .eml di
| C:\laragon\bin\sendmail\output dan muncul notifikasi di tray. Cukup untuk demo lokal.
*/

// --- 1. Mailpit / Mailtrap (email ditangkap, tidak terkirim; cocok untuk demo) ---------
// Mailpit lokal: jalankan mailpit.exe, inbox di http://localhost:8025
$config['protocol']    = 'smtp';
$config['smtp_host']   = 'localhost';
$config['smtp_port']   = 1025;
$config['smtp_user']   = '';
$config['smtp_pass']   = '';
$config['smtp_crypto'] = '';
// Mailtrap Email Testing: host sandbox.smtp.mailtrap.io, port 2525, user/pass dari inbox Mailtrap.

// --- 2. Brevo (gratis ~300 email/hari, email benar-benar terkirim) ----------------------
// $config['protocol']    = 'smtp';
// $config['smtp_host']   = 'smtp-relay.brevo.com';
// $config['smtp_port']   = 587;
// $config['smtp_user']   = 'login-smtp@smtp-brevo.com';   // Brevo > SMTP & API > SMTP
// $config['smtp_pass']   = 'smtp-key-di-sini';
// $config['smtp_crypto'] = 'tls';
// Isi $config['notification_mail_from'] di config.php dengan alamat pengirim yang sudah diverifikasi di Brevo.

// --- 3. Gmail (aktifkan 2FA lalu buat App Password, bukan password akun) ----------------
// $config['protocol']    = 'smtp';
// $config['smtp_host']   = 'ssl://smtp.gmail.com';
// $config['smtp_port']   = 465;
// $config['smtp_user']   = 'alamat-email@contoh.com';
// $config['smtp_pass']   = 'app-password-di-sini';

$config['smtp_timeout'] = 10;
$config['mailtype']    = 'html';
$config['charset']     = 'utf-8';
$config['newline']     = "\r\n";
$config['crlf']        = "\r\n";
