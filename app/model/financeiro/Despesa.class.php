<?php

    use Adianti\Database\TRecord;

    class Despesa extends TRecord
    {
        const TABLENAME  = 'despesa';
        const PRIMARYKEY = 'id';
        const IDPOLICY   = 'serial';

        private $observacao;

        public function __construct( $id = NULL, $callObjectLoad = TRUE ) {
            parent::__construct( $id, $callObjectLoad );
            parent::addAttribute( 'id_comprovante' );
            parent::addAttribute( 'descricao' );
            parent::addAttribute( 'valor' );
        }

        public function get_observacao() {
            $retorno = "";
            if ( !empty( $this->descricao) ) {
                $retorno .= "<tr>";
                $retorno .= "   <td style='width:200px !important;border:1px solid;font-wight:bold;'>".Utilidades::converte_string( $this->descricao )."</td>";
                $retorno .= "   <td style='width:100px !important;border:1px solid;text-align:right;'>".number_format( $this->valor, 2, ',', '.' )."</td>";
                $retorno .= "</tr>";
            }
            return $retorno;
        }
    }
