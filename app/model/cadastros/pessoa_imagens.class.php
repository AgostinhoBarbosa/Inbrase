<?php

    class pessoa_imagens extends TRecord
    {
        const TABLENAME  = 'pessoa_imagens';
        const PRIMARYKEY = 'id';
        const IDPOLICY   = 'serial';

        private $pessoa;

        public function __construct( $id = NULL, $callObjectLoad = TRUE ) {
            parent::__construct( $id, $callObjectLoad );
            parent::addAttribute( 'id' );
            parent::addAttribute( 'arquivo_pessoa_id' );
            parent::addAttribute( 'arquivo_imagem' );
        }

        public function get_pessoa() {
            if ( empty( $this->pessoa ) )
                $this->pessoa = new Pessoa( $this->arquivo_pessoa_id );

            return $this->pessoa;
        }

        public function set_pessoa( pessoa $object ) {
            $this->pessoa            = $object;
            $this->arquivo_pessoa_id = $object->id;
        }

    }
