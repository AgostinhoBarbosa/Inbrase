<?php
    class Status extends TRecord
    {
        const TABLENAME  = 'status';
        const PRIMARYKEY = 'id';
        const IDPOLICY   = 'serial';


        public function __construct( $id = NULL, $callObjectLoad = TRUE ) {
            parent::__construct( $id, $callObjectLoad );
            parent::addAttribute( 'statu' );
            parent::addAttribute( 'prazo' );
            parent::addAttribute( 'email_cobranca' );
            parent::addAttribute( 'email_liberador' );
            parent::addAttribute( 'email_seguradora' );
            parent::addAttribute( 'status_final' );
        }
    }
