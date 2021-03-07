<?php 
//======================================================================================================//
//Classe de Mapeamento ObjetoxBanco de Dados - OOP
//Autor: Agostinho Francisco Babosa
//Banco de Dados: MariaDB
//Drive de Acesso: PDO
//Data da Geração: 22/05/2017 11:55:47
//======================================================================================================//
class veiculos_modelo extends TRecord
{
     const TABLENAME  = 'base_estatica.veiculos_modelo';
     const PRIMARYKEY = '"cd_marcamodelo"';
     const IDPOLICY   = 'serial';
 
 
     public function __construct($cd_marcamodelo  = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($cd_marcamodelo, $callObjectLoad);
        parent::addAttribute('nm_marcamodelo');
        parent::addAttribute('cd_fabricante');
        parent::addAttribute('cd_segmento');
        parent::addAttribute('cd_subsegmento');
        parent::addAttribute('cd_grupomodeloveiculo');
        parent::addAttribute('veiculos_modelo_id');
    }
}
?>