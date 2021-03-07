<?php

    class Recibos extends TRecord
    {
        const TABLENAME  = 'recibos';
        const PRIMARYKEY = 'id';
        const IDPOLICY   = 'serial';

        private $recibodetalhe;
        private $pessoa;
        private $processo;

        public function __construct( $id = NULL, $callObjectLoad = TRUE ) {
            parent::__construct( $id, $callObjectLoad );
            parent::addAttribute( 'id' );
            parent::addAttribute( 'pessoa_id' );
            parent::addAttribute( 'processo_id' );
            parent::addAttribute( 'data_emissao' );
            parent::addAttribute( 'valor_recibo' );
            parent::addAttribute( 'status' );
        }

        public function get_processo() {
            if ( empty( $this->processo ) )
                $this->processo = new Processo( $this->processo_id );

            return $this->processo;
        }

        public function get_pessoa() {
            if ( empty( $this->pessoa ) ) {
                $this->pessoa = Pessoa::find( $this->pessoa_id );
                if ( empty( $this->pessoa ) ) {
                    $this->pessoa = new Pessoa();
                }
            }
            return $this->pessoa;
        }

        public function onVerificaTitulo() {
            $count = Titulo::where( 'numero', '=', $this->id )->where( 'tipolancamento_id', 'IN', [36,91,94] )->count();

            if ( $count > 0 ) {
                return TRUE;
            } else {
                return FALSE;
            }
        }

        public function onVerificaStatus() {
            $count = Hstatus::where( 'id_processo', '=', $this->processo_id )->where( 'id_status', '=', 14 )->count();

            if ( $count > 0 ) {
                return TRUE;
            } else {
                return FALSE;
            }
        }

        public function get_observacao() {
            $retorno = "";
            if ( $this->get_recibodetalhe() ) {
                $detalhes = $this->get_recibodetalhe();
                foreach ( $detalhes as $detalhe ) {
                    $retorno .= "<tr>";
                    $retorno .= "   <td style='width:200px !important;border:1px solid;font-wight:bold;'>".$detalhe->nome."</td>";
                    $retorno .= "   <td style='width:100px !important;border:1px solid;text-align:right;'>".number_format( floatval( $detalhe->valor ), 2, ',', '.' )."</td>";
                    $retorno .= "</tr>";
                }
            }
            return $retorno;
        }

        public function get_recibodetalhe() {
            if ( empty( $this->recibodetalhe ) ) {
                $this->recibodetalhe = Recibodetalhe::where( 'recibo_id', '=', $this->id )->load();
                if ( empty( $this->recibodetalhe ) ) {
                    $this->recibodetalhe = [];
                }
            }
            return $this->recibodetalhe;
        }

    }
