<?php

use CodeIgniter\Pager\PagerRenderer;

/**
 * @var PagerRenderer $pager
 */
$pager->setSurroundCount(5);
?>

<nav>
	<ul class="pagination pagination-sm pagination-gutter">
		<li class="page-item page-indicator">
			<a class="page-link" href="<?= $pager->hasPrevious() ? $pager->getPrevious() : 'javascript:void(0)' ?>">
				<i class="la la-angle-left"></i></a>
		</li>
		<?php
		// dd($pager->links());
		foreach ($pager->links() as $link) : ?>
		
			<li class="page-item <?= $link['active'] ? 'active' : '' ?>">
				<a class="page-link" href="<?= $link['active'] ? 'javascript:void(0)' : $link['uri'] ?>"><?= $link['title'] ?></a>
			</li>
		<?php endforeach ?>
		
		<li class="page-item page-indicator">
			<a class="page-link" href="<?=$pager->hasNext() ? $pager->getNext() : 'javascript:void(0)'?>">
				<i class="la la-angle-right"></i></a>
		</li>
	</ul>
</nav>
