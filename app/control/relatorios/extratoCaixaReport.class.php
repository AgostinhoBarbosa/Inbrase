<?php
    
    use Adianti\Base\TStandardForm;
    use Adianti\Widget\Form\TDate;
    use Adianti\Widget\Wrapper\TDBCombo;
    
    class extratoCaixaReport extends TStandardForm {
        private $linha;
        private $pdf;
        private $saldo_anterior;
        private $total_debito;
        private $total_credito;
        private $saldo_atual;

        function __construct() {
            parent::__construct();

            $this->setDatabase( 'afincco' );
            $this->setActiveRecord( 'Pessoa' );

            $this->form = new BootstrapFormBuilder( 'form_'.__CLASS__ );
            $this->form->setFormTitle( 'Extrato Conta Corrente' );

            $contacorrente_id = new TDBCombo( 'contacorrente_id', 'afincco', 'Contacorrente', 'id', 'nome', 'nome' );
            $data_ini         = new TDate( 'data_ini' );
            $data_fim         = new TDate( 'data_fim' );

            $data_ini->setMask( 'dd/mm/yyyy' );
            $data_fim->setMask( 'dd/mm/yyyy' );
            $data_ini->setDatabaseMask( 'yyyy-mm-dd' );
            $data_fim->setDatabaseMask( 'yyyy-mm-dd' );

            $contacorrente_id->style = ( 'background-color: #FFFEEB;' );
            $data_ini->style         = ( 'text-align:center;' );
            $data_fim->style         = ( 'text-align:center;' );

            $campo_conta   = array( new TLabel( 'Conta Corrente: ' ), $contacorrente_id );
            $campo_dataIni = array( new TLabel( 'Data Inicial: ' ), $data_ini );
            $campo_dataFim = array( new TLabel( 'Data Final: ' ), $data_fim );

            $row = $this->form->addFields($campo_conta, $campo_dataIni, $campo_dataFim);
            $row->layout= ['col-sm-3', 'col-sm-2', 'col-sm-2'];

            $this->form->addAction( _t( 'Generate' ), new TAction( array( $this, 'onGenerate' ) ), 'far:file-pdf red fa-lg' );

            $container        = new TVBox;
            $container->style = 'width: 100%';
            $container->add( $this->form );

            parent::add( $container );
        }

        function onGenerate( $param ) {
            try {
                $data = $this->form->getData();
                $this->total_credito  = 0.00;
                $this->total_debito   = 0.00;
                $this->saldo_anterior = NULL;
                $this->saldo_atual    = 0.00;

                if (empty($data->data_ini)) {
                    $data->data_ini = '2000-01-01';
                }
                if (empty($data->data_fim)) {
                    $data->data_fim =  date('Y-m-d');
                }
                $this->form->setData( $data );

                TTransaction::open( 'afincco' );

                $objects = Caixa::where( 'contacorrente_id', '=', $data->contacorrente_id )
                                ->where( 'data_movimento', '>=', $data->data_ini )
                                ->where( 'data_movimento', '<=', $data->data_fim )
                                ->orderBy( 'data_movimento, dc, valor' )->load();

                if ( $objects ) {
                    $this->pdf = new TSoftgtReport;
                    $this->pdf->set_nomerel( "Extrato Conta Corrente - ".utf8_decode( $objects[ 0 ]->get_contacorrente()->nome ) );
                    $this->pdf->set_titulo( 'Período de '.TDate::date2br($data->data_ini).' até '.TDate::date2br($data->data_fim) );
                    $this->pdf->set_logo( 'logo.jpg' );
                    $this->pdf->set_livro( false );
                    $this->pdf->AddPage();

                    $fill = TRUE;

                    $this->onCabecalho();

                    foreach ( $objects as $object ) {
                        if ( $this->saldo_anterior == NULL ) {
                            $this->saldo_anterior = $object->get_saldo_anterior();
                            $this->saldo_atual    = $this->saldo_anterior;
                            $this->pdf->Ln( 10 );
                            $this->pdf->SetFont( 'Arial', '', 6 );
                            $this->pdf->Cell( 50, 10, TDate::date2br( $object->data_movimento ), 1, 0, 'C', $fill );
                            $this->pdf->Cell( 300, 10, utf8_decode( "SALDO ANTERIOR" ), 1, 0, 'L', $fill );
                            $this->pdf->Cell( 50, 10, " ", 1, 0, 'C', $fill );
                            $this->pdf->Cell( 50, 10, '', 1, 0, 'R', $fill );
                            $this->pdf->Cell( 50, 10, '', 1, 0, 'R', $fill );

                            if ( $this->saldo_atual < 0 ) {
                                $this->pdf->SetTextColor( 255, 0, 0 );
                            } else {
                                $this->pdf->SetTextColor( 25, 25, 112 );
                            }

                            $this->pdf->Cell( 50, 10, number_format( $this->saldo_atual, 2, ',', '.' ), 1, 0, 'R', $fill );
                            $this->pdf->SetTextColor( 0, 0, 0 );

                            $this->onAddLinha();
                        }

                        $this->pdf->Ln( 10 );
                        $this->pdf->SetFont( 'Arial', '', 6 );
                        $this->pdf->setFillColorRGB( '#FFFACD' );
                        $this->pdf->Cell( 50, 10, TDate::date2br( $object->data_movimento ), 1, 0, 'C', $fill );
                        $historico = $object->historico;
                        $historico = str_replace( '<p>', '', $historico );
                        $historico = str_replace( '</p>', '', $historico );
                        $historico = str_replace( '<br>', '', $historico );

                        $this->pdf->Cell( 300, 10, utf8_decode( $historico ), 1, 0, 'L', $fill );
                        $this->pdf->Cell( 50, 10, $object->servico_id, 1, 0, 'C', $fill );
                        if ( $object->dc == "C" ) {
                            $this->pdf->SetTextColor( 0, 0, 128 );
                            $this->saldo_atual += $object->valor;
                            $this->pdf->Cell( 50, 10, number_format( $object->valor, 2, ',', '.' ), 1, 0, 'R', $fill );
                            $this->pdf->Cell( 50, 10, '', 1, 0, 'R', $fill );
                            $this->pdf->SetTextColor( 0, 0, 0 );
                            $this->total_credito += $object->valor;
                        } else {
                            $this->pdf->SetTextColor( 255, 0, 0 );
                            $this->saldo_atual -= $object->valor;
                            $this->pdf->Cell( 50, 10, '', 1, 0, 'R', $fill );
                            $this->pdf->Cell( 50, 10, number_format( $object->valor, 2, ',', '.' ), 1, 0, 'R', $fill );
                            $this->pdf->SetTextColor( 0, 0, 0 );
                            $this->total_debito += $object->valor;
                        }

                        if ( $this->saldo_atual < 0 ) {
                            $this->pdf->SetTextColor( 255, 0, 0 );
                        } else {
                            $this->pdf->SetTextColor( 0, 0, 128 );
                        }

                        $this->pdf->Cell( 50, 10, number_format( $this->saldo_atual, 2, ',', '.' ), 1, 0, 'R', $fill );
                        $this->pdf->SetTextColor( 0, 0, 0 );

                        $this->onAddLinha();

                        $fill = ! $fill;
                    }

                    $this->pdf->ln( 10 );
                    $this->pdf->SetFont( 'Arial', '', 6 );
                    $this->pdf->Cell( 400, 10, utf8_decode( 'TOTAL DO PERÍODO' ), 1, 0, 'C', TRUE );
                    $this->pdf->Cell( 50, 10, number_format( $this->total_credito, 2, ',', '.' ), 1, 0, 'R', $fill );
                    $this->pdf->Cell( 50, 10, number_format( $this->total_debito, 2, ',', '.' ), 1, 0, 'R', $fill );
                    if ( $this->saldo_atual < 0 ) {
                        $this->pdf->SetTextColor( 255, 0, 0 );
                    } else {
                        $this->pdf->SetTextColor( 25, 25, 112 );
                    }

                    $this->pdf->Cell( 50, 10, number_format( $this->saldo_atual, 2, ',', '.' ), 1, 0, 'R', $fill );
                    $this->pdf->SetTextColor( 0, 0, 0 );

                    $arquivo = "tmp/Extrato_".str_pad( rand( 1, 10000 ), 6, "0", STR_PAD_LEFT ).".pdf";

                    if ( ! file_exists( $arquivo ) OR is_writable( $arquivo ) ) {
                        $this->pdf->save( $arquivo );
                    } else {
                        throw new Exception( _t( 'Permission denied' ).': '.$arquivo );
                    }

                    parent::openFile( $arquivo );

                } else {
                    new TMessage( 'error', 'Sem dados para listar!!' );
                }

                $this->form->setData( $param );

            } catch ( Exception $e )
            {
                new TMessage( 'error', '<b>Error</b> '.$e->getMessage() );

                TTransaction::rollback();
            } finally {
                TTransaction::close();
            }
        }

        function onCabecalho() {

            $this->pdf->SetFont( 'Arial', '', 6 );
            $this->pdf->setFillColorRGB( '#CFCFCF' );

            $this->pdf->Cell( 50, 10, utf8_decode( 'Data' ), 1, 0, 'C', TRUE );
            $this->pdf->Cell( 300, 10, utf8_decode( 'Histórico' ), 1, 0, 'C', TRUE );
            $this->pdf->Cell( 50, 10, utf8_decode( 'O.S.' ), 1, 0, 'C', TRUE );
            $this->pdf->Cell( 50, 10, utf8_decode( 'Crédito' ), 1, 0, 'C', TRUE );
            $this->pdf->Cell( 50, 10, utf8_decode( 'Débito' ), 1, 0, 'C', TRUE );
            $this->pdf->Cell( 50, 10, utf8_decode( 'Saldo' ), 1, 0, 'C', TRUE );

            $this->linha = 1;
        }

        function onAddLinha() {
            $this->linha++;
            if ( $this->linha > 67 ) {
                $this->pdf->addPage( 'P', 'A4' );
                $this->onCabecalho();
            }
        }
    }

