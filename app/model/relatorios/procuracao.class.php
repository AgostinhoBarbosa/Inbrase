<?php

    use Adianti\Database\TTransaction;
    use Adianti\Widget\Dialog\TMessage;

    class procuracao
    {
        private $processoa;
        private $arquivo;

        public function __construct( Processo $processo ) {
            $this->processoa = $processo;
            $this->gerarTermo();
        }

        function gerarTermo() {
            require_once( "vendor/autoload.php" );
            try {
                $verifica = ProcessoArq::where( 'id_processo', '=', $this->processoa->id )->where( 'tipoarq_id', '=', 3 )->load();

                if ( $verifica ) {
                    $destino       = 'app/arquivos/'.$this->processoa->id."/".$verifica[ 0 ]->nome;
                    $this->arquivo = $destino;
                    new TMessage( 'info', 'Procuração ja existe, caso queira gerar uma nova, deve antes excluir a existente...' );
                } else {
                    $empresa = new Empresa( 1 );
                    $textos  = Textos::find( 8 );
                    $texto   = $textos->texto;

                    $imagem = "app/images/".$empresa->logo;

                    $texto = str_replace( '[LOGO]', $imagem, $texto );
                    $texto = str_replace( '[DATA_EMISSAO]', Utilidades::onDataAtual(), $texto );
                    $texto = str_replace( '[MODELO]', $this->processoa->marca_modelo, $texto );
                    $texto = str_replace( '[PLACA]', $this->processoa->placa, $texto );
                    $texto = str_replace( '[CHASSI]', $this->processoa->chassi, $texto );

                    $texto = str_replace( '[OUTORGADO]', $this->processoa->liberadores->nome, $texto );
                    $texto = str_replace( '[CPF_OUTORGADO]', $this->processoa->liberadores->documento, $texto );
                    $texto = str_replace( '[END_OUTORGADO]', $this->processoa->liberadores->get_endereco_completo(), $texto );

                    $arq_pdf   = 'Procuracao_'.$this->processoa->id."_".rand().'.pdf';
                    $senha_arq = Utilidades::geraSenha();

                    TTransaction::open( 'afincco' );

                    $obj_arq              = new ProcessoArq();
                    $obj_arq->id_processo = $this->processoa->id;
                    $obj_arq->nome        = $arq_pdf;
                    $obj_arq->data_arq    = date( 'Y-m-d H:i:s' );
                    $obj_arq->usuario     = TSession::getValue( 'login' );
                    $obj_arq->tipoarq_id  = 3;
                    $obj_arq->assinado    = 0;
                    $obj_arq->token       = $senha_arq;
                    $obj_arq->hash        = '';

                    $obj_arq->store();

                    $ini  = parse_ini_file( 'app/config/application.ini', TRUE );
                    $site = $ini[ 'general' ][ 'site' ];

                    $arq = 'app/arquivos/'.$this->processoa->id."/";
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
                    $arq_qrcode        = "procuracao_".$obj_arq->id_arq."_qrcode.png";
                    $end_qrcode        = "app/arquivos/".$this->processoa->id."/".$arq_qrcode;

                    $params[ 'savename' ] = $end_qrcode;
                    $qrcode->generate( $params );

                    $texto = str_replace( '[QRCODE]', $end_qrcode, $texto );
                    $texto = str_replace( '[CODDOC]', $obj_arq->id_arq, $texto );
                    $texto = str_replace( '[SENHADOC]', $senha_arq, $texto );


                    if ( !file_exists( $arq ) ) {
                        mkdir( $arq );
                        chmod( $arq, 0777 );
                    }

                    $arq .= $arq_pdf;

                    $pdf = new mPDF( '', 'A4', 0, '', 15, 10, 5, 1, 1, 1, 'P' );

                    $pdf->SetTitle( "Procuração - ".$this->processoa->id );
                    $pdf->SetAuthor( "SoftGT Informatica" );
                    $pdf->SetFont( 'Arial', '', 6 );
                    $pdf->margin_footer = 3;
                    $pdf->WriteHTML( $texto );
                    $this->arquivo = $arq;
                    $pdf->Output( $arq, "F" );

                    $obj_arq->hash = md5_file( $arq );
                    $obj_arq->store();

                }
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

