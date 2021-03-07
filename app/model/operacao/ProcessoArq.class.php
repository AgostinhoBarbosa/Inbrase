<?php
class ProcessoArq extends TRecord
{
    const TABLENAME = 'processo_arq';
    const PRIMARYKEY= 'id_arq';
    const IDPOLICY =  'serial';

    private $Tipoarquivo;

    public function __construct($id_arq = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id_arq, $callObjectLoad);
        parent::addAttribute('id_processo');
        parent::addAttribute('data_arq');
        parent::addAttribute('usuario');
        parent::addAttribute('nome');
        parent::addAttribute('tipoarq_id');
        parent::addAttribute('assinado');
        parent::addAttribute('token');
        parent::addAttribute('hash');
    }

    public function get_Tipoarquivo(){
        if (empty($this->Tipoarquivo) && $this->tipoarq_id)
        {
                $this->Tipoarquivo = Tipoarquivo::find($this->tipoarq_id);
                if (empty($this->Tipoarquivo))
                {
                    $this->Tipoarquivo = new Tipoarquivo;
                }
        }
        return $this->Tipoarquivo;
    }

    public function get_path_arq() {
        return 'app/arquivos/'.$this->id_processo.'/'.$this->nome;
    }

    public function onAfterDelete( $object  ) {
        $path_arq = 'app/arquivos/'.$object->id_processo."/".$object->nome;
        if ( file_exists( $path_arq ) ) {
            unlink( $path_arq );
        }
    }
}
