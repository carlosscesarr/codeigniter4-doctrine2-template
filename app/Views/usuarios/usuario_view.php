<?= $this->extend('layouts/template') ?>
<?php
$this->section('content');
if ($acao == "listar") {
	?>
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header">
				<h4 class="card-title">Filtros</h4>
			</div>
			<div class="card-body">
				
				<form class="d-flex align-items-center" id="form_filtro" onSubmit="" action="<?= base_url('/usuario') ?>"
				      method="get">
					<div class="mb-2 mx-sm-3">
						<input type="text" id="nome" name="nome" class="form-control" style="width: 100%;" maxlength="50"
						       value="<?= isset($filtros['nome']) ? $filtros['nome'] : '' ?>" placeholder="Nome"/>
					</div>
					
					<button type="submit" class="btn btn-primary mb-2">Buscar</button>
				</form>
			
			</div>
		
		</div>
	</div>
	
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header">
				<h4 class="card-title">Listagem de usuários</h4>
			</div>
			<div class="card-body">
				<h5>Tota: <?=$total?></h5>
				<div class="table-responsive">
					<table class="table table-responsive-md">
						<thead>
						<tr>
							<th><strong>Nome</strong></th>
							<th><strong>Login</strong></th>
							<th><strong>Tipo de usuário</strong></th>
							<th><strong>Situação</strong></th>
							<!--<th><strong>Acesso</strong></th>-->
							<th><strong>Ações</strong></th>
						</tr>
						</thead>
						<tbody>
						<?php
						foreach ($usuarios as $index => $usuario) { ?>
							<tr>
								<td><?=$usuario['Nome']?></td>
								<td><?=$usuario['Login']?></td>
								<td><?=$usuario['tipoUsuario']?></td>
								<td>
									<div class="d-flex align-items-center">
										<i class="fa fa-circle text-<?=$usuario['Ativo'] == 1 ? 'success' : 'danger'?> me-1"></i> <?=$usuario['Ativo'] == 1 ? 'Ativo' : 'Inativo'?>
									</div>
								</td>
								<td>
									<div class="d-flex">
										<a href="<?=base_url("/usuario/alterar/" . $usuario['IdUsuario'])?>" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fas fa-pencil-alt"></i></a>
										<!--<a href="#" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></a>-->
									</div>
								</td>
							</tr>
							<?php
						}
						?>
						</tbody>
					</table>
				</div>
				<div>
					<?php
					echo $paginacao;
					?>
				</div>
			</div>
		</div>
	</div>
	<?php
}
$this->endSection();