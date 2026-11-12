$("#building").change(function () {
	let building_id = $(this).val();
	$.ajax({
		method: "POST",
		url: baseUrl + "/room/list_rooms",
		dataType: "json",
		cache: false,
		data: { building_id: building_id },
		success: function (results) {
			$.each(results, function (i, item) {
				$("#loc").append(
					$("<options>", {
						value: item.value,
						text: item.text,
					})
				);
			});
		},
		error: function () {
			console.log("Error");
		},
	});
});

// Pindah aset massal (halaman Transaksi / Aset): satu konfirmasi, satu transaksi
$("#submit_bulk_takeout").on("click", function () {
	const room_id = $("#room_bulk_transaction").val();
	const note = $("#bulk_note").length ? $("#bulk_note").val() : "";
	const asset = $('input:checkbox[name="check"]:checked')
		.map(function () {
			return $(this).val();
		})
		.get();

	if (asset.length === 0) {
		Swal.fire({
			title: "Belum ada aset dipilih",
			text: "Centang aset yang ingin dipindahkan terlebih dahulu",
			icon: "info",
		});
		return;
	}

	if (!room_id) {
		Swal.fire({
			title: "Lokasi belum dipilih",
			text: "Pilih gedung dan lokasi tujuan",
			icon: "info",
		});
		return;
	}

	Swal.fire({
		title: "Memindahkan Aset",
		text: "Anda ingin memindahkan " + asset.length + " aset yang dipilih?",
		icon: "question",
		showCancelButton: true,
		confirmButtonColor: "#3085d6",
		cancelButtonColor: "#d33",
		confirmButtonText: "Ya",
		cancelButtonText: "Tidak",
	}).then((result) => {
		if (!result.value) return;

		$.ajax({
			method: "POST",
			url: baseUrl + "transaction/bulk_transaction",
			dataType: "json",
			cache: false,
			data: {
				asset_id: asset,
				room_id: room_id,
				note: note,
			},
			success: function (results) {
				if (results > 0) {
					const skipped = asset.length - results;
					Swal.fire({
						title: "Berhasil",
						text:
							results +
							" aset berhasil dipindahkan" +
							(skipped > 0 ? " (" + skipped + " aset dilewati karena sedang dipindahkan / sudah di lokasi tujuan)" : ""),
						icon: "success",
					}).then(function () {
						location.reload();
					});
				} else {
					Swal.fire({
						title: "Gagal",
						text: "Aset gagal dipindahkan. Pastikan aset belum dalam proses pindah dan lokasi tujuan berbeda dari lokasi saat ini.",
						icon: "error",
					});
				}
			},
			error: function (err) {
				console.log(err);
				Swal.fire({
					title: "Gagal",
					text: "Terjadi kesalahan pada server",
					icon: "error",
				});
			},
		});
	});
});

$("#btn-acc").click(function () {
	const asset_ids = $('input:checkbox[name="check_in"]:checked')
		.map(function () {
			return $(this).val();
		})
		.get();

	if (asset_ids.length === 0) {
		Swal.fire({
			title: "Belum ada aset dipilih",
			text: "Centang aset yang ingin diterima terlebih dahulu",
			icon: "info",
		});
		return;
	}

	Swal.fire({
		title: "Terima Aset",
		text: "Anda ingin menerima " + asset_ids.length + " aset yang dipilih?",
		icon: "question",
		showCancelButton: true,
		confirmButtonColor: "#3085d6",
		cancelButtonColor: "#d33",
		confirmButtonText: "Ya",
		cancelButtonText: "Tidak",
	}).then((result) => {
		if (!result.value) return;

		$.ajax({
			method: "POST",
			url: baseUrl + "TI/bulk_acc",
			dataType: "json",
			data: {
				asset_ids: asset_ids,
			},
			success: function (results) {
				if (results > 0) {
					Swal.fire({
						title: "Berhasil",
						text: results + " aset berhasil diterima",
						icon: "success",
					}).then(function () {
						location.reload();
					});
				} else {
					Swal.fire({
						title: "Gagal",
						text: "Gagal menerima aset",
						icon: "error",
					});
				}
			},
			error: function (err) {
				console.log(err);
				Swal.fire({
					title: "Gagal",
					text: "Terjadi kesalahan pada server",
					icon: "error",
				});
			},
		});
	});
});
