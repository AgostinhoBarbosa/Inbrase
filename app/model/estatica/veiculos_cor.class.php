<?php 
//======================================================================================================//
//Classe de Mapeamento ObjetoxBanco de Dados - OOP
//Autor: Agostinho Francisco Babosa
//Banco de Dados: MariaDB
//Drive de Acesso: PDO
//Data da Geração: 22/05/2017 11:55:47
//======================================================================================================//
class veiculos_cor extends TRecord
{
     const TABLENAME  = 'base_estatica.veiculos_cor';
     const PRIMARYKEY = '"veiculos_cor_id"';
     const IDPOLICY   = 'serial';
 
 
     public function __construct($veiculos_cor_id  = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($veiculos_cor_id, $callObjectLoad);
        parent::addAttribute('cd_corveiculo');
        parent::addAttribute('nm_corveiculo');
    }
}
?>