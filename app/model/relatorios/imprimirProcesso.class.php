<?php
class imprimirProcesso
{
    private $processoa;
    private $arquivo;

    public function __construct( Processo $processo)
    {
        $this->processoa = $processo;
        return $this->gerarTermo();
    }

    public function get_arquivo()
    {
        return $this->arquivo;
    }
    function gerarTermo()
    {
        require_once("vendor/autoload.php");

        try
        {
            $empresa = new Empresa( 1);
            $textos  = Textos::find(2);
            $texto   = $textos->texto;

            $data_processo = date('d/m/Y');

            $imagem  = "app/images/".$empresa->logo;

            $texto   = str_replace('[LOGO]',             $imagem, $texto);
            $texto   = str_replace('[DATA_EMISSAO]',     $data_processo, $texto);

            $texto   = str_replace('[SEGURADORA]',       $this->processoa->seguradoras->nome, $texto);
            $texto   = str_replace('[GERENTE]',          $this->processoa->seguradoras->gerente, $texto);

            $texto   = str_replace('[PROCESSO]',         $this->processoa->id, $texto);
            $texto   = str_replace('[SEGURADO]',         $this->processoa->nome_segurado, $texto);
            $texto   = str_replace('[MARCA_MODELO]',     $this->processoa->marca_modelo, $texto);
            $texto   = str_replace('[TIPO]',             $this->processoa->tipo, $texto);
            $texto   = str_replace('[ANO_FAB]',          $this->processoa->ano, $texto);
            $texto   = str_replace('[UF_PLACA]',         $this->processoa->placa."/".$this->processoa->uf, $texto);
            $texto   = str_replace('[CHASSI]',           $this->processoa->chassi, $texto);
            $texto   = str_replace('[RENAVAM]',          $this->processoa->renavam, $texto);
            $texto   = str_replace('[SINISTRO]',         $this->processoa->sinistro, $texto);
            $texto   = str_replace('[CIDADE_DEC]',       $this->processoa->cidade_dec."(".$this->processoa->uf_dec.")", $texto);
            $texto   = str_replace('[DP_DEC]',           $this->processoa->dp_dec, $texto);
            $texto   = str_replace('[BO_DEC]',           $this->processoa->bo_dec, $texto);
            $texto   = str_replace('[DATA_DEC]',         TDate::date2br($this->processoa->data_dec), $texto);
            $texto   = str_replace('[CIDADE_REC]',       $this->processoa->cidade_rec."(".$this->processoa->uf_rec.")", $texto);
            $texto   = str_replace('[DP_REC]',           $this->processoa->dp_rec, $texto);
            $texto   = str_replace('[BO_REC]',           $this->processoa->bo_rec, $texto);
            $texto   = str_replace('[DATA_REC]',         TDate::date2br($this->processoa->data_rec), $texto);
            $texto   = str_replace('[CIDADE_DEV]',       $this->processoa->cidade_dev."(".$this->processoa->uf_dev.")", $texto);
            $texto   = str_replace('[DP_DEV]',           $this->processoa->dp_dev, $texto);
            $texto   = str_replace('[BO_DEV]',           $this->processoa->bo_dev, $texto);
            $texto   = str_replace('[DATA_ENTREGA_DEV]', TDate::date2br($this->processoa->data_entrega_dev), $texto);
            $texto   = str_replace('[RESPONSAVEL_DEV]',  $this->processoa->responsavel_dev, $texto);
            $texto   = str_replace('[OBS_DEV]',          $this->processoa->obs_dev, $texto);

            $arq_pdf         = 'Processo_'.$this->processoa->id."_".rand().'.pdf';
            $arq             = 'tmp/'.$arq_pdf;

            $pdf = new mPDF();
	        $pdf->SetTitle("Processo - ".$this->processoa->id);
	        $pdf->SetAuthor("SoftGT Informatica");
	        $pdf->pagenumPrefix = 'Pagina ';
	        $pdf->pagenumSuffix = '';
	        $pdf->nbpgPrefix = ' de ';
	        define("PAGINA", "Impresso em ".date('d/m/Y - H:i:s  ')." {PAGENO} "." de  {nb}",TRUE);
	        $pdf->SetFont('Arial',   '', 6);
	        $pdf->SetFooter(PAGINA);
	        $pdf->WriteHTML($texto);
	        $this->arquivo = $arq;
	        $pdf->Output($arq,"F");
            return true;
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());

            $this->arquivo = false;
            return false;
        }
    }

}
