<div class="container-fluid">
    <h2> <?= $title; ?></h2>
    <form action="<?= base_url('admin/index'); ?>" method="POST">
        <div class="row">
            <div class="col-md-6">
                <label for="order"> Filter berdasarkan ruangan </label>
            </div>
            <div class="text-right col-md-6" style="margin-top: -5%;">
                <img class="mr-2" src="<?= base_url('assets/img/logo_raharja.png') ?>" width="50">
                <img src="<?= base_url('assets/img/kampus_merdeka.png') ?>" width="50">
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <select id="order" name="room_id" class="pr-3 pl-3 filter-room">
                        <option value=""> -- Pilih Ruangan -- </option>
                        <?php foreach ($rooms as $room) : ?>
                            <option value="<?= $room['id']; ?>"> <?= $room['name'] ?> </option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-success btn-sm mt-2"> Filter </button>
                    <p class="ml-1 mt-3" style="font-size: 12px;"> Filter berdasarkan :
                        <?php if ($room_name == null) {
                            echo "<b> semua ruangan </b>";
                        } else {
                            echo "<b>" . $room_name['name'] . "</b>";
                        } ?>
                    </p>
                </div>
            </div>
        </div>
    </form>
    <div style="width: 70%; height: 320px;">
        <canvas id="myChart" class="chart"></canvas>
    </div>

    <script>
        var ctx = document.getElementById("myChart");
        var myChart = new Chart(ctx, {
            type: 'horizontalBar', //Tipe tampilan grafik, sobat bisa menggunakan tampilan bar, pie, line, radar dan sebagainya
            data: {
                labels: [<?php foreach ($assets as $asset) {
                                echo '"' . $asset['asset_name'] . '"' . ",";
                            }
                            ?>], //keterangan nama-nama label
                datasets: [{
                    label: 'Inventaris Universitas Raharja', //Judul Grafik
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
                        'rgba(139, 100, 100, 0.2)', //Warna Background Data Grafik
                        'rgba(139, 200, 50, 0.2)', //Warna Background Data Grafik
                        'rgba(139, 100, 50, 0.2)', //Warna Background Data Grafik
                        'rgba(139, 50, 50, 0.2)', //Warna Background Data Grafik
                        'rgba(29, 50, 50, 0.2)', //Warna Background Data Grafik
                        'rgba(255, 50, 50, 0.2)', //Warna Background Data Grafik
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
                        stacked: true,
                        gridLines: {
                            display: false
                        },
                        ticks: {
                            display: true,
                            beginAtZero: true,
                            stepSize: 2,
                            suggestedMax: 10,
                            suggestedMin: 1,
                        },
                    }],
                    xAxes: [{
                        gridLines: {
                            display: false
                        }
                    }]
                },
                layout: {
                    padding: {
                        bottom: 20
                    }
                },
                animation: {
                    duration: 0,
                    onComplete: function() {
                        // render the value of the chart above the bar
                        var ctx = this.chart.ctx;
                        ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, 'normal', Chart.defaults.global.defaultFontFamily);
                        ctx.fillStyle = this.chart.config.options.defaultFontColor;
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'bottom';
                        this.data.datasets.forEach(function(dataset) {
                            for (var i = 0; i < dataset.data.length; i++) {
                                var model = dataset._meta[Object.keys(dataset._meta)[0]].data[i]._model;
                                ctx.fillText(dataset.data[i], model.x + 15, model.y + 5);
                            }
                        });
                    }
                }
            }
        });
    </script>

</div>
<!-- /.container-fluid -->
</div>
<!-- End of Main Content -->