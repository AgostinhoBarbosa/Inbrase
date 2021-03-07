<?php

    class Banco extends TRecord
    {
        const TABLENAME  = 'banco';
        const PRIMARYKEY = 'id';
        const IDPOLICY   = 'serial';

        public function __construct( $id = NULL, $callObjectLoad = TRUE ) {
            parent::__construct( $id, $callObjectLoad );
            parent::addAttribute( 'id' );
            parent::addAttribute( 'codigo' );
            parent::addAttribute( 'nome' );
        }

        public function getcontacorrentes() {
            $criteria = new TCriteria();
            $criteria->add( new TFilter( 'banco_id', '=', $this->id ) );
            return Contacorrente::getObjects( $criteria );
        }

    }
