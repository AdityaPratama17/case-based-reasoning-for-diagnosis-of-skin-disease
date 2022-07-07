<!DOCTYPE html>
<html>
<head>
	<title><?= $data['judul']; ?></title>
    <style>
        div.dataTables_wrapper {
            width: 100%;
            margin: 0 auto;
        }
    </style>
	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
	<link rel="stylesheet" href="<?= BASEURL; ?>/css/bootstrap.css">
	<link rel="stylesheet" href="<?= BASEURL; ?>/css/styles.css">
	<script src="https://kit.fontawesome.com/fe118aecdc.js" crossorigin="anonymous"></script>
</head>
<body style="background-color: whitesmoke;">

<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow">
    <div class="container">
        <a class="navbar-brand" href="#">CASE BASED REASONING</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASEURL; ?>/cbr">Diagnosis</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASEURL; ?>/revise">Revise</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASEURL; ?>/kasus">Basis Kasus</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASEURL; ?>/akurasi">Akurasi</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="jumbotron jumbotron-fluid shadow text-white text-center mt-2">
    <div class="container">
        <h2 class="font-weight-light mt-4 font-italic">CASE BASED REASONING</h2>
        <h1 class="display-4 font-weight-bold mt-2">DIAGNOSA PENYAKIT KULIT</h1>
        <h4 class="font-weight-light">dengan metode <b>Minkowski Distance</h4>
        <hr class="my-4 bg-white w-25">
        <a class="btn btn-outline-light btn-md font-weight-bold" href="<?= BASEURL; ?>/cbr" role="button">MULAI KONSULTASI <i class="fas fa-angle-double-right"></i></a>
    </div>
</div>

<div class="container contain shadow p-4">
    <!-- Basis Kasus -->
    <div class="card mt-4">
        <div class="card-header text-center">BASIS KASUS</div>
        <div class="card-body font-weight-normal">
            <table id="example" class="display nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th scope="col">id</th>
                        <?php for ($i=1; $i < 35; $i++) { ?>
                            <th scope="col"><?= $i;?></th>
                        <?php } ?>
                        <th scope="col">Penyakit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['kasus'] as $key => $kasus) { ?>
                        <tr class="text-center">
                            <td><?= $kasus['id_cb'];?></td>
                            <?php for ($i=1; $i < 35; $i++) { ?>
                                <td><?= $kasus[$i];?></td>
                            <?php } ?>   
                            <td><?= $kasus['class'];?></td>
                        </tr>
                    <?php }?>
                </tbody>
            </table>
        </div>
    </div><br>

    <div class="row mt-2">
        <!-- PENYAKIT -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header text-center">JENIS PENYAKIT</div>
                <div class="card-body font-weight-normal">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                            <th scope="col">id</th>
                            <th scope="col">Penyakit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1;?>
                            <?php foreach ($data['penyakit'] as $penyakit) { ?>
                                <tr>
                                    <td><?= $i++;?></td>
                                    <td><?= $penyakit;?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- FITUR -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header text-center">JENIS FITUR</div>
                <div class="card-body font-weight-normal">
                    <div class="table-wrapper-scroll-y my-custom-scrollbar" style="height: 256px;">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                <th scope="col">id</th>
                                <th scope="col">Fitur</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i=1;?>
                                <?php foreach ($data['fitur'] as $fitur) { ?>
                                    <tr>
                                        <td><?= $fitur['id_bobot'];?></td>
                                        <td><?= $fitur['fitur'];?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- BOBOT -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header text-center">JENIS <i>VALUE</i></div>
                <div class="card-body font-weight-normal">
                    <table class="table table-sm table-hover">
                    <caption style="font-size: 15px;">Pada fitur <b>Family history</b> value <b>1</b> bermakna <b>Ya</b></ style="font-size: 15px;">
                        <thead>
                            <tr>
                            <th scope="col">id</th>
                            <th scope="col">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=0;?>
                            <?php foreach ($data['bobot'] as $bobot) { ?>
                                <tr>
                                    <td><?= $i++;?></td>
                                    <td><?= $bobot;?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> <br>
</div>

<br><br><br><br>

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#example').DataTable( {
            "scrollX": true
        } );
    } );
</script>
<script src="<?= BASEURL; ?>/js/bootstrap.js"></script>
</body>
</html>
