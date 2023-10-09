<?php
if ($acao == "listar") {
	?>
	<script type="text/javascript">
	  $("title").html("<?=$title?>");
	  $(".dashboard_bar").html("<?=$title;?>");
	  $(document).ready(function() {
		    $("#paginacao").find( "a" ).each(function(index, element) {
                console.log(element.href)
		        $( this ).click(function() {
		            abrir_div(element.href.replace('segusuario','<?=$nomeClasse?>'),'container');//caso entity tiver o nome diferente do controller
		            element.href = "#";
		        });
	        });
	
		    $("#btn_novo").click(function(){
		        abrir_div('<?=base_url().$nomeClasse?>/cadastrar','container');
		    });
	  });
	
	  function filtrar_lista(form){
		sendForm(form.id,'container');
		return false;
	  }
	</script>
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header">
				<h4 class="card-title">Filtros</h4>
			</div>
			<div class="card-body">
				
				<form class="d-flex align-items-center" id="form_filtro" onSubmit="return filtrar_lista(this);" action="<?= base_url('/usuario') ?>"
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
				<h5>Total: <?=$listagem['total']?></h5>
				<div class="table-responsive">
					<table class="table table-responsive-md">
						<thead>
						<tr>
							<th><strong>Nome</strong></th>
							<th><strong>Login</strong></th>
							<th><strong>Tipo de usuário</strong></th>
							<!--<th><strong>Acesso</strong></th>-->
							<th><strong>Ações</strong></th>
						</tr>
						</thead>
						<tbody>
						<?php
						/**@var App\Models\Entity\SegUsuario $usuario*/
						foreach ($listagem['lista'] as $index => $usuario) { ?>
							<tr>
								<td><?=$usuario->getNome()?></td>
								<td><?=$usuario->getLogin()?></td>
								<td><?=$usuario->getTipo()?></td>
								<td>
									<div class="d-flex">
										<a onclick="abrir_div('/usuario/alterar/<?=$usuario->getIdusuario()?>')" href="javascript:void(0)" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fas fa-pencil-alt"></i></a>
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
					echo $listagem['paginacao'];
					?>
				</div>
			</div>
		</div>
	</div>
	<?php
}