<?php 
//======================================================================================================//
//Classe de Mapeamento ObjetoxBanco de Dados - OOP
//Autor: Agostinho Francisco Babosa
//Banco de Dados: MariaDB
//Drive de Acesso: PDO
//Data da Geração: 22/05/2017 11:55:47
//======================================================================================================//
class tb_tipo_veiculo extends TRecord
{
     const TABLENAME  = 'base_estatica.tb_tipo_veiculo';
     const PRIMARYKEY = 'CD_TipoVeiculo';
     const IDPOLICY   = 'serial';
 
 
     public function __construct($CD_TipoVeiculo  = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($CD_TipoVeiculo, $callObjectLoad);
        parent::addAttribute('NM_TipoVeiculo');
    }
}
?>