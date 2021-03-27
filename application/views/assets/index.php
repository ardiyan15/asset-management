<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="row">
        <div class="col-sm">
            <div class="row">
                <div class="col-sm">
                    <h1 class="h3 text-gray-800"><?= $title; ?></h1>
                    <p style="font-size: 12px"> Total Asset's : <?= $amount_data; ?> </p>
                </div>
                <div class="col-sm">
                    <form action="<?= base_url('asset'); ?>" method="post">
                        <div class="input-group">
                            <input type="text" name="keyword" class="form-control" placeholder="Filter asset" aria-describedby="basic-addon2">
                            <button type="submit" class="ml-2 btn btn-success btn-sm"> <i class="fas fa-search"></i> Filter </button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Untuk Menampilkan pop up sweetalert -->
            <div class="flash-data" data-flashdata="<?= $this->session->flashdata('message'); ?>"></div>
            <div class="table-responsive mt-3">
                <table class="table table-border table-sm table-hover" id="table-asset">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Name</th>
                            <th class="text-center">Merk</th>
                            <th class="text-center">Serial Number</th>
                            <th class="text-center">Current Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($assets) : ?>
                            <?php $i = 1; ?>
                            <?php foreach ($assets as $asset) : ?>
                                <tr>
                                    <td scope="row" class="text-center"> <?= $i; ?></td>
                                    <td scope="row" class="text-center"><?= $asset['asset_name']; ?></td>
                                    <td scope="row" class="text-center"><?= $asset['merk']; ?></td>
                                    <td scope="row" class="text-center"><?= $asset['serial_number']; ?></td>
                                    <td scope="row" class="text-center"><?= $asset['name']; ?></td>
                                </tr>
                                <?php $i++; ?>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="5">
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
<!-- /.container-fluid -->