<?php

    class Cheque extends TRecord
    {
        const TABLENAME  = 'cheque';
        const PRIMARYKEY = 'id';
        const IDPOLICY   = 'serial';

        private $contacorrente;
        private $tipolancamento;

        public function __construct( $id = NULL, $callObjectLoad = TRUE ) {
            parent::__construct( $id, $callObjectLoad );
            parent::addAttribute( 'id' );
            parent::addAttribute( 'contacorrente_id' );
            parent::addAttribute( 'tipolancamento_id' );
            parent::addAttribute( 'data_emissao' );
            parent::addAttribute( 'numero' );
            parent::addAttribute( 'data_compensacao' );
            parent::addAttribute( 'valor' );
            parent::addAttribute( 'favorecido' );
            parent::addAttribute( 'observacao' );
            parent::addAttribute( 'cancelado' );
            parent::addAttribute( 'emitido' );
            parent::addAttribute( 'usuario' );
        }

        public function get_contacorrente() {
            if ( empty( $this->contacorrente ) )
                $this->contacorrente = new Contacorrente( $this->contacorrente_id );

            return $this->contacorrente;
        }

        public function get_tipolancamento() {
            if ( empty( $this->tipolancamento ) )
                $this->tipolancamento = new TipoLancamento( $this->tipolancamento_id );

            return $this->tipolancamento;
        }
    }
