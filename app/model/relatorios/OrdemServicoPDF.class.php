<?php

    use Adianti\Database\TTransaction;

    class OrdemServicoPDF
    {
        private $servicos;
        private $arquivo;

        public function __construct( $id ) {
            setlocale( LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese' );
            date_default_timezone_set( 'America/Sao_Paulo' );

            try {
                TTransaction::open( 'afincco' );
                $this->servicos = servicos::find( $id );
                $this->arquivo  = 'app/output/OS_'.$id.'.pdf';
                $ini            = parse_ini_file( 'app/config/application.ini', TRUE );

                if ( $ini[ 'general' ][ 'relos' ] == 0 ) {
                    $this->gerarPDF();
                } else {
                    $this->gerarPDF1();
                }
            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();
            } finally {
                TTransaction::close();
            }
        }

        function gerarPDF() {
            $pdf = new TPDFDesigner();
            $pdf->fromXml( 'app/reports/ordem_servico.pdf.xml' );
            $pdf->replace( '[OS]', str_pad( $this->servicos->id, 3, "0", STR_PAD_LEFT ) );
            $pdf->replace( '[RENAVAM]', $this->servicos->get_veiculo()->renavan );
            $pdf->replace( '[PLACA]', $this->servicos->get_veiculo()->placa );
            $pdf->replace( '[CHASSI]', $this->servicos->get_veiculo()->chassi );
            $pdf->replace( '[PARCEIRO]', utf8_decode( $this->servicos->get_parceiros()->nome ) );
            $pdf->replace( '[MUNIC]', utf8_decode( substr( $this->servicos->cidade_emplacamento, 0, -3 ) ) );
            $pdf->replace( '[UFPLACA]', substr( $this->servicos->cidade_emplacamento, -2 ) );

            if ( $this->servicos->get_pessoa()->tipo_pessoa == 1 ) {
                $pdf->replace( '[TIPODOC]', 'CPF' );
                $pdf->replace( '[DOCUMENTO]', $this->servicos->get_pessoa()->documento );
                $pdf->replace( '[TIPORG]', 'RG' );
            } else {
                $pdf->replace( '[TIPODOC]', 'CNPJ' );
                $pdf->replace( '[TIPORG]', 'Insc.Est.' );
            }

            $pdf->replace( '[DOCUMENTO]', $this->servicos->get_pessoa()->documento );
            $pdf->replace( '[RG_IE]', $this->servicos->get_pessoa()->rg_ie );
            $pdf->replace( '[NOME]', utf8_decode( $this->servicos->get_pessoa()->nome ) );

            $rua = $this->servicos->get_pessoa()->rua;
            if ( !empty( $this->servicos->get_pessoa()->numero ) ) {
                $rua .= ", ".$this->servicos->get_pessoa()->numero;
            }
            if ( !empty( $this->servicos->get_pessoa()->complemento ) ) {
                $rua .= ", ".$this->servicos->get_pessoa()->complemento;
            }
            $pdf->replace( '[RUA]', utf8_decode( $rua ) );
            $pdf->replace( '[NUMERO]', utf8_decode( $this->servicos->get_pessoa()->numero ) );
            $pdf->replace( '[COMPLEMENTO]', utf8_decode( $this->servicos->get_pessoa()->complemento ) );
            $pdf->replace( '[CIDADE]', utf8_decode( $this->servicos->get_pessoa()->cidade."(".$this->servicos->get_pessoa()->uf.")" ) );
            $pdf->replace( '[BAIRRO]', utf8_decode( $this->servicos->get_pessoa()->bairro ) );
            $pdf->replace( '[CEP]', $this->servicos->get_pessoa()->cep );

            $fones       = "";
            $lista_fones = $this->servicos->get_pessoa()->getTelefones();
            foreach ( $lista_fones as $fone ) {
                if ( $fone ) {
                    if ( $fones == "" ) {
                        $fones = $fone->fone_numero;
                    } else {
                        $fones .= " - ".$fone->fone_numero;
                    }
                }
            }

            $pdf->replace( '[FONE]', $fones );
            $servicos = Utilidades::limitarTexto( $this->servicos->servicos_a_executar, 89, FALSE );
            $pdf->replace( '[SERVICOS]', utf8_decode( $servicos ) );
            $pdf->replace( '[SERVICOS1]', utf8_decode( substr( $this->servicos->servicos_a_executar, 90, 89 ) ) );
            $pdf->replace( '[MARCA]', utf8_decode( $this->servicos->get_veiculo()->marca ) );
            $pdf->replace( '[MODELO]', utf8_decode( $this->servicos->get_veiculo()->modelo ) );
            $pdf->replace( '[ANO_MODELO]', $this->servicos->get_veiculo()->ano_fab."/".$this->servicos->get_veiculo()->ano_modelo );
            $pdf->replace( '[COR]', utf8_decode( $this->servicos->get_veiculo()->get_cores()->nome ) );
            $pdf->replace( '[COMBUSTIVEL]', utf8_decode( $this->servicos->get_veiculo()->get_combustivel()->nome ) );
            $pdf->replace( '[TIPOVEIC]', utf8_decode( $this->servicos->get_veiculo()->get_tipoveiculo()->nome ) );
            $pdf->replace( '[OBS]', utf8_decode( $this->servicos->observacao ) );

            $pdf->replace( '[ORCAMENTO]', number_format( $this->servicos->get_orcamento(), 2, ',', '.' ) );
            $pdf->replace( '[ADIANTAMENTO]', number_format( $this->servicos->get_adiantamento(), 2, ',', '.' ) );

            $doctos = '';
            $values = explode( ',', $this->servicos->doctos_recebidos );
            if ( $values ) {
                foreach ( $values as $docto ) {
                    if ( $docto > 0 ) {
                        if ( empty( $doctos ) ) {
                            $doctos = Utilidades::doctos()[ (int)$docto ];
                        } else {
                            $doctos .= ", ".Utilidades::doctos()[ $docto ];
                        }
                    }
                }
            }

            $pdf->replace( '[DOCENTR]', utf8_decode( $doctos ) );

            $data = Utilidades::onDataAtual();
            $pdf->replace( '[DATA_EMI]', $data );
            $pdf->replace( '[USUARIO]', utf8_decode( TSession::getValue( 'login' ) ) );

            $data_compra = $this->servicos->data_compra;

            if ( substr( $this->servicos->cidade_emplacamento, -2 ) == 'PR' ) {
                $data_compra = date( 'Y-m-d', strtotime( "+30 days", strtotime( $data_compra ) ) );
            } else {
                $data_compra = date( 'Y-m-d', strtotime( "+60 days", strtotime( $data_compra ) ) );
            }

            $pdf->replace( '[DATA_CRV]', TDate::date2br( $data_compra ) );

            $pdf->generate();
            $pdf->save( $this->arquivo );
        }

        function gerarPDF1() {
            try {
                $textos = Textos::find( 1 );
                $texto  = $textos->texto;

                $texto = str_replace( '[OS]', str_pad( $this->servicos->id, 3, "0", STR_PAD_LEFT ), $texto );
                $texto = str_replace( '[RENAVAM]', $this->servicos->veiculo->renavan, $texto );
                $texto = str_replace( '[PLACA]', $this->servicos->veiculo->placa, $texto );
                $texto = str_replace( '[PARCEIRO]', $this->servicos->parceiros->nome, $texto );
                $texto = str_replace( '[MUNIC]', $this->servicos->cidade_emplacamento, $texto );
                $texto = str_replace( '[UFPLACA]', substr( $this->servicos->cidade_emplacamento, -2 ), $texto );

                if ( $this->servicos->pessoa->tipo_pessoa == 1 ) {
                    $texto = str_replace( '[TIPODOC]', 'CPF', $texto );
                    $texto = str_replace( '[DOCUMENTO]', $this->servicos->pessoa->documento, $texto );
                    $texto = str_replace( '[TIPORG]', 'RG', $texto );
                } else {
                    $texto = str_replace( '[TIPODOC]', 'CNPJ', $texto );
                    $texto = str_replace( '[TIPORG]', 'Insc.Est.', $texto );
                }

                $texto = str_replace( '[DOCUMENTO]', $this->servicos->pessoa->documento, $texto );
                $texto = str_replace( '[RG_IE]', $this->servicos->pessoa->rg_ie, $texto );
                $texto = str_replace( '[NOME]', $this->servicos->pessoa->nome, $texto );

                $rua = $this->servicos->pessoa->rua;

                if ( !empty( $this->servicos->pessoa->numero ) ) {
                    $rua .= ", ".$this->servicos->pessoa->numero;
                }
                if ( !empty( $this->servicos->pessoa->complemento ) ) {
                    $rua .= ", ".$this->servicos->pessoa->complemento;
                }

                $texto = str_replace( '[RUA]', $rua, $texto );
                $texto = str_replace( '[NUMERO]', $this->servicos->pessoa->numero, $texto );
                $texto = str_replace( '[COMPLEMENTO]', $this->servicos->pessoa->complemento, $texto );
                $texto = str_replace( '[CIDADE]', $this->servicos->pessoa->cidade."(".$this->servicos->pessoa->uf.")", $texto );
                $texto = str_replace( '[BAIRRO]', $this->servicos->pessoa->bairro, $texto );
                $texto = str_replace( '[CEP]', $this->servicos->pessoa->cep, $texto );

                $fone_res    = "";
                $celular     = "";
                $fone_com    = "";
                $lista_fones = $this->servicos->pessoa->relefones;

                foreach ( $lista_fones as $fone ) {
                    if ( $fone ) {
                        switch ( $fone->fone_tipo ) {
                            case 1:
                                $fone_res = $fone->fone_numero;
                                break;
                            case 2:
                                $celular = $fone->fone_numero;
                                break;
                            case 3:
                                $fone_com = $fone->fone_numero;
                                break;
                        }
                    }
                }

                $texto = str_replace( '[FONE_RES]', $fone_res, $texto );
                $texto = str_replace( '[CELULAR]', $celular, $texto );
                $texto = str_replace( '[FONE_COM]', $fone_com, $texto );

                $texto = str_replace( '[SERVICOS]', $this->servicos->servicos_a_executar, $texto );

                $texto = str_replace( '[MARCA]', $this->servicos->veiculo->marca, $texto );
                $texto = str_replace( '[MODELO]', $this->servicos->veiculo->modelo, $texto );
                $texto = str_replace( '[CHASSI]', $this->servicos->veiculo->chassi, $texto );
                $texto = str_replace( '[ANO_MODELO]', $this->servicos->veiculo->ano_fab."/".$this->servicos->veiculo->ano_modelo, $texto );
                $texto = str_replace( '[COR]', $this->servicos->veiculo->get_cores()->nome, $texto );

                $texto = str_replace( '[CARROCERIA]', $this->servicos->get_veiculo()->carroceria->nome, $texto );
                $texto = str_replace( '[COMBUSTIVEL]', $this->servicos->get_veiculo()->combustivel->nome, $texto );
                $texto = str_replace( '[TIPOVEIC]', $this->servicos->get_veiculo()->tipoveiculo->nome, $texto );

                $texto = str_replace( '[OBS]', $this->servicos->observacao, $texto );
                $texto = str_replace( '[ORCAMENTO]', number_format( $this->servicos->orcamento, 2, ',', '.' ), $texto );
                $texto = str_replace( '[ADIANTAMENTO]', number_format( $this->servicos->adiantamento, 2, ',', '.' ), $texto );

                $doctos = '';
                $values = explode( ',', $this->servicos->doctos_recebidos );
                if ( $values ) {
                    foreach ( $values as $docto ) {
                        if ( $docto > 0 ) {
                            if ( empty( $doctos ) ) {
                                $doctos = Utilidades::doctos()[ (int)$docto ];
                            } else {
                                $doctos .= ", ".Utilidades::doctos()[ $docto ];
                            }
                        }
                    }
                }

                $texto = str_replace( '[DOCENTR]', $doctos, $texto );

                $data  = Utilidades::onDataAtual();
                $texto = str_replace( '[DATA_EMI]', utf8_encode( $data ), $texto );

                $texto = str_replace( '[USUARIO]', TSession::getValue( 'login' ), $texto );

                $data_compra = $this->servicos->data_compra;
                if ( substr( $this->servicos->cidade_emplacamento, -2 ) == 'PR' ) {
                    $data_compra = date( 'Y-m-d', strtotime( "+30 days", strtotime( $data_compra ) ) );
                } else {
                    $data_compra = date( 'Y-m-d', strtotime( "+60 days", strtotime( $data_compra ) ) );
                }

                $texto = str_replace( '[DATA_COMPRA]', TDate::date2br( $data_compra ), $texto );

                $texto = str_replace( '[DATA_CRV]', TDate::date2br( $data_compra ), $texto );
                include_once( "app/lib/mpdf/mpdf.php" );
                $pdf = new mPDF();
                $pdf->SetTitle( "Ordem de Serviço" );
                $pdf->SetAuthor( "SoftGT Informatica" );
                $pdf->pagenumPrefix = 'Pagina ';
                $pdf->pagenumSuffix = '';
                $pdf->nbpgPrefix    = ' de ';
                define( "PAGINA", "Impresso em ".date( 'd/m/Y - H:i:s  ' )." {PAGENO} "." de  {nb}", TRUE );
                $pdf->SetFooter( PAGINA );
                $pdf->WriteHTML( $texto );
                $pdf->Output( $this->arquivo, "F" );
                $pdf->charset_in = 'windows-1252';

            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );

            }
        }

        public function get_arquivo() {
            return $this->arquivo;
        }
    }
