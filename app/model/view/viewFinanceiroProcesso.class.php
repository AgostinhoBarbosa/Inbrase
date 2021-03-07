<?php

    class viewFinanceiroProcesso extends TRecord
    {
        const TABLENAME  = 'view_financeiro_processo';
        const PRIMARYKEY = 'titulo_id';
        const IDPOLICY   = 'serial';

        private $pessoa;
        private $tipolancamento;
        private $contacorrente;

        public function __construct( $titulo_id = NULL, $callObjectLoad = TRUE ) {
            parent::__construct( $titulo_id, $callObjectLoad );
            parent::addAttribute( 'pessoa_id' );
            parent::addAttribute( 'tipolancamento_id' );
            parent::addAttribute( 'data_entrada' );
            parent::addAttribute( 'data_vencimento' );
            parent::addAttribute( 'data_emissao' );
            parent::addAttribute( 'valor' );
            parent::addAttribute( 'saldo' );
            parent::addAttribute( 'numero' );
            parent::addAttribute( 'pagar_receber' );
            parent::addAttribute( 'dc' );
            parent::addAttribute( 'processo_id' );
            parent::addAttribute( 'contacorrente_id' );
        }

        public function get_contacorrente() {
            if ( empty( $this->contacorrente ) ) {
                $this->contacorrente = Contacorrente::find( $this->contacorrente_id );
                if ( empty( $this->contacorrente ) ) {
                    $this->contacorrente = new Contacorrente();
                }
            }
            return $this->contacorrente;

        }

        public function get_pessoa() {
            if ( empty( $this->pessoa ) ) {
                $this->pessoa = Pessoa::find( $this->pessoa_id );
            }
            return $this->pessoa;

        }

        public function get_tipolancamento() {
            if ( empty( $this->tipolancamento ) ) {
                $this->tipolancamento = TipoLancamento::find( $this->tipolancamento_id );
                if ( empty( $this->tipolancamento ) ) {
                    $this->tipolancamento = new TipoLancamento();
                }
            }
            return $this->tipolancamento;

        }
    }
