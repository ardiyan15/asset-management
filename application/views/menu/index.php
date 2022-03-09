<div class="container-fluid">

    <div class="row">
        <div class="col-sm">
            <div class="row">
                <div class="col-md-6">
                    <h1 class="h3 text-gray-800"><?= $title; ?></h1>
                    <button type="button" class="btn btn-success btn-sm mb-3" data-toggle="modal" data-target="#addAsset">
                        <i class="fas fa-plus mr-1"></i> Tambah Asset Baru
                    </button>
                    <button type="button" class="btn btn-warning btn-sm mb-3" data-toggle="modal" data-target="#import"><i class="fas fa-file-import"></i> Import File CSV</button>
                    <a href="<?= base_url('asset/download') ?>" class="btn btn-primary btn-sm rounded mb-3"><i class="fa fa-download" aria-hidden="true"></i> Download Format CSV</a>
                </div>
                <div class="text-right col-md-6">
                    <img class="mr-2" src="<?= base_url('assets/img/logo_raharja.png') ?>" width="50">
                    <img src="<?= base_url('assets/img/kampus_merdeka.png') ?>" width="50">
                </div>
            </div>
            <!-- Untuk Menampilkan pop up sweetalert -->
            <div class="flash-data" data-flashdata="<?= $this->session->flashdata('message'); ?>"></div>
            <table class="table table-border table-sm table-hover" id="mytable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Merk</th>
                        <th>Nomor Seri</th>
                        <th>Lokasi Saat Ini</th>
                        <th>Tahun Input</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
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
                        <label for="name">Jumlah</label>
                        <input type="text" class="form-control" id="qty" name="qty" placeholder="Masukkan Jumlah" value="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
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
                        <input type="file" class="form-control" id="import" name="import" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="upload" class="btn btn-success">Upload</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
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
                "type": "POST"
            },
            "columns": [
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
                            Swal.fire(
                                'Gagal',
                                'Gagal hapus data',
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