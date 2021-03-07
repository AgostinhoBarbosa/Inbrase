<?php 
//======================================================================================================//
//Classe de Mapeamento ObjetoxBanco de Dados - OOP
//Autor: Agostinho Francisco Babosa
//Banco de Dados: MariaDB
//Drive de Acesso: PDO
//Data da Geração: 22/05/2017 11:55:47
//======================================================================================================//
class est_veiculos extends TRecord
{
     const TABLENAME  = 'base_estatica.veiculos';
     const PRIMARYKEY = 'dt_atualizacao';
     const IDPOLICY   = 'serial';
 
 
     public function __construct($dt_atualizacao  = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($dt_atualizacao, $callObjectLoad);
        parent::addAttribute('nr_chassi');
        parent::addAttribute('nr_placa');
        parent::addAttribute('nr_faturado');
        parent::addAttribute('nr_anofabricacao');
        parent::addAttribute('cd_municipio');
        parent::addAttribute('sg_uf');
        parent::addAttribute('cd_marcamodelo');
        parent::addAttribute('cd_combustivel');
        parent::addAttribute('vl_potencia');
        parent::addAttribute('vl_capacidadecarga');
        parent::addAttribute('cd_nacionalidade');
        parent::addAttribute('cd_linha');
        parent::addAttribute('nr_carroceria');
        parent::addAttribute('nr_caixacambio');
        parent::addAttribute('nr_eixotraseirodif');
        parent::addAttribute('nr_terceiroeixo');
        parent::addAttribute('nr_motor');
        parent::addAttribute('cd_tipodoctofaturado');
        parent::addAttribute('sg_uffaturado');
        parent::addAttribute('cd_tipodoctoprop');
        parent::addAttribute('nr_anomodelo');
        parent::addAttribute('cd_tipoveiculo');
        parent::addAttribute('cd_especieveiculo');
        parent::addAttribute('cd_tipocarroceria');
        parent::addAttribute('cd_corveiculo');
        parent::addAttribute('nr_qtdpax');
        parent::addAttribute('cd_situacaochassi');
        parent::addAttribute('nr_eixos');
        parent::addAttribute('cd_tipomontagem');
        parent::addAttribute('cd_tipodoctoimportadora');
        parent::addAttribute('nr_identimportadora');
        parent::addAttribute('nr_di');
        parent::addAttribute('dt_registrodi');
        parent::addAttribute('cd_unidadelocalsrf');
        parent::addAttribute('dt_ultimaatualizacao');
        parent::addAttribute('cd_restricao01');
        parent::addAttribute('cd_restricao02');
        parent::addAttribute('cd_restricao03');
        parent::addAttribute('cd_restricao04');
        parent::addAttribute('dt_limiterestricaotrib');
        parent::addAttribute('nr_cilindradas');
        parent::addAttribute('nr_capmaximatracao');
        parent::addAttribute('nr_pesobrutototal');
        parent::addAttribute('cd_situacaoveiculo');
        parent::addAttribute('veiculos_id');
        parent::addAttribute('nr_chassi_vector');
    }
}
?>