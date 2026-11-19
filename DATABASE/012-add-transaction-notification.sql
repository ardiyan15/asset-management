-- Notifikasi transaksi masuk.
--
-- created_by  : user yang membuat transaksi pindah (tidak ikut dikirimi email notifikasinya sendiri)
-- notified_at : kapan email "ada kiriman masuk" berhasil dikirim ke gedung tujuan.
--               NULL = belum terkirim; `php index.php cron transaction_notifications` mencoba ulang
--               selama kiriman masih menunggu diterima.

ALTER TABLE simanis.`transaction`
	ADD COLUMN created_by INT NULL AFTER notes,
	ADD COLUMN notified_at DATETIME NULL AFTER created_by,
	ADD KEY transaction_notified_idx (notified_at);

-- Transaksi lama dianggap sudah diberitahukan supaya cron tidak mengirim email untuk data lama.
UPDATE simanis.`transaction` SET notified_at = COALESCE(created_at, NOW()) WHERE notified_at IS NULL;
