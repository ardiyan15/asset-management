<div class="container-fluid">
    <div class="row">
        <div class="col-sm">
            <div class="row">
                <div class="col-md-6">
                    <h1 class="h3 text-gray-800"><?= $title; ?></h1>
                </div>
                <div class="text-right col-md-6">
                    <img class="mr-2" src="<?= base_url('assets/img/logo_raharja.png') ?>" width="50">
                    <img src="<?= base_url('assets/img/kampus_merdeka.png') ?>" width="50">
                </div>
            </div>

            <!-- Untuk Menampilkan pop up sweetalert -->
            <div class="flash-data" data-flashdata="<?= $this->session->flashdata('message'); ?>"></div>

            <div class="table mt-3 col-md-12">
                <table class="table table-border table-sm table-hover" id="mytable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Merk</th>
                            <th>Nomor Seri</th>
                            <th>Lokasi Saat Ini</th>
                            <th>Aksi</th>
                            <th>
                                <button data-target="#bulk_takeout" data-toggle="modal" class="btn btn-success btn-sm">
                                    Pindahkan
                                </button>
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
</div>

<div class="modal fade" id="takeout" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Pindahkan Aset</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="<?= base_url('Transaction/store'); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <input type="hidden" name="asset_id" id="assetId" value="">
                        <input type="hidden" name="source" id="source" value="">
                        <label for="room">Lokasi</label>
                        <select class="form-control filter-room" name="room">
                            <option value=""> -- Pilih Lokasi -- </option>
                            <?php foreach ($rooms as $room) : ?>
                                <option value="<?= $room['id']; ?>"> <?= $room['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-sm">Pindahkan</button>
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Tutup</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="bulk_takeout" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Pindahkan Aset</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="room">Lokasi</label>
                    <select class="form-control filter-room" name="room" id="room_bulk_transaction">
                        <option value=""> -- Pilih Lokasi -- </option>
                        <?php foreach ($rooms as $room) : ?>
                            <option value="<?= $room['id']; ?>"> <?= $room['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="submit_bulk_takeout" class="btn btn-success btn-sm">Pindahkan</button>
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var table = $("#mytable").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": "<?php echo base_url() . '/transaction/get_data_asset' ?>",
                "type": "POST"
            },
            "columns": [
                null,
                null,
                null,
                null,
                null,
                {
                    orderable: false
                },
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

    function takeout(assetId, roomId) {
        $("#assetId").val(assetId)
        $("#source").val(roomId)
    }
</script>