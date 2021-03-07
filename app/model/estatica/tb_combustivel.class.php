<?php 
//======================================================================================================//
//Classe de Mapeamento ObjetoxBanco de Dados - OOP
//Autor: Agostinho Francisco Babosa
//Banco de Dados: MariaDB
//Drive de Acesso: PDO
//Data da Geração: 22/05/2017 11:55:47
//======================================================================================================//
class tb_combustivel extends TRecord
{
     const TABLENAME  = 'base_estatica.tb_combustivel';
     const PRIMARYKEY = '"CD_Combustivel"';
     const IDPOLICY   = 'serial';
 
 
     public function __construct($CD_Combustivel  = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($CD_Combustivel, $callObjectLoad);
        parent::addAttribute('NM_Combustivel');
    }
}
?>