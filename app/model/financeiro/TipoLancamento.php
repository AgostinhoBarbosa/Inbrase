<?php

    class TipoLancamento extends TRecord
    {
        const TABLENAME  = 'tipolancamento';
        const PRIMARYKEY = 'id';
        const IDPOLICY   = 'serial';

        public function __construct( $id = NULL, $callObjectLoad = TRUE ) {
            parent::__construct( $id, $callObjectLoad );
            parent::addAttribute( 'id' );
            parent::addAttribute( 'nome' );
        }

        public function getcaixa() {
            $criteria = new TCriteria();
            $criteria->add( new TFilter( 'tipolancamento_id', '=', $this->id ) );
            return Caixa::getObjects( $criteria );
        }


        public function getmovimentotitulo() {
            $criteria = new TCriteria();
            $criteria->add( new TFilter( 'tipolancamento_id', '=', $this->id ) );
            return MovimentoTitulo::getObjects( $criteria );
        }
    }
