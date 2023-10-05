<div class="dlabnav">
	<div class="dlabnav-scroll">
		<ul class="metismenu" id="menu">
			<li>
				<a class="" href="" aria-expanded="false">
					<i class="fas fa-home"></i>
					<span class="nav-text">Inicial</span>
				</a>
			</li>
			
			<?php
			// if ($usuarioLogado['tipo'] == $this->config->item('tipos')['TOTAL']) {
			?>
			<li>
				<a class=" has-arrow" href="javascript:void()" aria-expanded="false">
					<i class="fas fa-table"></i>
					<span class="nav-text">Cadastros</span>
				</a>
				<ul>
					<li>
						<a class="has-arrow" href="javascript:void()" aria-expanded="false">Acessos</a>
						<ul aria-expanded="false" class="left mm-collapse" style="">
							<li><a href="<?= base_url('/usuarios') ?>">Usuários</a></li>
							<li><a href="<?= base_url('/usuarios/restrincaoOperacoes') ?>">Restrição de operações</a></li>
						</ul>
					</li>
					<li>
						<a class="has-arrow" href="javascript:void()" aria-expanded="false">Endereços</a>
						<ul aria-expanded="false" class="left mm-collapse" style="">
							<!--<li><a href="--><?php //= base_url('/enderecos/uf') ?><!--">UF</a></li>-->
							<!--<li><a href="--><?php //= base_url('/enderecos/municipios') ?><!--">Município</a></li>-->
							<li><a href="<?= base_url('/enderecos/zonas') ?>">Zona</a></li>
							<li><a href="<?= base_url('/enderecos/locais') ?>">Local</a></li>
						</ul>
					</li>
					<li>
						<a class="has-arrow" href="javascript:void()" aria-expanded="false">Contatos</a>
						<ul aria-expanded="false" class="left mm-collapse" style="">
							<!--<li><a href="--><?php //= base_url('/enderecos/uf') ?><!--">UF</a></li>-->
							<!--<li><a href="--><?php //= base_url('/enderecos/municipios') ?><!--">Município</a></li>-->
							<li><a href="<?= base_url('/contatos/categorias') ?>">Categorias</a></li>
							<li><a href="<?= base_url('/contatos/orgaos') ?>">Órgãos</a></li>
							<!--<li><a href="--><?php //= base_url('/enderecos/locais') ?><!--">Local</a></li>-->
						</ul>
					</li>
				</ul>
			</li>
			
			<li>
				<a class=" has-arrow" href="javascript:void()" aria-expanded="false">
					<i class="fas fa-table"></i>
					<span class="nav-text">Demandas</span>
				</a>
				<ul>
					<li>
						<a href="<?= base_url('/demandas/tiposdemandas') ?>">Tipos de demandas</a>
					</li>
					<li>
						<a href="<?= base_url('/demandas') ?>">Demandas</a>
					
					</li>
					<li>
						<a class="has-arrow" href="javascript:void()" aria-expanded="false">Relatórios</a>
						<ul aria-expanded="false" class="left mm-collapse" style="">
							<li><a href="<?= base_url('/demandas/relatoriosDetalhes') ?>">Detalhes</a></li>
						</ul>
					</li>
				</ul>
			</li>
			<?php
			// }
			?>
			
			
			
			<?php
			// if ($usuarioLogado['tipo'] == $this->config->item('tipos')['TOTAL']) {
			?>
			
			<li>
				<a class="" href="#config" onclick="abrir_div('<?= base_url() ?>config','container',1)"
				   aria-expanded="false">
					<i class="fas fa-wrench"></i>
					<span class="nav-text">Configurações</span>
				</a>
			</li>
			<?php
			// }
			?>
		</ul>
		
		<div class="copyright">
			<p><strong>sistem</strong> © <?= date("Y") ?></p>
		</div>
	</div>
</div>