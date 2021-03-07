<?php 
//======================================================================================================//
//Classe de Mapeamento ObjetoxBanco de Dados - OOP
//Autor: Agostinho Francisco Babosa
//Banco de Dados: MariaDB
//Drive de Acesso: PDO
//Data da Geração: 22/05/2017 11:55:47
//======================================================================================================//
class tb_restricoes extends TRecord
{
     const TABLENAME  = 'tb_restricoes';
     const PRIMARYKEY = 'CD_Restricao';
     const IDPOLICY   = 'serial';
 
 
     public function __construct($CD_Restricao  = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($CD_Restricao, $callObjectLoad);
        parent::addAttribute('NM_Restricao');
    }
}
?>