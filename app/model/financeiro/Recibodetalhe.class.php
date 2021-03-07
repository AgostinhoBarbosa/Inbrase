<?php

    class Recibodetalhe extends TRecord
    {
        const TABLENAME  = 'recibodetalhe';
        const PRIMARYKEY = 'id';
        const IDPOLICY   = 'serial';

        public function __construct( $id = NULL, $callObjectLoad = TRUE ) {
            parent::__construct( $id, $callObjectLoad );
            parent::addAttribute( 'id' );
            parent::addAttribute( 'recibo_id' );
            parent::addAttribute( 'nome' );
            parent::addAttribute( 'valor' );
        }

    }
