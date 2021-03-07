<?php 
//======================================================================================================//
//Classe de Mapeamento ObjetoxBanco de Dados - OOP
//Autor: Agostinho Francisco Babosa
//Banco de Dados: MariaDB
//Drive de Acesso: PDO
//Data da Geração: 22/05/2017 11:55:47
//======================================================================================================//
class tb_carroceria extends TRecord
{
     const TABLENAME  = 'tb_carroceria';
     const PRIMARYKEY = 'CD_Carroceria';
     const IDPOLICY   = 'serial';
 
 
     public function __construct($CD_Carroceria  = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($CD_Carroceria, $callObjectLoad);
        parent::addAttribute('NM_Carroceria');
    }
}
?>