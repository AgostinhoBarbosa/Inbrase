<?php
/**
 * Calendario Active Record
 * @author  Agostinho Francisco Barbosa
 */
class Calendario extends TRecord
{
    const TABLENAME = 'calendario';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('hora_inicio');
        parent::addAttribute('hora_final');
        parent::addAttribute('color');
        parent::addAttribute('title');
        parent::addAttribute('description');
    }


}
