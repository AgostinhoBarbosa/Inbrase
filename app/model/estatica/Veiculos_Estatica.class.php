<?php
//======================================================================================================//
//Classe de Mapeamento ObjetoxBanco de Dados - OOP
//Autor: Agostinho Francisco Babosa
//Banco de Dados: PostgreSQL
//Drive de Acesso: PDO
//Data da Geração: 10/11/2017 11:40:37
//======================================================================================================//
class Veiculos_Estatica extends TRecord
{
    const TABLENAME = 'base_estatica.veiculos';
    const PRIMARYKEY= 'veiculos_id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    private $tb_combustivel;
    private $tb_tipo_veiculo;
    private $veiculos_cor;
    private $veiculos_modelo;
    /**
     * Constructor method
     */
    public function __construct($veiculos_id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($veiculos_id, $callObjectLoad);
        parent::addAttribute('dt_atualizacao');
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
        parent::addAttribute('nr_chassi_vector');
    }

    public function get_tb_combustivel()
    {
        if (empty($this->tb_combustivel))
        {
            $this->tb_combustivel = tb_combustivel::find($this->cd_combustivel);
        }
        return $this->tb_combustivel;
    }
    
    public function get_tb_tipo_veiculo()
    {
        if (empty($this->tb_tipo_veiculo))
        {
            $this->tb_tipo_veiculo = tb_tipo_veiculo::find($this->cd_tipoveiculo);
        }
        return $this->tb_tipo_veiculo;
    }
    
    public function get_veiculos_cor()
    {
        if (empty($this->veiculos_cor))
        {
            $this->veiculos_cor = veiculos_cor::find($this->cd_corveiculo);
        }
        return $this->veiculos_cor;
    }

    public function get_veiculos_modelo()
    {
        if (empty($this->veiculos_modelo))
        {
            $this->veiculos_modelo = veiculos_modelo::find($this->cd_marcamodelo);
        }
        return $this->veiculos_modelo;
    }

}
