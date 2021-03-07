<?php

    class Pessoa_fone extends TRecord
    {
        const TABLENAME  = 'pessoa_fone';
        const PRIMARYKEY = 'id';
        const IDPOLICY   = 'serial';

        public function __construct( $id = NULL, $callObjectLoad = TRUE ) {
            parent::__construct( $id, $callObjectLoad );
            parent::addAttribute( 'id' );
            parent::addAttribute( 'fone_pessoa_id' );
            parent::addAttribute( 'fone_tipo' );
            parent::addAttribute( 'fone_numero' );
        }

        public function get_tipo_nome() {
            switch ( $this->fone_tipo ) {
                case 1:
                    return "<p style='color:#8B8B83;font-weight:bold;'>Residencial</p>";
                    break;
                case 2:
                    return "<p style='color:#228B22;font-weight:bold;'>Celular</p>";
                    break;
                case 3:
                    return "<p style='color:#B8860B;font-weight:bold;'>Comercial</p>";
                    break;
            }
        }
    }
