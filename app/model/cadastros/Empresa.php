<?php

    class Empresa extends TRecord
    {
        const TABLENAME  = 'empresa';
        const PRIMARYKEY = 'id';
        const IDPOLICY   = 'serial';

        public function __construct( $id = NULL, $callObjectLoad = TRUE ) {
            parent::__construct( $id, $callObjectLoad );
            parent::addAttribute( 'cnpj' );
            parent::addAttribute( 'nome' );
            parent::addAttribute( 'datainicio' );
            parent::addAttribute( 'ativa' );
            parent::addAttribute( 'cep' );
            parent::addAttribute( 'rua' );
            parent::addAttribute( 'numero' );
            parent::addAttribute( 'complemento' );
            parent::addAttribute( 'bairro' );
            parent::addAttribute( 'cidade' );
            parent::addAttribute( 'uf' );
            parent::addAttribute( 'email' );
            parent::addAttribute( 'fone' );
            parent::addAttribute( 'contato' );
            parent::addAttribute( 'portaria' );
            parent::addAttribute( 'numero_os' );
            parent::addAttribute( 'numero_recibo' );
            parent::addAttribute( 'logo' );
        }
    }
