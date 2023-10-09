<?php

namespace App\Libraries;

class Pagination
{
	
	public $paginaAtual;
	public $totalRegistros;
	public $registrosPorPagina;
	
	public $quantidadeLinksExibir;
	
	public $baseUrl;
	
	const NUMERO_PADRAO_LINKS = 8;
	
	/**
	 * Constructor
	 *
	 * @return    void
	 */
	public function __construct($paginaAtual, $totalRegistros, $registrosPorPagina, $quantidadeLinksExibir = 5, $baseUrl = '')
	{
		$this->paginaAtual = $paginaAtual;
		$this->registrosPorPagina = $registrosPorPagina;
		$this->totalRegistros = $totalRegistros;
		$this->quantidadeLinksExibir = $quantidadeLinksExibir;
		$this->baseUrl = $baseUrl;
	}
	
	/**
	 * @return string
	 */
	public function criarLinks()
	{
		$numPaginas = ceil($this->totalRegistros / $this->registrosPorPagina);
		$meio = ceil($this->quantidadeLinksExibir / 2);
		
		$paginaAtual = $this->paginaAtual;
		
		// Limita a página inicial
		$paginaInicial = max(0, $paginaAtual - $meio);
		
		// Limita a página final
		$paginaFinal = min($numPaginas, $paginaInicial + $this->quantidadeLinksExibir);
		
		// Ajusta a página inicial se a página final for a última página
		$paginaInicial = max(0, $paginaFinal - $this->quantidadeLinksExibir);
		
		$links = '<nav id="paginacao">';
		$links .= '<ul class="pagination pagination-sm pagination-gutter">';
		
		// Link para a primeira página
		if ($paginaAtual > 0) {
			$baseUrl = $this->baseUrl != '' ? $this->baseUrl : '?pagina=1';
			$links .= '<li class="page-item page-indicator"><a class="page-link" href="'.$baseUrl.'">Primeira</a></li>';
		}
		
		// Link para página anterior
		if ($paginaAtual > 0) {
			$baseUrl = $this->baseUrl != '' ? $this->baseUrl : "?pagina=". ($this->paginaAtual - 1);
			$links .= '<li class="page-item page-indicator">
				<a class="page-link" href="'.$baseUrl.'"><i class="la la-angle-left"></i></a></li>';
		}
		
		// Links para páginas
		for ($i = $paginaInicial; $i < $paginaFinal; $i++) {
			if ($i == $paginaAtual) {
				$links .= '<li class="page-item active"><a  class="page-link" href="#">' . ($i + 1) . '</a></li>';
			} else {
				$baseUrl = $this->baseUrl != '' ? $this->baseUrl . '/'.$i : "?pagina=" . $i;
				$links .= '<li class="page-item"><a class="page-link" href="'.$baseUrl.'">' . ($i + 1). '</a></li>';
			}
		}
		
		// Link para próxima página
		if ($paginaAtual < $numPaginas) {
			$baseUrl = $this->baseUrl != '' ? $this->baseUrl : "?pagina=" . ($paginaAtual + 1);
			$links .= '<li class="page-item page-indicator">
				<a class="page-link" href="'.$baseUrl.'"><i class="la la-angle-right"></i></a></li>';
		}
		
		// Link para a última página
		if ($paginaAtual < $numPaginas) {
			$baseUrl = $this->baseUrl != '' ? $this->baseUrl : "?pagina=" . $numPaginas;

			$links .= '<li class="page-item"><a class="btn btn-xs btn-primary" href="'.$baseUrl.'">Última</a></li>';
		}
		
		$links .= '</ul></nav>';
		
		return $links;
	}
}
