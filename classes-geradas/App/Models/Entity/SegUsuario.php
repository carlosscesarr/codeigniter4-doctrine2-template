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


}
