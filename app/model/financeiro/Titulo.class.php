<?php

    class Titulo extends TRecord
    {
        const TABLENAME  = 'titulo';
        const PRIMARYKEY = 'id';
        const IDPOLICY   = 'serial';

        private $pessoa;
        private $tipolancamento;
        private $movimentotitulo;

        public function __construct( $id = NULL, $callObjectLoad = TRUE ) {
            parent::__construct( $id, $callObjectLoad );
            parent::addAttribute( 'pessoa_id' );
            parent::addAttribute( 'tipolancamento_id' );
            parent::addAttribute( 'data_entrada' );
            parent::addAttribute( 'data_vencimento' );
            parent::addAttribute( 'data_emissao' );
            parent::addAttribute( 'valor' );
            parent::addAttribute( 'saldo' );
            parent::addAttribute( 'numero' );
            parent::addAttribute( 'parcela' );
            parent::addAttribute( 'pagar_receber' );
            parent::addAttribute( 'dc' );
            parent::addAttribute( 'tipodoc' );
            parent::addAttribute( 'processo_id' );
            parent::addAttribute( 'observacao' );
        }

        public function onAfterDelete( $object ) {
            return Hstatus::where( 'id_processo', '=', $this->processo_id )->where( 'id_status', '=', 14 )->delete();
        }

        public function onBeforeStore( $object ) {
            if ( empty( $object->saldo ) ) {
                $object->saldo = $object->valor;
            }
        }

        public function get_movimentotitulo() {
            if ( empty( $this->movimentotitulo ) ) {
                $this->movimentotitulo = MovimentoTitulo::where( 'titulo_id', '=', $this->id )->orderBy( 'id' )->load();
            }

            return $this->movimentotitulo;
        }

        public function get_tipolancamento() {
            if ( empty( $this->tipolancamento ) ) {
                $this->tipolancamento = new TipoLancamento( $this->tipolancamento_id );
            }

            return $this->tipolancamento;
        }

        public function get_pessoa() {
            if ( empty( $this->pessoa ) ) {
                $this->pessoa = new Pessoa( $this->pessoa_id );
            }
            return $this->pessoa;
        }
    
        public function onVerMovimentoTitulo() {
            if ( MovimentoTitulo::where( 'titulo_id', '=', $this->id )->count() > 0) {
                return TRUE;
            }
        
            return FALSE;
        }

    }
