<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="format-detection" content="telephone=no">
	
	<!-- PAGE TITLE HERE -->
	<title>sistema - Login</title>
	
	<link href="<?= base_url() ?>xhtml/css/style.css" rel="stylesheet">
	<link href="<?= base_url() ?>xhtml/vendor/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">
	<!-- Toastr -->
	<link rel="stylesheet" href="<?= base_url() ?>xhtml/vendor/toastr/css/toastr.min.css">
</head>

<body class="vh-100">
<div class="authincation h-100">
	<div class="container h-100">
		<div class="row justify-content-center h-100 align-items-center">
			<div class="col-md-6">
				<div class="authincation-content">
					<div class="row no-gutters">
						<div class="col-xl-12 dvLogin">
							<div class="auth-form">
								<div class="text-center mb-3">
									<a href=""><img src="<?= base_url() ?>assets/imagens/logo-infoassessor.png" width="100%" alt=""></a>
								</div>
								<h4 class="text-center mb-4">Realizar Login</h4>
								<?php
								if (session()->has('erro')) { ?>
									<div class="alert alert-danger">
										<span><?=session()->getFlashdata('erro')?></span>
									</div>
									<?php
								} elseif (session()->has('erros')) { ?>
									<div class="alert alert-danger">
										<?php
										foreach (session()->getFlashdata('erros') as $index => $erro) { ?>
											<span class="d-block"><?=$erro?></span>
											<?php
										}
										?>
									</div>
									<?php
								}
								?>
								
								<form action="<?= base_url() ?>/login/logar" method="post">
									<div class="mb-3">
										<label for="login" class="mb-1"><strong>Usuário:</strong></label>
										<input type="text" id="login" class="form-control" name="login" required value="<?=$login ?? ''?>">
									</div>
									<div class="mb-3">
										<label for="senha" class="mb-1"><strong>Senha:</strong></label>
										<input id="senha" type="password" class="form-control" value="" required name="senha">
									</div>
									<div class="row d-flex justify-content-between mt-4 mb-2">
										<div class="mb-3">
											<!--<a href="javascript:;" id="recuperaSenha">Esqueceu a senha?</a>-->
										</div>
									</div>
									<div class="text-center">
										<button type="submit" class="btn btn-primary btn-block">Entrar</button>
									</div>
								</form>
								
								<?php
								if (isset($_GET['erro'])) {
									?>
									<br>
									<div class="alert alert-danger solid alert-end-icon alert-dismissible fade show">
										<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor"
										     stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"
										     class="me-2">
											<polygon
												points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon>
											<line x1="15" y1="9" x2="9" y2="15"></line>
											<line x1="9" y1="9" x2="15" y2="15"></line>
										</svg>
										<strong>Erro!</strong> Usuário ou senha inválidos!
										<button type="button" class="btn-close" data-bs-dismiss="alert"
										        aria-label="btn-close">
										</button>
									</div>
									<?php
								}
								?>
							</div>
						</div>
						
						
						<div class="col-xl-12 dvRecupera" style="display: none">
							<div class="auth-form">
								<span style="position: absolute;font-size: 1.5rem;cursor: pointer;"><i
										class="fa fa-arrow-left"></i></span>
								
								<div class="text-center mb-3">
									<a href="index.html"><img src="<?= base_url() ?>images/logo.jpeg" width="100%"
									                          alt=""></a>
								</div>
								<h4 class="text-center mb-4">Recuperar acesso</h4>
								<form action="<?= base_url() ?>index.php/login/recuperarSenha">
									<div class="mb-3">
										<label class="mb-1"><strong>Informe seu E-mail</strong></label>
										<input type="email" class="form-control" name="email" value="">
									</div>
									
									<div class="text-center">
										<button type="submit" class="btn btn-primary btn-block">Enviar</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<!--**********************************
	Scripts
***********************************-->
<script src="<?= base_url() ?>js/jquery.min.js"></script>
<!-- Required vendors -->
<script src="<?= base_url() ?>xhtml/vendor/global/global.min.js"></script>
<script src="<?= base_url() ?>xhtml/vendor/sweetalert2/dist/sweetalert2.min.js"></script>
<script src="<?= base_url() ?>xhtml/js/custom.js"></script>
<script src="<?= base_url() ?>xhtml/js/dlabnav-init.js"></script>
<!-- <script src="<?= base_url() ?>xhtml/js/styleSwitcher.js"></script> -->
<!-- Toastr -->
<script src="<?= base_url() ?>xhtml/vendor/toastr/js/toastr.min.js"></script>

<!-- jquery.inputmask -->
<script src="<?= base_url() ?>js/jquery.inputmask.min.js" type="text/javascript"></script>
<script src="<?= base_url() ?>js/index.js"></script>
<script type="text/javascript">
    $('#recuperaSenha').click(() => {
        $('.dvRecupera').css('display', 'block')
        $('.dvLogin').css('display', 'none')
    });
    
    $('.fa-arrow-left').click(() => {
        $('.dvRecupera').css('display', 'none')
        $('.dvLogin').css('display', 'block')
    });
</script>
</body>
</html>