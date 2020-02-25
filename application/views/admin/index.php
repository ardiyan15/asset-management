<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Page Heading -->

    <h2> <?= $title; ?></h2>

    <form action="<?= base_url('admin/index'); ?>" method="POST">
        <div class="form-group" style="width: 20%; height: 10%">
            <label for="order"> Order By Store / Deparment</label>
            <select id="order" name="order">
                <option value=""> Store / Department </option>
                <?php foreach ($store as $str) : ?>
                    <option value="<?= $str['store_code']; ?>"> <?= $str['store_code'] ?> <?= $str['store_name'] ?> </option>
                <?php endforeach; ?>
            </select>
            <button class="mt-2 btn btn-primary btn-sm"> Filter </button>
            <p class="ml-1 mt-3" style="font-size: 12px;"> Filter by :
                <?php if ($order == null) {
                    echo "All";
                } else {
                    echo $order;
                } ?>
            </p>
        </div>
    </form>
    <div style="height: 70%; width: 70%;">
        <canvas id="myChart" class="chart"></canvas>
    </div>

    <script>
        var ctx = document.getElementById("myChart");
        var myChart = new Chart(ctx, {
            type: 'bar', //Tipe tampilan grafik, sobat bisa menggunakan tampilan bar, pie, line, radar dan sebagainya
            data: {
                labels: [<?php foreach ($assets as $asset) {
                                echo '"' . $asset['asset_name'] . '"' . ",";
                            }
                            ?>], //keterangan nama-nama label
                datasets: [{
                    label: 'Books & Beyond Asset IT', //Judul Grafik
                    data: [<?php foreach ($assets as $asset) {
                                echo $asset['total'] . ',';
                            } ?>],
                    barPercentage: 0.5, //Data Grafik
                    backgroundColor: [
                        'rgba(205, 99, 132, 0.2)', //Warna Background Data Grafik
                        'rgba(54, 162, 235, 0.2)', //Warna Background Data Grafik
                        'rgba(255, 206, 86, 0.2)', //Warna Background Data Grafik
                        'rgba(75, 192, 192, 0.2)', //Warna Background Data Grafik
                        'rgba(153, 102, 255, 0.2)', //Warna Background Data Grafik
                        'rgba(255, 159, 64, 0.2)', //Warna Background Data Grafik
                        'rgba(255, 129, 64, 0.2)', //Warna Background Data Grafik
                        'rgba(50, 129, 64, 0.2)', //Warna Background Data Grafik
                        'rgba(50, 255, 64, 0.2)', //Warna Background Data Grafik
                        'rgba(50, 255, 100, 0.2)', //Warna Background Data Grafik
                        'rgba(129, 255, 100, 0.2)', //Warna Background Data Grafik
                        'rgba(139, 200, 100, 0.2)', //Warna Background Data Grafik
                        'rgba(139, 200, 50, 0.2)', //Warna Background Data Grafik
                    ],
                    borderColor: [
                        'rgba(255,99,132,1)', //Warna Garis Data Grafik
                        'rgba(54, 162, 235, 1)', //Warna Garis Data Grafik
                        'rgba(255, 206, 86, 1)', //Warna Garis Data Grafik
                        'rgba(75, 192, 192, 1)', //Warna Garis Data Grafik
                        'rgba(153, 102, 255, 1)', //Warna Garis Data Grafik
                        'rgba(255, 159, 64, 1)' //Warna Garis Data Grafik
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            stepSize: 2,
                            suggestedMax: 10,
                            suggestedMin: 1
                        }
                    }]
                },
                layout: {
                    padding: {
                        bottom: 20
                    }
                }
            }
        });
    </script>

</div>
<!-- /.container-fluid -->
</div>
<!-- End of Main Content -->