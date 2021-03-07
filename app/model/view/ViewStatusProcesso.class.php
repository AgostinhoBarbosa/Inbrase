<?php

    class ViewStatusProcesso extends TRecord
    {
        const TABLENAME  = 'view_status_processo';
        const PRIMARYKEY = 'id_status';
        const IDPOLICY   = 'serial';

        private $status;

        public function __construct( $id_status = NULL, $callObjectLoad = TRUE ) {
            parent::__construct( $id_status, $callObjectLoad );
            parent::addAttribute( 'total' );
        }

        public function get_status() {
            if ( empty( $this->status ) ) {
                $this->status = Status::find( $this->id_status );
            }
            return $this->status;

        }

    }
