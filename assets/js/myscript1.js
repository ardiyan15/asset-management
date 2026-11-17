const flashData = $(".flash-data").data("flashdata");

switch (flashData) {
	case "added ":
		Swal.fire({
			title: "Berhasil",
			text: "Berhasil menambahkan aset",
			icon: "success",
			showConfirmButton: true,
		});
		break;
	case "edited ":
		Swal.fire({
			title: "Berhasil",
			text: "Update Aset Berhasil",
			icon: "success",
			showConfirmButton: true,
		});
		break;
	case "failed ":
		Swal.fire({
			title: "Failed",
			text: "Operation failed",
			icon: "error",
			showConfirmButton: true,
		});
		break;
	case "activate":
		Swal.fire({
			title: "Berhasil",
			text: "User berhasil diaktifkan",
			icon: "success",
			showConfirmButton: true,
		});
		break;
	case "deactivate":
		Swal.fire({
			title: "Berhasil",
			text: "non-aktifkan user berhasil",
			icon: "success",
			showConfirmButton: true,
		});
		break;
	case "take out":
		Swal.fire({
			title: "Berhasil",
			text: "Berhasil memindahkan aset",
			icon: "success",
			showConfirmButton: true,
		});
		break;
	case "ti_success":
		Swal.fire({
			title: "Berhasil",
			text: "Terima aset berhasil",
			icon: "success",
			showConfirmButton: true,
		});
		break;
	case "move_failed":
		Swal.fire({
			title: "Gagal",
			text: "Aset gagal dipindahkan. Pastikan lokasi tujuan dipilih dan berbeda dari lokasi saat ini.",
			icon: "error",
			showConfirmButton: true,
		});
		break;
	case "ti_failed":
		Swal.fire({
			title: "Gagal",
			text: "Transaksi tidak ditemukan atau sudah diproses sebelumnya",
			icon: "error",
			showConfirmButton: true,
		});
		break;
	case "reject":
		Swal.fire({
			title: "Berhasil",
			text: "Aset ditolak",
			icon: "info",
			showConfirmButton: true,
		});
		break;
	case "success":
		Swal.fire({
			title: "Berhasil",
			text: "Bangunan berhasil ditambahkan",
			icon: "success",
			showConfirmButton: true,
		});
		break;
	case "edtStr":
		Swal.fire({
			title: "Success",
			text: "Edit Store Location Successfully",
			icon: "success",
			showConfirmButton: true,
		});
		break;
	case "deactStr":
		Swal.fire({
			title: "Success",
			text: "The Store has been deactivated",
			icon: "success",
			showConfirmButton: true,
		});
		break;
	case "actvtStr":
		Swal.fire({
			title: "Success",
			text: "The Store has been activated",
			icon: "success",
			showConfirmButton: true,
		});
		break;
	case "deleted":
		Swal.fire({
			title: "Success",
			text: "Delete asset successfully",
			icon: "success",
			showConfirmButton: true,
		});
		break;
	case "failed":
		Swal.fire({
			title: "Failed",
			text: "Cannot input the same serial number",
			icon: "error",
			showConfirmButton: true,
		});
		break;
	case "editBuilding":
		Swal.fire({
			title: "Berhasil",
			text: "Bangunan berhasil diubah",
			icon: "success",
			showConfirmButton: true,
		});
		break;
	case "addUser":
		Swal.fire({
			title: "Berhasil",
			text: "User berhasil ditambahkan",
			icon: "success",
			showConfirmButton: true,
		});
		break;
	case "addFloor":
		Swal.fire({
			title: "Berhasil",
			text: "Lantai berhasil ditambahkan",
			icon: "success",
			showConfirmButton: true,
		});
		break;
	case "addRoom":
		Swal.fire({
			title: "Berhasil",
			text: "Ruangan berhasil ditambahkan",
			icon: "success",
			showConfirmButton: true,
		});
		break;
	case "addCategory":
		Swal.fire({
			title: "Berhasil",
			text: "Kategori berhasil ditambahkan",
			icon: "success",
			showConfirmButton: true,
		});
		break;
	case "deleteCategory":
		Swal.fire({
			title: "Berhasil",
			text: "Kategori berhasil dihapus",
			icon: "success",
			showConfirmButton: true,
		});
		break;
	case "editCategory":
		Swal.fire({
			title: "Berhasil",
			text: "Kategori berhasil diubah",
			icon: "success",
			showConfirmButton: true,
		});
		break;
	case "deleteBuilding":
		Swal.fire({
			title: "Berhasil",
			text: "Bangunan berhasil dihapus",
			icon: "success",
			showConfirmButton: true,
		});
		break;
	case "editFloor":
		Swal.fire({
			title: "Berhasil",
			text: "Lantai berhasil diubah",
			icon: "success",
			showConfirmButton: true,
		});
		break;
	case "editRoom":
		Swal.fire({
			title: "Berhasil",
			text: "Ruangan berhasil diubah",
			icon: "success",
			showConfirmButton: true,
		});
		break;
	case "errorImport":
		Swal.fire({
			title: "Gagal",
			text: "File Tidak Ada / Format File Bukan CSV",
			icon: "error",
			showConfirmButton: true,
		});
		break;
	case "delete":
		Swal.fire({
			title: "Berhasil",
			text: "User Berhasil Dihapus",
			icon: "success",
			showConfirmButton: true,
		});
		break;
	case "opnameStarted":
		Swal.fire({
			title: "Opname dimulai",
			text: "Scan label QR atau centang aset yang ada di ruangan",
			icon: "success",
			showConfirmButton: true,
		});
		break;
	case "opname_exists":
		Swal.fire({
			title: "Opname masih berjalan",
			text: "Ruangan ini sudah punya opname yang belum selesai, lanjutkan sesi ini",
			icon: "info",
			showConfirmButton: true,
		});
		break;
	case "opnameFinished":
		Swal.fire({
			title: "Opname selesai",
			text: "Hasil opname tersimpan, berita acara siap dicetak",
			icon: "success",
			showConfirmButton: true,
		});
		break;
	case "opnameCanceled":
		Swal.fire({
			title: "Opname dibatalkan",
			text: "Sesi opname dan hasil scan-nya sudah dihapus",
			icon: "success",
			showConfirmButton: true,
		});
		break;
	case "opname_failed":
		Swal.fire({
			title: "Gagal",
			text: "Opname tidak ditemukan atau Anda tidak punya akses",
			icon: "error",
			showConfirmButton: true,
		});
		break;
	case "disposalRequested":
		Swal.fire({
			title: "Pengajuan terkirim",
			text: "Pengajuan penghapusan menunggu persetujuan admin",
			icon: "success",
			showConfirmButton: true,
		});
		break;
	case "disposalApproved":
		Swal.fire({
			title: "Disetujui",
			text: "Aset ditandai sudah dihapus, BAST penghapusan siap dicetak",
			icon: "success",
			showConfirmButton: true,
		});
		break;
	case "disposalRejected":
		Swal.fire({
			title: "Ditolak",
			text: "Pengajuan penghapusan aset ditolak",
			icon: "info",
			showConfirmButton: true,
		});
		break;
	case "disposal_invalid":
		Swal.fire({
			title: "Data belum lengkap",
			text: "Pilih aset yang memenuhi syarat dan isi alasan / metode penghapusan",
			icon: "warning",
			showConfirmButton: true,
		});
		break;
	case "disposal_nothing":
		Swal.fire({
			title: "Tidak ada yang diproses",
			text: "Aset yang dipilih sudah ditinjau sebelumnya",
			icon: "info",
			showConfirmButton: true,
		});
		break;
	case "disposal_no_approved":
		Swal.fire({
			title: "Belum ada yang disetujui",
			text: "BAST penghapusan baru bisa dicetak setelah ada aset yang disetujui",
			icon: "info",
			showConfirmButton: true,
		});
		break;
	case "disposal_failed":
		Swal.fire({
			title: "Gagal",
			text: "Pengajuan tidak ditemukan, Anda tidak punya akses, atau terjadi kesalahan",
			icon: "error",
			showConfirmButton: true,
		});
		break;
	case "purchase_invalid":
		Swal.fire({
			title: "Data pembelian tidak valid",
			text: "Cek tanggal perolehan (tidak boleh di masa depan), harga, dan garansi (butuh tanggal perolehan, maks. 240 bulan)",
			icon: "warning",
			showConfirmButton: true,
		});
		break;
	case "depreciation_invalid":
		Swal.fire({
			title: "Data penyusutan tidak valid",
			text: "Masa manfaat 1-50 tahun (atau kosong) dan nilai sisa 0-100%",
			icon: "warning",
			showConfirmButton: true,
		});
		break;
	case "maintenanceScheduled":
		Swal.fire({
			title: "Jadwal dibuat",
			text: "Jadwal maintenance tersimpan, pengingat akan muncul saat mendekati jatuh tempo",
			icon: "success",
			showConfirmButton: true,
		});
		break;
	case "maintenanceUpdated":
		Swal.fire({ title: "Berhasil", text: "Jadwal maintenance diperbarui", icon: "success", showConfirmButton: true });
		break;
	case "maintenanceActivated":
		Swal.fire({ title: "Berhasil", text: "Jadwal diaktifkan kembali", icon: "success", showConfirmButton: true });
		break;
	case "maintenanceDeactivated":
		Swal.fire({ title: "Berhasil", text: "Jadwal dinonaktifkan, tidak akan diingatkan lagi", icon: "info", showConfirmButton: true });
		break;
	case "maintenanceRecorded":
		Swal.fire({
			title: "Tercatat",
			text: "Pekerjaan maintenance tersimpan di riwayat",
			icon: "success",
			showConfirmButton: true,
		});
		break;
	case "maintenance_invalid":
		Swal.fire({
			title: "Data belum lengkap",
			text: "Cek aset, nama perawatan, interval, tanggal (tidak boleh di masa depan untuk servis), dan keterangan pekerjaan",
			icon: "warning",
			showConfirmButton: true,
		});
		break;
	case "maintenance_failed":
		Swal.fire({
			title: "Gagal",
			text: "Jadwal tidak ditemukan, Anda tidak punya akses, atau terjadi kesalahan",
			icon: "error",
			showConfirmButton: true,
		});
		break;
	case "loanCreated":
		Swal.fire({ title: "Peminjaman tercatat", text: "Jangan lupa cetak bukti peminjaman untuk ditandatangani", icon: "success", showConfirmButton: true });
		break;
	case "loanReturned":
		Swal.fire({ title: "Dikembalikan", text: "Pengembalian aset tersimpan", icon: "success", showConfirmButton: true });
		break;
	case "loanExtended":
		Swal.fire({ title: "Diperpanjang", text: "Batas kembali peminjaman diperbarui", icon: "success", showConfirmButton: true });
		break;
	case "loan_invalid":
		Swal.fire({
			title: "Data tidak valid",
			text: "Cek aset (harus tersedia), nama peminjam, tanggal pinjam (tidak di masa depan), dan batas kembali",
			icon: "warning",
			showConfirmButton: true,
		});
		break;
	case "loan_extend_invalid":
		Swal.fire({ title: "Tanggal tidak valid", text: "Batas kembali baru harus setelah batas sebelumnya dan tidak di masa lalu", icon: "warning", showConfirmButton: true });
		break;
	case "loan_failed":
		Swal.fire({ title: "Gagal", text: "Peminjaman tidak ditemukan, aset sudah dipinjam, atau Anda tidak punya akses", icon: "error", showConfirmButton: true });
		break;
	case "labelEmpty":
		Swal.fire({
			title: "Tidak ada label",
			text: "Tidak ada aset yang bisa dicetak labelnya",
			icon: "info",
			showConfirmButton: true,
		});
		break;
	case "editUser":
		Swal.fire({
			title: "Berhasil",
			text: "Berhasil mengubah data user",
			icon: "success",
			showConfirmButton: true,
		});
		break;
}

// Pop Up Delete
$(".delete-button").on("click", function (e) {
	e.preventDefault();
	const href = $(this).attr("href");

	Swal.fire({
		title: "Hapus Data",
		text: "Anda yakin ingin menghapus data?",
		icon: "question",
		showCancelButton: true,
		confirmButtonColor: "#3085d6",
		cancelButtonColor: "#d33",
		confirmButtonText: "Ya",
		cancelButtonText: "Tidak",
	}).then((result) => {
		if (result.value) {
			document.location.href = href;
		}
	});
});

$(".activate-user").on("click", function (e) {
	e.preventDefault();

	const href = $(this).attr("href");

	Swal.fire({
		title: "Hapus User?",
		text: "Anda yakin ingin menghapus user?",
		icon: "question",
		showCancelButton: true,
		confirmButtonColor: "#3085d6",
		cancelButtonColor: "#d33",
		confirmButtonText: "Yes",
	}).then((result) => {
		if (result.value) {
			document.location.href = href;
		}
	});
});

$(".deactivate-user").on("click", function (e) {
	e.preventDefault();

	const href = $(this).attr("href");

	Swal.fire({
		title: "Non-aktifkan user",
		text: "Anda yakin ingin menonaktifkan user?",
		icon: "question",
		showCancelButton: true,
		confirmButtonColor: "#3085d6",
		cancelButtonColor: "#d33",
		confirmButtonText: "Ya",
		cancelButtonText: "Tidak",
	}).then((result) => {
		if (result.value) {
			document.location.href = href;
		}
	});
});

$(".takein-button").on("click", function (e) {
	e.preventDefault();

	const href = $(this).attr("href");

	Swal.fire({
		title: "Terima Aset",
		text: "Anda yakin sudah menerima aset?",
		icon: "question",
		showCancelButton: true,
		confirmButtonColor: "#3085d6",
		cancelButtonColor: "#d33",
		confirmButtonText: "Ya",
		cancelButtonText: "Tidak",
	}).then((result) => {
		if (result.value) {
			document.location.href = href;
		}
	});
});

$(".reject-button").on("click", function (e) {
	e.preventDefault();

	const href = $(this).attr("href");

	Swal.fire({
		title: "Tolak?",
		text: "Anda tidak menerima aset?",
		icon: "question",
		showCancelButton: true,
		confirmButtonColor: "#3085d6",
		cancelButtonColor: "#d33",
		confirmButtonText: "Ya",
		cancelButtonText: "Tidak",
	}).then((result) => {
		if (result.value) {
			document.location.href = href;
		}
	});
});

$(".delete-building").on("click", function (e) {
	e.preventDefault();
	const href = $(this).attr("href");
	Swal.fire({
		title: "Hapus Bangunan",
		text: "Anda yakin ingin menghapus data bangunan?",
		icon: "question",
		showCancelButton: true,
		confirmButtonColor: "#3085d6",
		cancelButtonColor: "#d33",
		confirmButtonText: "Ya",
		cancelButtonText: "Tidak",
	}).then((result) => {
		if (result.value) {
			document.location.href = href;
		}
	});
});

$(".actvt-button").on("click", function (e) {
	e.preventDefault();

	const href = $(this).attr("href");

	Swal.fire({
		title: "Activate Store Location?",
		text: "This Store will be able to receive or send the asset",
		icon: "question",
		showCancelButton: true,
		confirmButtonColor: "#3085d6",
		cancelButtonColor: "#d33",
		confirmButtonText: "Yes",
	}).then((result) => {
		if (result.value) {
			document.location.href = href;
		}
	});
});

// jQUery for autocomplete
$(document).ready(function () {
	$(".filter-room").select2({
		theme: "bootstrap",
	});

	// Edit User Modal Population
	$(document).on("click", ".btn-edit-user", function () {
		const id = $(this).data("id");
		const username = $(this).data("username");
		const building = $(this).data("building");

		$("#editUserModal #id_user").val(id);
		$("#editUserModal #username").val(username);
		$("#editUserModal #loc").val(building);
	});
});
