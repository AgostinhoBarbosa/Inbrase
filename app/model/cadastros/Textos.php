<?php
/**
 * Textos Active Record
 * @author Agostinho Francisco Barbosa
 */
class Textos extends TRecord
{
    const TABLENAME = 'textos';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}


    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('texto');
    }


}
