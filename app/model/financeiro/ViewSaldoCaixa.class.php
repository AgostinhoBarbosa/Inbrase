<?php

    class ViewSaldoCaixa extends TRecord
    {
        const TABLENAME  = 'view_saldo_caixa';
        const PRIMARYKEY = 'contacorrente_id';
        const IDPOLICY   = 'max';


        public function __construct( $contacorrente_id = NULL, $callObjectLoad = TRUE ) {
            parent::__construct( $contacorrente_id, $callObjectLoad );
            parent::addAttribute( 'data_movimento' );
            parent::addAttribute( 'credito_no_dia' );
            parent::addAttribute( 'debito_no_dia' );
            parent::addAttribute( 'saldo_no_dia' );
            parent::addAttribute( 'acumulado' );
        }


    }
