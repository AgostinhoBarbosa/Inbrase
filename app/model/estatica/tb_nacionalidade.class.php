<?php 
//======================================================================================================//
//Classe de Mapeamento ObjetoxBanco de Dados - OOP
//Autor: Agostinho Francisco Babosa
//Banco de Dados: MariaDB
//Drive de Acesso: PDO
//Data da Geração: 22/05/2017 11:55:47
//======================================================================================================//
class tb_nacionalidade extends TRecord
{
     const TABLENAME  = 'tb_nacionalidade';
     const PRIMARYKEY = 'CD_Nacionalidade';
     const IDPOLICY   = 'serial';
 
 
     public function __construct($CD_Nacionalidade  = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($CD_Nacionalidade, $callObjectLoad);
        parent::addAttribute('NM_Nacionalidade');
    }
}
?>