<?php

    class Tipoarquivo extends TRecord
    {
        const TABLENAME  = 'tipoarquivo';
        const PRIMARYKEY = 'id';
        const IDPOLICY   = 'serial';

        public function __construct( $id = NULL, $callObjectLoad = TRUE ) {
            parent::__construct( $id, $callObjectLoad );
            parent::addAttribute( 'nome' );
            parent::addAttribute( 'liberacao' );
        }


    }
