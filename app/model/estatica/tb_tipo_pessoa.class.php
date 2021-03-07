<?php 
//======================================================================================================//
//Classe de Mapeamento ObjetoxBanco de Dados - OOP
//Autor: Agostinho Francisco Babosa
//Banco de Dados: MariaDB
//Drive de Acesso: PDO
//Data da Geração: 22/05/2017 11:55:47
//======================================================================================================//
class tb_tipo_pessoa extends TRecord
{
     const TABLENAME  = 'tb_tipo_pessoa';
     const PRIMARYKEY = 'CD_TipoPessoa';
     const IDPOLICY   = 'serial';
 
 
     public function __construct($CD_TipoPessoa  = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($CD_TipoPessoa, $callObjectLoad);
        parent::addAttribute('NM_TipoPessoa');
    }
}
?>