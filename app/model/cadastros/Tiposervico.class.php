<?php
    class Tiposervico extends TRecord
    {
        const TABLENAME = 'tiposervico';
        const PRIMARYKEY= 'id';
        const IDPOLICY =  'serial';

        public function __construct($id = NULL, $callObjectLoad = TRUE)
        {
            parent::__construct($id, $callObjectLoad);
            parent::addAttribute('nome');
        }
    }
