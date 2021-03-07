<?php
    class arquivos extends TRecord
    {
        const TABLENAME  = 'arquivos';
        const PRIMARYKEY = 'id';
        const IDPOLICY   = 'serial';


        public function __construct( $id = NULL, $callObjectLoad = TRUE ) {
            parent::__construct( $id, $callObjectLoad );
            parent::addAttribute( 'antigo_nome_arquivo' );
            parent::addAttribute( 'novo_nome_arquivo' );
            parent::addAttribute( 'placa' );
            parent::addAttribute( 'processo' );
            parent::addAttribute( 'data' );
        }
    }

