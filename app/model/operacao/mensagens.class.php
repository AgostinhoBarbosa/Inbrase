<?php

    class mensagens extends TRecord
    {
        const TABLENAME  = 'mensagens';
        const PRIMARYKEY = 'id';
        const IDPOLICY   = 'serial';


        public function __construct( $id = NULL, $callObjectLoad = TRUE ) {
            parent::__construct( $id, $callObjectLoad );
            parent::addAttribute( 'nome' );
            parent::addAttribute( 'email' );
            parent::addAttribute( 'telefone' );
            parent::addAttribute( 'nextel' );
            parent::addAttribute( 'cidade' );
            parent::addAttribute( 'uf' );
            parent::addAttribute( 'msg' );
            parent::addAttribute( 'datahora' );
        }
    }
