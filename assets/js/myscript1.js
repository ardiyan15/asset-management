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
	case "reject":
		Swal.fire({
			title: "Success",
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
	case "delete":
		Swal.fire({
			title: "Berhasil",
			text: "User Berhasil Dihapus",
			icon: "success",
			showConfirmButton: TextTrackCue,
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
