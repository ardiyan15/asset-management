<div class="container-fluid">
    <div class="card rounded p-3">
        <div class="row">
            <div class="col-sm">
                <div class="row">
                    <div class="col-md-6">
                        <h1 class="h3 text-gray-800"><?= $title; ?></h1>
                        <?php if ($user['role_id'] == 1) :  ?>
                            <button type="button" class="btn btn-success btn-sm mb-3" data-toggle="modal" data-target="#addAsset">
                                <i class="fas fa-plus mr-1"></i> Tambah Asset Baru
                            </button>
                            <button type="button" class="btn btn-warning btn-sm mb-3" data-toggle="modal" data-target="#import"><i class="fas fa-file-import"></i> Import File CSV</button>
                            <a href="<?= base_url('asset/download') ?>" class="btn btn-primary btn-sm rounded mb-3"><i class="fa fa-download" aria-hidden="true"></i> Download Format CSV</a>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Untuk Menampilkan pop up sweetalert -->
                <div class="flash-data" data-flashdata="<?= $this->session->flashdata('message'); ?>"></div>
                <div class="form-inline mb-2">
                    <label for="filterCondition" class="small text-muted mr-2">Kondisi</label>
                    <select id="filterCondition" class="form-control form-control-sm">
                        <option value="">Semua</option>
                        <?php foreach (asset_condition_options() as $value => $label) : ?>
                            <option value="<?= $value; ?>" <?= (int) $this->input->get('condition') === $value ? 'selected' : ''; ?>><?= $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <table class="mt-3 table table-border table-sm table-hover table-striped" id="mytable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Merk</th>
                            <th>Nomor Seri</th>
                            <th>Lokasi Saat Ini</th>
                            <th>Kondisi</th>
                            <th>Tahun Input</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Start Modal Add Menu -->
<div class="modal fade" id="addAsset" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Aset</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="<?= base_url('Asset/add_asset'); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Nama</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Name" required>
                    </div>
                    <div class="form-group">
                        <label for="merk">Merk</label>
                        <input type="text" class="form-control" id="merk" name="merk" placeholder="Merk" required>
                    </div>
                    <div class="form-group">
                        <label for="category">Kategori</label>
                        <select class="form-control filter-room" id="category" name="category">
                            <option value=""> -- Pilih Kategori -- </option>
                            <?php foreach ($categories as $categorie) : ?>
                                <option value="<?= $categorie['code']; ?>"> <?= $categorie['name'] ?> </option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('loc', '<small class="text-danger pl-3">', '</small>'); ?>
                    </div>
                    <div class="form-group">
                        <label for="loc">Lokasi</label>
                        <select class="form-control filter-room" id="loc" name="loc">
                            <option value=""> -- Pilih Lokasi -- </option>
                            <?php foreach ($rooms as $room) : ?>
                                <option value="<?= $room['id']; ?>"> <?= $room['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('loc', '<small class="text-danger pl-3">', '</small>'); ?>
                    </div>
                    <div class="form-group">
                        <label for="condition">Kondisi</label>
                        <select class="form-control" id="condition" name="condition">
                            <?php foreach (asset_condition_options() as $value => $label) : ?>
                                <option value="<?= $value; ?>"><?= $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name">Jumlah</label>
                        <input type="text" class="form-control" id="qty" name="qty" placeholder="Masukkan Jumlah" value="1">
                    </div>
                    <a class="small font-weight-bold" data-toggle="collapse" href="#purchaseFields" role="button" aria-expanded="false" aria-controls="purchaseFields">
                        <i class="fas fa-receipt mr-1"></i> Data pembelian (opsional)
                    </a>
                    <div class="collapse mt-3" id="purchaseFields">
                        <?php $this->load->view('menu/_purchase_fields', ['purchase' => null]); ?>
                        <small class="text-muted d-block">Kalau jumlah lebih dari 1, data pembelian yang sama dipakai untuk semua unit.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-sm rounded">Simpan</button>
                    <button type="button" class="btn btn-danger btn-sm rounded" data-dismiss="modal">Tutup</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ubah Kondisi -->
<div class="modal fade" id="conditionModal" role="dialog" aria-labelledby="conditionModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="conditionModalLabel">Ubah Kondisi Aset</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="conditionForm">
                <div class="modal-body">
                    <input type="hidden" name="asset_id" id="conditionAssetId">
                    <p class="font-weight-bold text-gray-800 mb-3" id="conditionAssetName"></p>
                    <div class="form-group">
                        <label>Kondisi</label>
                        <div>
                            <?php foreach (asset_condition_options() as $value => $label) : ?>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="cond<?= $value; ?>" name="condition" value="<?= $value; ?>" class="custom-control-input">
                                    <label class="custom-control-label text-<?= asset_condition_color($value); ?> font-weight-bold" for="cond<?= $value; ?>"><?= $label; ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label for="conditionNotes">Keterangan <span class="text-muted">(opsional)</span></label>
                        <input type="text" class="form-control" id="conditionNotes" name="notes" maxlength="255" placeholder="mis. Layar retak, kompresor bocor">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import -->
<div class="modal fade" id="import" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Upload File CSV</h5>
                <div id="spinner" style="display: none;" class="spinner-border spinner-border-sm ml-2" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="<?= base_url('Asset/import'); ?>" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <input type="file" class="form-control-file" id="import" name="import" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="upload" class="btn btn-success btn-sm rounded">Upload</button>
                    <button type="button" class="btn btn-danger btn-sm rounded" data-dismiss="modal">Tutup</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function alphaOnly(event) {
        var value = String.fromCharCode(event.which);
        var pattern = new RegExp(/[a-zåäö ]/i);
        return pattern.test(value);
    }

    $('#name').bind('keypress', alphaOnly);
    $("#merk").bind('keypress', alphaOnly)

    $(document).ready(function() {
        var table = $("#mytable").DataTable({
            "scrollX": false,
            processing: true,
            serverSide: true,
            ajax: {
                "url": "<?php echo base_url() . '/asset/get_data_asset' ?>",
                "type": "POST",
                "data": function(d) {
                    d.condition = $('#filterCondition').val();
                }
            },
            "columns": [{
                    orderable: false
                },
                null,
                null,
                null,
                null,
                null,
                null,
                {
                    orderable: false
                },
            ],
            "order": [],
            "oLanguage": {
                "sLengthMenu": "_MENU_",
                "sSearch": "Cari",
                "sInfo": "Jumlah data: <b> _TOTAL_ </b>",
                "sInfoEmpty": "Tidak ada data",
                "sProcessing": "loading..."
            },
            "language": {
                "paginate": {
                    "previous": "Sebelumnya",
                    "next": "Selanjutnya"
                }
            }
        });

        $('#filterCondition').on('change', function() {
            table.ajax.reload();
        });

        $('#mytable').on('click', '.js-condition', function() {
            const $btn = $(this);
            $('#conditionAssetId').val($btn.data('id'));
            $('#conditionAssetName').text($btn.data('name'));
            $('#conditionNotes').val('');
            $('#conditionForm input[name="condition"][value="' + $btn.data('condition') + '"]').prop('checked', true);
            $('#conditionModal').modal('show');
        });

        $('#conditionForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: "<?= base_url('asset/update_condition'); ?>",
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json'
            }).done(function(res) {
                $('#conditionModal').modal('hide');
                Swal.fire({
                    title: res.status === 'failed' ? 'Gagal' : 'Berhasil',
                    text: res.message,
                    icon: res.status === 'failed' ? 'error' : (res.status === 'unchanged' ? 'info' : 'success')
                });
                table.ajax.reload(null, false);
            }).fail(function() {
                Swal.fire('Gagal', 'Gagal menghubungi server.', 'error');
            });
        });
    });
</script>
<script>
    $("#upload").on('click', function() {
        $("#spinner").css('display', 'inline');
        $("#spinner").css('margin-top', '5px');
    })

    function hapus(assetId) {
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
                $.ajax({
                    url: "<?= base_url('asset/delete'); ?>",
                    type: 'POST',
                    data: {
                        assetId
                    },
                    success: response => {
                        if (response == 200) {
                            Swal.fire(
                                'Berhasil',
                                'Berhasil hapus data!',
                                'success'
                            ).then(() => {
                                window.location.reload()
                            })
                        } else {
                            // 409 = aset punya riwayat, server mengirim alasannya
                            let message = 'Gagal hapus data';
                            try {
                                const res = typeof response === 'string' ? JSON.parse(response) : response;
                                if (res && res.message) message = res.message;
                            } catch (e) {}
                            Swal.fire(
                                'Gagal',
                                message,
                                'error'
                            )
                        }
                    },
                    error: () => {
                        Swal.fire(
                            'Error',
                            'Internal Server Error',
                            'error'
                        )
                    }
                })
            }
        });
    }
</script>