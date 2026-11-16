<div class="container-fluid">
    <div class="row">
        <div class="col-sm">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="h3 text-gray"><?= $title; ?></h3>
                    <p class="mt-2 font-weight-bold"><?= $room['name'] ?> ( <?= $room['description'] ?> )</p>
                    <div class="row" style="margin-left: 2px;">
                        <a href="<?= base_url('room/') . $room['floor_id'] ?>" style="margin-right: 10px;">Kembali</a>
                        <a target="_blank" href="<?= base_url('asset/print_report/') . $room_id ?>" class="btn btn-primary btn-sm rounded mr-2">Print Laporan PDF</a>
                        <a target="_blank" href="<?= base_url('asset/print_labels/') . $room_id ?>" class="btn btn-dark btn-sm rounded mr-2"><i class="fas fa-qrcode mr-1"></i> Cetak Label QR Ruangan</a>
                        <button type="button" id="print_selected_labels" class="btn btn-outline-dark btn-sm rounded"><i class="fas fa-qrcode mr-1"></i> Cetak Label Terpilih</button><br />
                    </div>
                </div>
            </div>
            <!-- Untuk Menampilkan pop up sweetalert -->
            <div class="flash-data" data-flashdata="<?= $this->session->flashdata('message'); ?>"></div>
            <div class="table-responsive mt-4">
                <table class="table table-border table-sm table-hover" id="myTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Merk</th>
                            <th>Nomor Seri</th>
                            <th>Kondisi</th>
                            <th>Lokasi Saat Ini</th>
                            <th>Aksi</th>
                            <th>
                                <button data-target="#bulk_takeout" data-toggle="modal" class="btn btn-success btn-sm">
                                    Pindahkan
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($assets) : ?>
                            <?php $i = 1; ?>
                            <?php foreach ($assets as $asset) : ?>
                                <tr>
                                    <td scope="row"> <?= $i; ?></td>
                                    <td scope="row"><?= $asset['asset_name']; ?></td>
                                    <td scope="row"><?= $asset['merk']; ?></td>
                                    <td scope="row">
                                        <a href="<?= base_url('scan/' . rawurlencode($asset['serial_number'])) ?>" target="_blank" title="Lihat halaman scan QR"><i class="fas fa-qrcode mr-1"></i><?= $asset['serial_number']; ?></a>
                                    </td>
                                    <td scope="row"><?= asset_condition_badge($asset['asset_condition']); ?></td>
                                    <?php if ($asset['placement_status'] == 1) : ?>
                                        <td scope="row"><?= $asset['name']; ?></td>
                                        <td>
                                            <a href="<?= $asset['id_asset'] ?>" data-toggle="modal" data-target="#takeout<?= $asset['id_asset'] ?>" class="btn btn-warning btn-sm"> Pindahkan </a>
                                        </td>
                                    <?php else : ?>
                                        <td scope="row">Sedang Dipindahkan</td>
                                        <td></td>
                                    <?php endif ?>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" name="check" type="checkbox" value="<?= $asset['id_asset'] ?>">
                                        </div>
                                    </td>
                                </tr>
                                <?php $i++; ?>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="8">
                                    <h4> <?= $error; ?> </h4>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
foreach ($assets as $asset) :
?>
    <div class="modal fade" id="takeout<?= $asset['id_asset'] ?>" role="dialog" aria-labelledby="exampleModalLabel">
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
                            <input type="hidden" name="asset_id" value="<?= $asset['id_asset'] ?>">
                            <input type="hidden" name="source" value="<?= $asset['room_id'] ?>">
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
<?php endforeach; ?>

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
    $('#print_selected_labels').on('click', function() {
        const ids = $('input:checkbox[name="check"]:checked').map(function() {
            return $(this).val();
        }).get();

        if (ids.length === 0) {
            Swal.fire({
                title: 'Belum ada aset dipilih',
                text: 'Centang aset yang ingin dicetak labelnya terlebih dahulu',
                icon: 'info',
            });
            return;
        }
        window.open(baseUrl + 'asset/print_labels?ids=' + ids.join(','), '_blank');
    });

    function takeout(assetId, roomId) {
        $("#assetId").val(assetId)
        $("#source").val(roomId)
    }
</script>