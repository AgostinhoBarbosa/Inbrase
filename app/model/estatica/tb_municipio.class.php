<?php 
//======================================================================================================//
//Classe de Mapeamento ObjetoxBanco de Dados - OOP
//Autor: Agostinho Francisco Babosa
//Banco de Dados: MariaDB
//Drive de Acesso: PDO
//Data da Geração: 22/05/2017 11:55:47
//======================================================================================================//
class tb_municipio extends TRecord
{
     const TABLENAME  = 'tb_municipio';
     const PRIMARYKEY = 'CD_Municipio';
     const IDPOLICY   = 'serial';
 
 
     public function __construct($CD_Municipio  = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($CD_Municipio, $callObjectLoad);
        parent::addAttribute('NM_Municipio');
        parent::addAttribute('SG_Estado');
    }
}
?>