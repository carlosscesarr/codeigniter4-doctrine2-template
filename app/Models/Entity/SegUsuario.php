<?php

namespace App\Models\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SegUsuario
 *
 * @ORM\Table(name="seg_usuario")
 * @ORM\Entity
 */
class SegUsuario
{
	/**
	 * @var int
	 *
	 * @ORM\Column(name="idUsuario", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $idusuario;
	
	/**
	 * @var string|null
	 *
	 * @ORM\Column(name="nome", type="string", length=100, nullable=true)
	 */
	private $nome;
	
	/**
	 * @var string|null
	 *
	 * @ORM\Column(name="login", type="string", length=50, nullable=true)
	 */
	private $login;
	
	/**
	 * @var string|null
	 *
	 * @ORM\Column(name="senha", type="string", length=50, nullable=true)
	 */
	private $senha;
	
	/**
	 * @var \DateTime|null
	 *
	 * @ORM\Column(name="ultimoAcesso", type="datetime", nullable=true)
	 */
	private $ultimoacesso;
	
	/**
	 * @var string|null
	 *
	 * @ORM\Column(name="tipo", type="string", length=20, nullable=true, options={"default"="ADMIN"})
	 */
	private $tipo = 'ADMIN';
	
	/**
	 * @var int|null
	 *
	 * @ORM\Column(name="idorgao", type="integer", nullable=true)
	 */
	private $idorgao;
	
	/**
	 * @var bool|null
	 *
	 * @ORM\Column(name="primeiroLogin", type="boolean", nullable=true)
	 */
	private $primeirologin = '0';
	
	public function getNome(): ?string
	{
		return $this->nome;
	}
	
	public function setNome(?string $nome): void
	{
		$this->nome = $nome;
	}
	
	public function getLogin(): ?string
	{
		return $this->login;
	}
	
	public function setLogin(?string $login): void
	{
		$this->login = $login;
	}
	
	public function getSenha(): ?string
	{
		return $this->senha;
	}
	
	public function setSenha(?string $senha): void
	{
		$this->senha = $senha;
	}
	
	public function getUltimoacesso(): ?\DateTime
	{
		return $this->ultimoacesso;
	}
	
	public function setUltimoacesso(?\DateTime $ultimoacesso): void
	{
		$this->ultimoacesso = $ultimoacesso;
	}
	
	public function getTipo(): ?string
	{
		return $this->tipo;
	}
	
	public function setTipo(?string $tipo): void
	{
		$this->tipo = $tipo;
	}
	
	public function getIdorgao(): ?int
	{
		return $this->idorgao;
	}
	
	public function setIdorgao(?int $idorgao): void
	{
		$this->idorgao = $idorgao;
	}
	
	public function getPrimeirologin(): bool|string|null
	{
		return $this->primeirologin;
	}
	
	public function setPrimeirologin(bool|string|null $primeirologin): void
	{
		$this->primeirologin = $primeirologin;
	}
	
	public function getIdusuario(): int
	{
		return $this->idusuario;
	}
}
