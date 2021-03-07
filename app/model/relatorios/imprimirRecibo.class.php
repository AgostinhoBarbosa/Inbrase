<?php

    class imprimirRecibo
    {
        private $recibos;
        private $arquivo;

        public function __construct( Recibos $dados ) {
            $this->recibos = $dados;

        }

        public function gerarRecibos() {
            require_once( "vendor/autoload.php" );

            try {
                $empresa = new Empresa( 1 );

                $fp    = fopen( 'app/resources/recibo.html', 'r' );
                $texto = fread( $fp, filesize( 'app/resources/recibo.html' ) );
                fclose( $fp );

                $imagem = "app/images/".$empresa->logo;

                $texto = str_replace( '[LOGO]', $imagem, $texto );
                $texto = str_replace( '[NR_RECIBO]', str_pad( $this->recibos->id, 6, '0', STR_PAD_LEFT ), $texto );

                $texto = str_replace( '[VALOR]', number_format( $this->recibos->valor_recibo, 2, ',', '.' ), $texto );
                $texto = str_replace( '[EXTENSO]', Valor_Extenso::numberToExt( $this->recibos->valor_recibo ), $texto );

                $texto = str_replace( '[PROCESSO]', $this->recibos->processo_id, $texto );
                $texto = str_replace( '[MARCA_MODELO]', $this->recibos->get_processo()->marca_modelo, $texto );
                $texto = str_replace( '[PLACA]', $this->recibos->get_processo()->placa, $texto );
                $texto = str_replace( '[CHASSI]', $this->recibos->get_processo()->chassi, $texto );
                $texto = str_replace( '[SINISTRO]', $this->recibos->get_processo()->sinistro, $texto );
                $texto = str_replace( '[DOC_PRESTADOR]', $this->recibos->get_pessoa()->documento, $texto );
                $texto = str_replace( '[NOME_PRESTADOR]', $this->recibos->get_pessoa()->nome, $texto );

                $despesas = "<table class='table table-bordered' style='width:100%;border-collapse:collapse;'>";
                $despesas .= "<tbody>";
                $despesas .= "    <tr>";
                $despesas .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:600px !important;'>DESCRIMINAÇÃO</th>";
                $despesas .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:200px !important;'>VALOR</th>";
                $despesas .= "    </tr>";

                if ( $this->recibos->get_recibodetalhe() ) {
                    foreach ( $this->recibos->get_recibodetalhe() as $detalhe ) {
                        $despesas .= "    <tr>";
                        $despesas .= "        <td style='text-align: left;border:1px solid;width:600px !important;'>$detalhe->nome</td>";
                        $despesas .= "        <td style='text-align: right;border:1px solid;width:200px !important;'>".number_format( $detalhe->valor, 2, ',', '.' )."</td>";
                        $despesas .= "    </tr>";
                    }
                }

                $despesas .= "    <tr>";
                $despesas .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:600px !important;'>TOTAL DAS DESPESAS</th>";
                $despesas .= "        <th style='text-align: right;background-color: rgb(255, 231, 206);border:1px solid;width:200px !important;'>".number_format( $this->recibos->valor_recibo, 2, ',', '.' )."</th>";
                $despesas .= "    </tr>";

                $despesas .= "</tbody>";
                $despesas .= "</table>";
                $texto    = str_replace( '[DESPESAS]', $despesas, $texto );
                $texto    = str_replace( '[DATAREC]', Utilidades::onDataAtual(), $texto );

                $arq_pdf   = 'recibo_'.$this->recibos->id."_".rand().'.pdf';
                $senha_arq = Utilidades::geraSenha();

                $obj_arq              = new ProcessoArq();
                $obj_arq->id_processo = $this->recibos->processo_id;
                $obj_arq->nome        = $arq_pdf;
                $obj_arq->data_arq    = date( 'Y-m-d H:i:s' );
                $obj_arq->usuario     = TSession::getValue( 'login' );
                $obj_arq->tipoarq_id  = 15;
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

                $arq           .= $arq_pdf;
                $this->arquivo = $arq;

                $pdf = new mPDF();

                $pdf->allow_charset_conversion = TRUE;

                $pdf->charset_in = 'utf-8';

                $pdf->SetTitle( "Recibo Prestador - ".$this->recibos->id );
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
                new TMessage( 'error', '<b>Erro</b> '.$e->getMessage() );

                TTransaction::rollback();
                $this->arquivo = FALSE;

                return FALSE;
            }
        }

        public function get_arquivo() {
            return $this->arquivo;
        }

    }


