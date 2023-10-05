<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="format-detection" content="telephone=no">
	<title> - Inicial</title>
	
	<link href="<?= base_url('xhtml/vendor/select2/css/select2.min.css') ?>" rel="stylesheet">
	<link href="<?= base_url('xhtml/vendor/jquery-nice-select/css/nice-select.css') ?>" rel="stylesheet">
	
	
	<link href="<?= base_url("xhtml/vendor/sweetalert2/dist/sweetalert2.min.css") ?>" rel="stylesheet">
	
	<!-- Toastr -->
	<link rel="stylesheet" href="<?= base_url() ?>xhtml/vendor/toastr/css/toastr.min.css">
	
	<!-- Style css -->
	<link href="<?= base_url('xhtml/css/style.css') ?>" rel="stylesheet">
	
	<?= $this->renderSection('styles') ?>
	
	<link href="<?= base_url('assets/css/app.css') ?>" rel="stylesheet">
	
	<style>
        .select2-selection {
            height: 3.5em !important;
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            padding-right: 35px !important;
        }

        #overlay {
            position: fixed;
            top: 0;
            z-index: 9999;
            width: 100%;
            height: 100%;
            display: none;
            background: rgba(0, 0, 0, 0.6);
        }

        .cv-spinner {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px #ddd solid;
            border-top: 4px #2e93e6 solid;
            border-radius: 50%;
            animation: sp-anime 0.8s infinite linear;
        }

        @keyframes sp-anime {
            100% {
                transform: rotate(360deg);
            }
        }

        .is-hide {
            display: none;
        }

        .no-image-sync {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            font-size: 20px;
        }
	</style>
</head>
<body>

<!--*******************
	Preloader start
********************-->
<?= $this->include('layouts/preloader') ?>
<!--*******************
	Preloader end
********************-->

<!--**********************************
	Main wrapper start
***********************************-->
<div id="main-wrapper">
	
	<!--**********************************
		Nav header start | Header start
	***********************************-->
	
	<?= $this->include('layouts/header') ?>
	
	<!--**********************************
		Nav header end | Header end
	***********************************-->
	
	<!--**********************************
		Sidebar start
	***********************************-->
	<?= $this->include('layouts/sidebar') ?>
	<!--**********************************
		Sidebar end
	***********************************-->
	
	<!--**********************************
		Content body start
	***********************************-->
	<div class="content-body">
		<!-- row -->
		<div class="container-fluid" id="container">
			<?= $this->renderSection('content') ?>
		</div>
	</div>
	<!--**********************************
		Content body end
	***********************************-->
	
	<!--**********************************
		Footer start
	***********************************-->
	<div class="footer">
		<div class="copyright">
			<p>Sistema <?= date("Y") ?></p>
		</div>
	</div>
	<!--**********************************
		Footer end
	***********************************-->
	
	<!--**********************************
	   Support ticket button start
	***********************************-->
	
	<!--**********************************
	   Support ticket button end
	***********************************-->


</div>
<div id="dv_aux"></div>
<!--**********************************
	Main wrapper end
***********************************-->

<!--**********************************
	Scripts
***********************************-->
<!-- Required vendors -->
<script src="<?= base_url() ?>public/assets/js/jquery.min.js"></script>
<script src="<?= base_url() ?>xhtml/vendor/global/global.min.js"></script>
<!--<script src="--><?php //= base_url() ?><!--xhtml/vendor/chart.js/Chart.bundle.min.js"></script>-->
<script src="<?= base_url() ?>xhtml/vendor/jquery-nice-select/js/jquery.nice-select.min.js"></script>
<script src="<?= base_url() ?>xhtml/vendor/sweetalert2/dist/sweetalert2.min.js"></script>
<script src="<?= base_url() ?>xhtml/vendor/select2/js/select2.full.min.js"></script>

<!-- Apex Chart -->
<!--<script src="--><?php //= base_url() ?><!--xhtml/vendor/apexchart/apexchart.js"></script>-->

<!--<script src="--><?php //= base_url() ?><!--xhtml/vendor/chart.js/Chart.bundle.min.js"></script>-->

<!-- Chart piety plugin files -->
<!--<script src="--><?php //= base_url() ?><!--xhtml/vendor/peity/jquery.peity.min.js"></script>-->
<!-- sample Dashboard 1 -->
<!-- <script src="<?= base_url() ?>xhtml/js/dashboard/dashboard-1.js"></script> -->

<!--<script src="--><?php //= base_url() ?><!--xhtml/vendor/owl-carousel/owl.carousel.js"></script>-->

<script src="<?= base_url() ?>xhtml/js/custom.js"></script>
<script src="<?= base_url() ?>xhtml/js/dlabnav-init.js"></script>

<!-- Toastr -->
<script src="<?= base_url() ?>xhtml/vendor/toastr/js/toastr.min.js"></script>

<!-- jquery.inputmask -->
<!--<script src="-->
<?php //= base_url() ?><!--public/assets/js/jquery.inputmask.min.js" type="text/javascript"></script>-->

<!-- CkEditor -->
<!--<script src="--><?php //= base_url() ?><!--xhtml/vendor/ckeditor/ckeditor.js"></script>-->

<script src="<?= base_url() ?>public/assets/js/jquery.form.min.js"></script>
<?= $this->renderSection('scripts') ?>
<script src="<?= base_url() ?>public/assets/js/index.js"></script>
<!--<script src="--><?php //= base_url() ?><!--public/assets/js/jquery.maskMoney.min.js"></script>-->

<!--<script src="--><?php //= base_url() ?><!--public/assets/js/qrcode.min.js"></script>-->

<!-- Mapa -->
<!--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC1XWGPu2IBRJpsxwvRUpqLyLW7QmqplPg&libraries=drawing"-->
<!--        async defer></script>-->
<script>
    $("select").select2();
</script>
</body>
</html>