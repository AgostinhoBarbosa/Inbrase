<?php 
//======================================================================================================//
//Classe de Mapeamento ObjetoxBanco de Dados - OOP
//Autor: Agostinho Francisco Babosa
//Banco de Dados: MariaDB
//Drive de Acesso: PDO
//Data da Geração: 22/05/2017 11:55:47
//======================================================================================================//
class tb_especie_veiculo extends TRecord
{
     const TABLENAME  = 'tb_especie_veiculo';
     const PRIMARYKEY = 'CD_EspecieVeiculo';
     const IDPOLICY   = 'serial';
 
 
     public function __construct($CD_EspecieVeiculo  = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($CD_EspecieVeiculo, $callObjectLoad);
        parent::addAttribute('NM_EspecieVeiculo');
    }
}
?>