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

$("#room_bulk_transaction").change(function () {
	let room_id = $(this).val();
	$("#submit_bulk_takeout").click(function () {
		let asset = [];
		$('input:checkbox[name="check"]:checked').each(function () {
			console.log($(this).val());
			asset.push($(this).val());
			Swal.fire({
				title: "Memindahkan Aset",
				text: "Anda ingin memindahkan aset yang dipilih?",
				icon: "question",
				showCancelButton: true,
				confirmButtonColor: "#3085d6",
				cancelButtonColor: "#d33",
				confirmButtonText: "Ya",
				cancelButtonText: "Tidak",
			})
				.then((result) => {
					console.log(asset);
					if (result.value) {
						$.ajax({
							method: "POST",
							url: baseUrl + "transaction/bulk_transaction",
							cache: false,
							data: {
								asset_id: asset,
								room_id: room_id,
							},
							success: function (results) {
								if (results > 0) {
									Swal.fire({
										title: "Berhasil",
										text: "Berhasil memindahkan aset",
										icon: "success",
									}).then(function () {
										location.reload();
									});
								} else {
									Swal.fire({
										title: "Gagal",
										text: "Gagal memindahkan aset",
										icon: "error",
									});
								}
							},
							error: function (err, XMLHttpRequest, textStatus, errorThrown) {
								console.log(err);
							},
						});
					}
				})
				.catch((err) => console.log("error"));
		});
	});
});

$("#btn-acc").click(function () {
	let asset_ids = [];
	$('input:checkbox[name="check_in"]:checked').each(function () {
		asset_ids.push($(this).val());
		Swal.fire({
			title: "Terima Aset",
			text: "Anda ingin menerima aset yang dipilih",
			icon: "question",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			cancelButtonColor: "#d33",
			confirmButtonText: "Ya",
			cancelButtonText: "Tidak",
		}).then((result) => {
			if (result.value) {
				$.ajax({
					method: "POST",
					url: baseUrl + "TI/bulk_acc",
					data: {
						asset_ids: asset_ids,
					},
					success: function (results) {
						if (results > 0) {
							Swal.fire({
								title: "Berhasil",
								text: "Berhasil menerima aset",
								icon: "success",
							}).then(function () {
								location.reload();
							});
						} else {
							Swal.fire({
								title: "Gagal",
								text: "Gagal memindahkan aset",
								icon: "error",
							});
						}
					},
					error: function (err, XMLHttpRequest, textStatus, errorThrown) {
						console.log(err);
					},
				});
			}
		});
	});
});
