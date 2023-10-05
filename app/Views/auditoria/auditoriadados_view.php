<?php


if (count($atual) > 0) {
	?>
	<table class="table table-striped table-hover">
		<thead>
		<tr>
			<th>Campos</th>
			<th>Antes</th>
			<th>Depois</th>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach ($atual as $key => $value) {
			?>
			<tr>
				<td><?= strtoupper($key) ?></td>
				<td><?= $anterior[$key] ?></td>
				<td><?= $atual[$key] ?></td>
			</tr>
		<?php } ?>
		</tbody>
		
		<tfoot>
		<tr>
			<th>Campos</th>
			<th>Antes</th>
			<th>Depois</th>
		</tr>
		</tfoot>
	</table>
<?php } else {
	echo "NENHUMA ALTERAÇÃO ENCONTRADA";
} ?>