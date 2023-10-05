<!-- Table -->
<table class="table table-striped table-hover">
	<thead>
	<tr>
		<th>Data/Hora</th>
		<th>Usuário</th>
		<th>Operação</th>
		<th>Visualizar</th>
	</tr>
	</thead>
	<tbody id="table_lines">
	<?php
	foreach ($auditorias as $row) {
		?>
		<tr>
			<td><?= $row->data_hora ?></td>
			<td><?= $row->login == '' ? 'Não Encontrado' : $row->login ?></td>
			<td><?= $row->operacao ?></td>
			<td>
				<?php if ($row->operacao != 'CADASTRO') { ?>
					<a href="#"
					   onClick="dialogo('<?= base_url() ?><?= $this->router->class ?>/auditoriaDadosAlt/<?= $row->$chave ?>/<?= $row->rev ?>','Auditoria de dados pelo login: <?= $row->login == '' ? 'Não Encontrado' : $row->login ?>')"
					   style="text-align:left;">Ver alteração</a>
				<?php } ?>
			</td>
		</tr>
	<?php } ?>
	</tbody>
	<tfoot>
	<tr>
		<th>Data/Hora</th>
		<th>Usuário</th>
		<th>Operação</th>
		<th>Visualizar</th>
	</tr>
	</tfoot>
</table>
