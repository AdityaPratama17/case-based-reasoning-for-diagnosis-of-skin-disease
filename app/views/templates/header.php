<!DOCTYPE html>
<html>
<head>
	<title><?= $data['judul']; ?></title>
	<link rel="stylesheet" href="<?= BASEURL; ?>/css/bootstrap.css">
	<link rel="stylesheet" href="<?= BASEURL; ?>/css/styles.css">
	<script src="https://kit.fontawesome.com/fe118aecdc.js" crossorigin="anonymous"></script>
</head>
<body style="background-color: whitesmoke;">

<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow">
    <div class="container">
        <a class="navbar-brand" href="<?= BASEURL; ?>/cbr">CASE BASED REASONING</a>
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