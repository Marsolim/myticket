<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap 5 Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://kit.fontawesome.com/8e279a0a26.js" crossorigin="anonymous"></script>
  <style>
  .text-toggle[aria-expanded=false] .text-expanded {
    display: none;
  }
  .text-toggle[aria-expanded=true] .text-collapsed {
    display: none;
  }
  .ticket.ticket-title:before {
  	content:" - "
  }
  </style>
</head>
<body>
<div class="ticket-view">
	<div class="card position-relative text-primary mt-3 mb-2">
    	<div class="card-header">
        	<div class="d-flex justify-content-between p-2 px-3">
            	<div class="d-flex flex-row align-items-center">
                	<div class="d-flex flex-column ml-2">
                        <i class="text-warning fa fa-3x fa-ticket"></i>
                    </div>
                    <div class="d-flex flex-column ms-2 ml-2">
                        <span class="h6">TS0001-Pindah titik kamera 6</span>
                        <span class="h6">AMD001-MIDI JAKARTA BARAT</span>
                    </div>
                </div>
                <div class="d-flex flex-row mt-1 ellipsis">
                    <div class="d-flex flex-column align-items-end ml-2">
                        <small class="mr-2 text-align-right">Last update 20 days ago</small>
                        <small class="mr-2 text-align-right">20-08-2023 12:50:24</small>
                    </div>
                </div>
        	</div>
            <div class="btn-group ms-2 small align-items-end position-absolute top-0 end-0 translate-middle-y">
                        <button class="btn btn-warning" title="Tidak dicover MC"><i class="fa fa-thumbs-down"></i></button>
                        <button class="btn btn-primary" title="Rekomendasi"><i class="fa fa-handshake"></i></button>
                        <button class="btn btn-primary" title="Teknisi"><i class="fa fa-users-gear"></i></button>
                        <button class="btn btn-primary" title="Pekerjaan"><i class="fa fa-screwdriver-wrench"></i></button>
                        <button class="btn btn-primary" title="Selesai menunggu IT"><i class="fa fa-hourglass"></i></button>
                        <button class="btn btn-success" title="Selesai"><i class="fa fa-circle-check"></i></button>
                        <button class="btn btn-danger" title="Double AHO"><i class="fa fa-bugs"></i></button>
                        <button class="btn btn-primary text-toggle" data-bs-toggle="collapse" href="#ts-ts0001-body" aria-expanded="false" aria-controls="ts-ts0001-body"><i class="text-collapsed fas fa-caret-down"></i><i class="text-expanded fas fa-caret-up"></i></button>
                    </div>
    	</div>
        <div class="card-body collapse" id="ts-ts0001-body">
        	<div class="text-justify">
            	<ul class="list-group list-group-flush">
                	<li class="list-group-item">
                    	<h6 class="card-title">Rekomendasi</h6>
                        <p class="card-text">Sebaiknya dilakukan dulu pengecekan area apakah aman untuk pemasangan kamera.</p>
                    </li>
                    <li class="list-group-item">
                    	<h6>Tidak tercover MC</h6>
                        <p>MC sudah expired sejak 2 hari sebelum pembuatan tiket servis.</p>
                    </li>
                    <li class="list-group-item">
                    	<h6>Daftar Dokumen</h6>
                        <a href="#">Invoice</a>
                        <a href="#">SPK</a>
                    </li>
                    <li class="list-group-item">
                    	<h6>Teknisi</h6>
                        <a href="#">Bambang</a>,
                        <a href="#">Herianto</a>
                    </li>
            	</ul>
        	</div>
        </div>
    </div>
    <div class="card position-relative text-primary mt-3 mb-2">
    	<div class="card-header">
        	<div class="d-flex justify-content-between p-2">
            	<div class="d-flex flex-row align-items-center">
                	<div class="d-flex flex-column ml-2">
                        <i class="text-warning fa fa-3x fa-ticket"></i>
                    </div>
                    <div class="d-flex flex-column ms-2 ml-2">
                        <div class="h6 position-relative">
                        <span class="h6 position-relative"><span class="ticket ticket-number">TS0001</span><span class="ticket ticket-title">Pindah titik kamera 6</span>
                        <span class="small badge rounded-pill bg-primary text-light">
                                <a class="text-decoration-none text-light" title="Status ticket" href="#" data-method="POST">Belum dikunjungi</a>
                                <span class="visually-hidden">unread messages</span>
                            </span>
                        </span>
                        </div>
                        <div class="h6 position-relative">
                            <span class="h6 position-relative rounded-pill">AMD001-MIDI JAKARTA BARAT
                            <span class="small badge rounded-pill bg-success text-light">
                                <a class="text-decoration-none text-light" title="Jumlah ticket" href="#" data-method="POST">12</a>
                                <span class="visually-hidden">unread messages</span>
                            </span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-row mt-1">
                    <div class="d-flex flex-column align-items-end ml-2">
                        <small class="mr-2 text-align-right">Last update 20 days ago</small>
                        <small class="mr-2 text-align-right">20-08-2023 12:50:24</small>
                    </div>
                </div>
        	</div>
            <div class="btn-group ms-2 small align-items-end position-absolute top-0 end-0 translate-middle-y">
            	<button class="btn btn-warning disabled" title="Tidak dicover MC"><i class="fa fa-thumbs-down"></i></button>
                <button class="btn btn-primary" title="Rekomendasi"><i class="fa fa-handshake"></i></button>
                <button class="btn btn-primary" title="Teknisi"><i class="fa fa-users-gear"></i></button>
                <button class="btn btn-primary" title="Pekerjaan"><i class="fa fa-screwdriver-wrench"></i></button>
                <button class="btn btn-primary" title="Selesai menunggu IT"><i class="fa fa-hourglass"></i></button>
                <button class="btn btn-success" title="Selesai"><i class="fa fa-circle-check"></i></button>
                <button class="btn btn-danger" title="Double AHO"><i class="fa fa-bugs"></i></button>
                <button class="btn btn-primary text-toggle" data-bs-toggle="collapse" href="#ts-ts0001-body" aria-expanded="false" aria-controls="ts-ts0001-body"><i class="text-collapsed fas fa-caret-down"></i><i class="text-expanded fas fa-caret-up"></i></button>
        	</div>
    	</div>
        <div class="card-body collapse" id="ts-ts0001-body">
        	<div class="text-justify">
            	<ul class="list-group list-group-flush">
                	<li class="list-group-item">
                    	<h6 class="card-title">Rekomendasi</h6>
                        <p class="card-text">Sebaiknya dilakukan dulu pengecekan area apakah aman untuk pemasangan kamera.</p>
                    </li>
                    <li class="list-group-item">
                    	<h6>Tidak tercover MC</h6>
                        <p>MC sudah expired sejak 2 hari sebelum pembuatan tiket servis.</p>
                    </li>
                    <li class="list-group-item">
                    	<h6>Daftar Dokumen</h6>
                        <a href="#">Invoice</a>
                        <a href="#">SPK</a>
                    </li>
                    <li class="list-group-item">
                    	<h6>Teknisi</h6>
                        <a href="#">Bambang</a>,
                        <a href="#">Herianto</a>
                    </li>
                    <li class="list-group-item">
                    	<h6>Pekerjaan</h6>
                        <div class="row small"><div class="col-2">20-08-2023 12:52.22</div><div class="col-4">Pemeriksaan lapangan</div></div>
                        <div class="row small"><div class="col-2">21-08-2023 12:52.22</div><div class="col-4">Ganti kamera</div><div class="col-4">Kamera HIKVISION</div><div class="col-2">123456789</div></div>
                    </li>
            	</ul>
        	</div>
        </div>
    </div>
</div>
<div>
<div class="container px-0 py-0 mx-0 mt-0 mb-2">
    <div class="row d-flex align-items-center justify-content-center">
        <div class="col">
            <div class="card border-warning" style="background-color:<?= $statuscolors[$model->status] ?>">
                
                <div class="card-body p-2 px-3">
                    
                 </div>
                 <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex flex-row align-items-center">
                            <small class="text-mute position-relative"><i class="fa fa-store"></i> MIDI JAKARTA BARAT
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-light text-dark">
                                <a href="#" data-method="POST">12</a>
                                <span class="visually-hidden">unread messages</span>
                            </span>
                            </small>
                        </div>
                        <div class="d-flex flex-row align-items-center">
                            <small class="text-primary">Belum Dikunjungi</small>
                        </div>
                        <div class="d-flex flex-row align-items-center">
                            <img src="https://i.imgur.com/1YrCKa1.jpg" width="15" height="15" class="rounded-circle">
                            <div class="d-flex flex-column ms-1 ml-2">
                                <small class="font-weight-bold">Administrator</small>
                            </div>
                        </div>
                    </div>
                    <hr class="bg-warning border-2 border-top border-warning" style="opacity:0.5">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex flex-row icons d-flex align-items-center">
                            <a href="#" class="btn btn-link quick-action"><i class="fa fa-thumbs-down text-warning"></i></a>
                            <a href="#" class="btn btn-link quick-action"><i class="fa fa-handshake"></i></a>
                            <a href="#" class="btn btn-link quick-action"><i class="fa fa-users-gear"></i></a>
                            <a href="#" class="btn btn-link quick-action"><i class="fa fa-screwdriver-wrench"></i></a>
                            <a href="#" class="btn btn-link quick-action"><i class="fa fa-hourglass-half text-primary"></i></a>
                            <a href="#" class="btn btn-link quick-action"><i class="fa fa-circle-check text-success"></i></a>
                            <a href="#" class="btn btn-link quick-action"><i class="fa fa-bug-slash text-danger"></i></a>
                        </div>
                        <div class="d-flex flex-row muted-color">
                            <a class="btn btn-primary position-relative" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                                Actions
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    1 
                                    <span class="visually-hidden">unread messages</span>
                                </span>
                            </a>
                        </div>
                    </div>
                    <div id="collapseExample" class="collapse">
                    <hr>
                    <div class="comments">
                        <div class="d-flex flex-row mb-2"> <img src="https://i.imgur.com/9AZ2QX1.jpg" width="40" height="40" class="rounded-image rounded-circle mt-2">
                            <div class="d-flex flex-column ms-2 ml-2"> <span class="name"><strong>Daniel Frozer</strong></span> <small class="comment-text">I like this alot! thanks alot</small>
                                <div class="d-flex flex-row align-items-center status"> <small>Like</small> <small>Reply</small> <small>Translate</small> <small>18 mins</small> </div>
                            </div>
                        </div>
                        <div class="d-flex flex-row mb-2"> <img src="https://i.imgur.com/1YrCKa1.jpg" width="40" class="rounded-image">
                            <div class="d-flex flex-column ml-2"> <span class="name">Elizabeth goodmen</span> <small class="comment-text">Thanks for sharing!</small>
                                <div class="d-flex flex-row align-items-center status"> <small>Like</small> <small>Reply</small> <small>Translate</small> <small>8 mins</small> </div>
                            </div>
                        </div>
                        <div class="comment-input"> <input type="text" class="form-control">
                            <div class="fonts"> <i class="fa fa-camera"></i> </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
