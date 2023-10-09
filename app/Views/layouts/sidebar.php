<div class="dlabnav">
	<div class="dlabnav-scroll">
		<ul class="metismenu" id="menu">
			<li>
				<a class="" href="" aria-expanded="false">
					<i class="fas fa-home"></i>
					<span class="nav-text">Inicial</span>
				</a>
			</li>
			<li>
				<a class=" has-arrow" href="javascript:void()" aria-expanded="false">
					<i class="fas fa-table"></i>
					<span class="nav-text">Cadastros</span>
				</a>
				<ul>
					<li>
						<a class="has-arrow" href="javascript:void()" aria-expanded="false">Cadastros</a>
						<ul aria-expanded="false" class="left mm-collapse" style="">
							<li><a onclick="abrir_div('<?=base_url()?>usuario')" href="#usuario">Usuários</a></li>
						</ul>
					</li>
				</ul>
			</li>
			
			<li>
				<a class="" href="#config" onclick="abrir_div('<?= base_url() ?>config','container',1)"
				   aria-expanded="false">
					<i class="fas fa-wrench"></i>
					<span class="nav-text">Configurações</span>
				</a>
			</li>
		</ul>
		
		<div class="copyright">
			<p><strong>sistem</strong> © <?= date("Y") ?></p>
		</div>
	</div>
</div>