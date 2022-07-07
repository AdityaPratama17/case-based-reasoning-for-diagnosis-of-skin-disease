
<div class="container contain shadow p-4" style="width: 900px;">
    <h5 class="text-center font-weight-bold">AKURASI</h5>
    <hr class="bg-dark w-50">
    <!-- FORM SPLIT DATA -->
    <div class="container mt-4" style="width: 300px;">
        <form action="<?= BASEURL;?>/akurasi/akurasi" method="post">
            <div class="form-group text-center">
                <label for="exampleInputEmail1">Split Data Training & Testing</label>
                <input type="text" class="form-control" name="split" placeholder="0.7" required>
            </div>
            <button type="submit" class="btn btn-info btn-block btn-sm"><i class="fas fa-cogs mr-1"></i> PROSES</button>
        </form>
    </div>

    <?php if ($data['flag']) {?>
        <!-- HASIL AKURASI -->
        <hr class="my-4">
        <div class="row mt-2">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Minkowski Distance + Bayesian Model</div>
                    <div class="card-body font-weight-normal">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td scope="col">Jumlah Data Training</td>
                                <td>:</td>
                                <th scope="col"><?= $data['jumTrain'];?></th>
                            </tr>
                            <tr>
                                <td scope="col">Jumlah Data Testing</td>
                                <td>:</td>
                                <th scope="col"><?= $data['jumTest'];?></th>
                            </tr>
                            <tr>
                                <td scope="col">Jumlah Data Benar</td>
                                <td>:</td>
                                <th scope="col"><?= $data['a']['jumBenar'];?> dari <?= $data['jumTest'];?></th>
                            </tr>
                            <tr>
                                <td scope="col">Nilai Akurasi</td>
                                <td>:</td>
                                <th scope="col"><?= $data['a']['akurasi'];?>%</th>
                            </tr>
                            <tr>
                                <td scope="col">Running Time</td>
                                <td>:</td>
                                <th scope="col"><?= $data['a']['menit'];?> menit <?= $data['a']['detik'];?> detik</th>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Minkowski Distance</div>
                    <div class="card-body font-weight-normal">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td scope="col">Jumlah Data Training</td>
                                <td>:</td>
                                <th scope="col"><?= $data['jumTrain'];?></th>
                            </tr>
                            <tr>
                                <td scope="col">Jumlah Data Testing</td>
                                <td>:</td>
                                <th scope="col"><?= $data['jumTest'];?></th>
                            </tr>
                            <tr>
                                <td scope="col">Jumlah Data Benar</td>
                                <td>:</td>
                                <th scope="col"><?= $data['b']['jumBenar'];?> dari <?= $data['jumTest'];?></th>
                            </tr>
                            <tr>
                                <td scope="col">Nilai Akurasi</td>
                                <td>:</td>
                                <th scope="col"><?= $data['b']['akurasi'];?>%</th>
                            </tr>
                            <tr>
                                <td scope="col">Running Time</td>
                                <td>:</td>
                                <th scope="col"><?= $data['b']['menit'];?> menit <?= $data['b']['detik'];?> detik</th>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php }?>
<br>
</div>


<br><br><br><br>