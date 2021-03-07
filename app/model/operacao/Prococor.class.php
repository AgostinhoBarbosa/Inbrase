<?php

    class Prococor extends TRecord
    {
        const TABLENAME  = 'prococor';
        const PRIMARYKEY = 'id';
        const IDPOLICY   = 'serial';

        public function __construct( $id = NULL, $callObjectLoad = TRUE ) {
            parent::__construct( $id, $callObjectLoad );
            parent::addAttribute( 'id_processo' );
            parent::addAttribute( 'usuario' );
            parent::addAttribute( 'data_ocor' );
            parent::addAttribute( 'historico' );
        }
    }
