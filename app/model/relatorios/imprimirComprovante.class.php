<?php

    use Adianti\Database\TTransaction;

    class imprimirComprovante
    {
        private $comprovante;
        private $arquivo;

        public function __construct( Comprovante $comprovante ) {
            $this->comprovante = $comprovante;
        }

        public function gerarComprovante() {
            require_once( "vendor/autoload.php" );

            try {
                $empresa = new Empresa( 1 );

                $fp    = fopen( 'app/resources/comprovante.html', 'r' );
                $texto = fread( $fp, filesize( 'app/resources/comprovante.html' ) );
                fclose( $fp );

                $imagem = "app/images/".$empresa->logo;

                $texto = str_replace( '[LOGO]', $imagem, $texto );
                $texto = str_replace( '[NR_RECIBO]', str_pad( $this->comprovante->IdComprovante, 6, '0', STR_PAD_LEFT ), $texto );

                $texto = str_replace( '[SEGURADORA]', $this->comprovante->get_seguradora()->nome, $texto );
                $texto = str_replace( '[END_SEGURADORA]', $this->comprovante->get_seguradora()->endereco, $texto );
                $texto = str_replace( '[VALOR]', number_format( $this->comprovante->ValorTotal, 2, ',', '.' ), $texto );
                $texto = str_replace( '[EXTENSO]', Valor_Extenso::numberToExt( $this->comprovante->ValorTotal ), $texto );

                $texto = str_replace( '[PROCESSO]', $this->comprovante->id_processo, $texto );
                $texto = str_replace( '[MARCA_MODELO]', $this->comprovante->get_processo()->marca_modelo, $texto );
                $texto = str_replace( '[PLACA]', $this->comprovante->get_processo()->placa, $texto );
                $texto = str_replace( '[CHASSI]', $this->comprovante->get_processo()->chassi, $texto );
                $texto = str_replace( '[SINISTRO]', $this->comprovante->get_processo()->sinistro, $texto );

                $despesas = "<table class='table table-bordered' style='width:100%;border-collapse:collapse;'>";
                $despesas .= "<tbody>";
                $despesas .= "    <tr>";
                $despesas .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:600px !important;'>DESCRIMINAÇÃO</th>";
                $despesas .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:200px !important;'>VALOR</th>";
                $despesas .= "    </tr>";

                if ( $this->comprovante->get_despesa() ) {
                    $despesas .= $this->comprovante->get_despesa()->observacao;
                }

                $despesas .= "    <tr>";
                $despesas .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:600px !important;'>TOTAL DAS DESPESAS</th>";
                $despesas .= "        <th style='text-align: right;background-color: rgb(255, 231, 206);border:1px solid;width:200px !important;'>".number_format( $this->comprovante->ValorTotal, 2, ',', '.' )."</th>";
                $despesas .= "    </tr>";

                $despesas .= "</tbody>";
                $despesas .= "</table>";
                $texto    = str_replace( '[DESPESAS]', $despesas, $texto );
                $texto    = str_replace( '[DATAREC]', Utilidades::onDataAtual(), $texto );

                $arq_pdf   = 'recibo_'.$this->comprovante->id_processo."_".rand().'.pdf';
                $senha_arq = Utilidades::geraSenha();

                $obj_arq              = new ProcessoArq();
                $obj_arq->id_processo = $this->comprovante->id_processo;
                $obj_arq->nome        = $arq_pdf;
                $obj_arq->data_arq    = date( 'Y-m-d H:i:s' );
                $obj_arq->usuario     = TSession::getValue( 'login' );
                $obj_arq->tipoarq_id  = 16;
                $obj_arq->assinado    = 0;
                $obj_arq->token       = $senha_arq;
                $obj_arq->hash        = '';

                $obj_arq->store();

                $ini  = parse_ini_file( 'app/config/application.ini', TRUE );
                $site = $ini[ 'general' ][ 'site' ];

                $arq = 'app/arquivos/';
                if ( !file_exists( $arq ) ) {
                    mkdir( $arq );
                    chmod( $arq, 0777 );
                }
                $arq .= $this->comprovante->id_processo."/";
                if ( !file_exists( $arq ) ) {
                    mkdir( $arq );
                    chmod( $arq, 0777 );
                }

                require_once( 'app/lib/genqrcode/GenQRCode.php' );
                require_once( 'vendor/autoload.php' );

                $qrcode            = new GenQRCode();
                $params[ 'data' ]  = $site.'/validador/'.$obj_arq->id_arq."/".$senha_arq;
                $params[ 'level' ] = 'H';
                $params[ 'size' ]  = 2;
                $arq_qrcode        = "recibo_".$obj_arq->id_arq."_qrcode.png";
                $end_qrcode        = "app/arquivos/";
                if ( !file_exists( $end_qrcode ) ) {
                    mkdir( $end_qrcode, 0777 );
                }
                $end_qrcode .= $this->comprovante->id_processo."/".$arq_qrcode;

                $params[ 'savename' ] = $end_qrcode;
                $qrcode->generate( $params );

                $texto = str_replace( '[QRCODE]', $end_qrcode, $texto );
                $texto = str_replace( '[CODDOC]', $obj_arq->id_arq, $texto );
                $texto = str_replace( '[SENHADOC]', $senha_arq, $texto );

                $texto = iconv( 'UTF-8', 'ISO-8859-1//IGNORE', $texto );

                $arq           .= $arq_pdf;
                $this->arquivo = $arq;

                $pdf = new mPDF();

                $pdf->allow_charset_conversion = TRUE;

                $pdf->charset_in = 'windows-1252';

                $pdf->SetTitle( "Recibo Reembolso - ".$this->comprovante->IdComprovante );
                $pdf->SetAuthor( "SoftGT Informatica" );
                $pdf->pagenumPrefix = 'Pagina ';
                $pdf->pagenumSuffix = '';
                $pdf->nbpgPrefix    = ' de ';
                define( "PAGINA", "Impresso em ".date( 'd/m/Y - H:i:s  ' )." {PAGENO} "." de  {nb}", TRUE );
                $pdf->SetFont( 'Arial', '', 6 );
                $pdf->SetFooter( PAGINA );
                $pdf->WriteHTML( $texto );
                $pdf->Output( $arq, "F" );

                return TRUE;
            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );

                TTransaction::rollback();
                $this->arquivo = FALSE;

                return FALSE;
            }
        }

        public function get_arquivo() {
            return $this->arquivo;
        }

    }

