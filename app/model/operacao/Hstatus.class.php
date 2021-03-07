<?php
    class Hstatus extends TRecord
    {
        const TABLENAME  = 'hstatus';
        const PRIMARYKEY = 'id';
        const IDPOLICY   = 'serial';

        private $status;

        public function __construct( $id = NULL, $callObjectLoad = TRUE ) {
            parent::__construct( $id, $callObjectLoad );
            parent::addAttribute( 'id_status' );
            parent::addAttribute( 'id_processo' );
            parent::addAttribute( 'representante' );
            parent::addAttribute( 'data_cadastro' );
        }

        public function get_status() {
            if ( empty( $this->status ) ) {
                $this->status = Status::find( $this->id_status );
                if ( empty( $this->status ) ) {
                    $this->status        = new Status();
                    $this->status->statu = "<p style='color:#ff0000;font-weight:bold;'>NÂO INFORMADO</p>";
                }
            }
            return $this->status;
        }

    }

?>
