<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| Contoh pengaturan SMTP untuk library Email (dipakai pengingat maintenance).
| Salin file ini menjadi application/config/email.php lalu isi kredensialnya.
| email.php sudah ada di .gitignore, jangan commit password SMTP.
|
| Contoh Gmail: aktifkan 2FA lalu buat App Password (bukan password akun).
*/
$config['protocol']    = 'smtp';
$config['smtp_host']   = 'ssl://smtp.gmail.com';
$config['smtp_port']   = 465;
$config['smtp_user']   = 'alamat-email@contoh.com';
$config['smtp_pass']   = 'app-password-di-sini';
$config['smtp_timeout'] = 10;
$config['mailtype']    = 'html';
$config['charset']     = 'utf-8';
$config['newline']     = "\r\n";
$config['crlf']        = "\r\n";
