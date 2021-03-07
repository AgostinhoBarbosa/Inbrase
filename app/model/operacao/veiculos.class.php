<?php
class veiculos extends TRecord
{
     const TABLENAME  = 'veiculos';
     const PRIMARYKEY = 'id';
     const IDPOLICY   = 'serial';

     private $seguradoras;


     public function __construct($id  = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('placa');
        parent::addAttribute('nome_proprietario');
        parent::addAttribute('uf');
        parent::addAttribute('tipo');
        parent::addAttribute('combustivel');
        parent::addAttribute('marca_modelo');
        parent::addAttribute('marca');
        parent::addAttribute('cor');
        parent::addAttribute('ano_fabricacao');
        parent::addAttribute('municipio');
        parent::addAttribute('chassi');
        parent::addAttribute('restricao');
        parent::addAttribute('renavam');
        parent::addAttribute('id_seguradora');
        parent::addAttribute('bo_dec');
        parent::addAttribute('bo_rec');
        parent::addAttribute('bo_dev');
        parent::addAttribute('data_cadastro');
        parent::addAttribute('sinistro');
        parent::addAttribute('apolice');
        parent::addAttribute('restricao02');
        parent::addAttribute('restricao03');
        parent::addAttribute('restricao04');
        parent::addAttribute('motor');
    }

    public function get_seguradoras()
    {
        if (empty($this->seguradoras))
        {
            $this->seguradoras = Seguradoras::find($this->id_seguradora);
        }
        return $this->seguradoras;
    }

    public function Importar_Veiculo($placa, $chassi, $motor)
    {
        $url = "http://consultas.consulcar.com.br/webservice/serach_agregados.php?serial=1XB6JSYDOS";
        if (!empty($placa)) {
            $url .= "&placa=" . $placa . "";
        }
        if (!empty($chassi)) {
            if (strlen($chassi) >= 17) {
                $url .= "&chassi=" . $chassi . "";
            }else{
                $url .= "&partechassi=" . $chassi . "";
            }
        }
        if (!empty($motor)) {
            $url .= "&motor=" . $motor . "";
        }
        $url .= "&tipo=1";
//        $url = "http://consultas.consulcar.com.br/webservice/serach_agregados.php?serial=1XB6JSYDOS&placa=".$placa."&tipo=1";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $resposta = curl_exec($ch);
        curl_close($ch);

        $xml_parser = xml_parser_create();
        xml_parse_into_struct($xml_parser, $resposta, $vals, $index);
        xml_parser_free($xml_parser);

        $resposta = [];
        foreach ($vals as $dados) {

            if ($dados['type'] == 'complete') {
                if (isset($dados['value']))
                {
                    $resposta[$dados['tag']] = $dados['value'];
                }
            }
        }

        $grava = FALSE;

        $this->placa    = $placa;
        if (isset($resposta['UF']))
        {
            $this->uf           = $resposta['UF'];
        }
        if (isset($resposta['TIPOVEICULO']))
        {
            $this->tipo         = $resposta['TIPOVEICULO'];
            $grava              = TRUE;
        }
        if (isset($resposta['CHASSI']))
        {
            $this->chassi       = $resposta['CHASSI'];
            $grava              = TRUE;
        }
        if (isset($resposta['MOTOR']))
        {
            $this->motor        = $resposta['MOTOR'];
            $grava              = TRUE;
        }
        if (isset($resposta['COMBUSTIVEL']))
        {
            $this->combustivel  = $resposta['COMBUSTIVEL'];
            $grava              = TRUE;
        }
        if (isset($resposta['MARCAMODELO']))
        {
            $this->marca_modelo = $resposta['MARCAMODELO'];
            $this->marca        = explode('/', $this->marca_modelo)[0];
            $grava              = TRUE;
        }
        if (isset($resposta['FABRICANTE']))
        {
            $this->marca        = $resposta['FABRICANTE'];
            $grava              = TRUE;
        }
        if (isset($resposta['CORVEICULO']))
        {
            $this->cor          = $resposta['CORVEICULO'];
            $grava              = TRUE;
        }
        if (isset($resposta['ANOFABRICACAO']))
        {
            $this->ano          = $resposta['ANOFABRICACAO'];
            $grava              = TRUE;
        }
        if ($grava)
        {
            $this->store();
        }
        return true;
    }
}
?>
