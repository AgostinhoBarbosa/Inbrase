<?php
    
    use Adianti\Database\TTransaction;
    use Adianti\Widget\Form\TDate;
    use Adianti\Wrapper\BootstrapFormBuilder;
    
    class resultadoReport extends TPage
    {
        protected $form;
        
        private $linha;
        private $pdf;
        private $total_cobrado;
        private $total_pago;
        private $tamanho = [ 50, 50, 50, 160, 70, 70, 50, 35 ];
        
        function __construct()
        {
            parent::__construct();
            
            $this->form = new BootstrapFormBuilder( 'form_'.__CLASS__ );
            $this->form->setFormTitle( 'Apuração de Resultado' );
            
            $data_ini = new TDate( 'data_ini' );
            $data_fim = new TDate( 'data_fim' );
            
            $data_ini->setMask( 'dd/mm/yyyy' );
            $data_ini->setDatabaseMask( 'yyyy-mm-dd' );
            $data_fim->setMask( 'dd/mm/yyyy' );
            $data_fim->setDatabaseMask( 'yyyy-mm-dd' );
            
            $data_ini->style .= ( '; text-align:center;' );
            $data_fim->style .= ( '; text-align:center;' );
            
            $campo_dataIni                                                   = [ new TLabel( 'Data Inicial: ' ), $data_ini ];
            $campo_dataFim                                                   = [ new TLabel( 'Data Final: ' ), $data_fim ];
            $this->form->addFields( $campo_dataIni, $campo_dataFim )->layout = [ 'col-sm-2', 'col-sm-2' ];
            
            $this->form->addAction( _t( 'Generate' ), new TAction( [ $this, 'onGenerate' ] ), 'far:file-pdf red fa-lg' );
            
            $container        = new TVBox;
            $container->style = 'width: 100%';
            $container->add( $this->form );
            
            parent::add( $container );
        }
        
        function onGenerate( $param )
        {
            try {
                $data = $this->form->getData();
                $this->form->setData( $data );
                $this->total_cobrado = 0.00;
                $this->total_pago    = 0.00;
                
                TTransaction::open( 'afincco' );
                
                $objects = Processo::where( 'data_cadastro', '>=', TDate::date2us( $param[ 'data_ini' ] ) )
                                   ->where( 'data_cadastro', '<=', TDate::date2us( $param[ 'data_fim' ] ) )
                                   ->orderBy( 'id' )->load();
                
                if ( $objects ) {
                    $this->pdf = new TSoftgtReport;
                    $this->pdf->set_nomerel( "Relatório de Fechamento" );
                    $this->pdf->set_titulo( 'Período de '.$param[ 'data_ini' ].' até '.$param[ 'data_fim' ] );
                    $this->pdf->set_logo( 'logo.jpg' );
                    $this->pdf->addPage();
                    
                    $fill = TRUE;
                    
                    $this->onCabecalho();
                    $resumo_seguradora = [];
                    
                    // data rows
                    foreach ( $objects as $object ) {
                        $this->pdf->Ln();
                        $this->pdf->SetFont( 'Arial', '', 6 );
                        $this->pdf->setFillColorRGB( '#FFFACD' );
                        $this->pdf->Cell( $this->tamanho[ 0 ], 10, str_pad( $object->id, 5, "0", STR_PAD_LEFT ), 1, 0, 'C', $fill );
                        $this->pdf->Cell( $this->tamanho[ 1 ], 10, TDate::date2br( $object->data_cadastro ), 1, 0, 'C', $fill );
                        $this->pdf->Cell( $this->tamanho[ 2 ], 10, $object->placa, 1, 0, 'C', $fill );
                        $cobrado = 0.00;
                        $pago    = 0.00;
                        if ( !empty( $object->get_financeiro()[ 0 ]->valor ) ) {
                            $seguradora =  Utilidades::limitarTexto( $object->get_financeiro()[ 0 ]->pessoa->nome, 42);
                            $this->pdf->Cell( $this->tamanho[ 3 ], 10, $seguradora, 1, 0, 'L', $fill );
                            foreach ( $object->get_financeiro() as $obj ) {
                                if ( $obj->pagar_receber == 'P' ) {
                                    $pago += $obj->valor;
                                }
                                if ( $obj->pagar_receber == 'R' ) {
                                    $cobrado += $obj->valor;
                                }
                            }
                        } else {
                            $seguradora = Utilidades::limitarTexto( $object->seguradoras->nome, 42);
                            $this->pdf->Cell($this->tamanho[ 3 ], 10, $seguradora, 1, 0, 'L', $fill );
                        }
                        $this->pdf->Cell( $this->tamanho[ 4 ], 10, number_format( $cobrado, 2, ',', '.' ), 1, 0, 'R', $fill );
                        $this->pdf->Cell( $this->tamanho[ 5 ], 10, number_format( $pago, 2, ',', '.' ), 1, 0, 'R', $fill );
                        $resultado = $cobrado - $pago;
                        $this->pdf->Cell( $this->tamanho[ 6 ], 10, number_format( $resultado, 2, ',', '.' ), 1, 0, 'R', $fill );
                        if (!isset($resumo_seguradora[$seguradora])){
                            $resumo_seguradora[$seguradora]['pago'] = 0.00;
                            $resumo_seguradora[$seguradora]['cobrado'] = 0.00;
                            
                        }
                        $resumo_seguradora[$seguradora]['pago'] += $pago;
                        $resumo_seguradora[$seguradora]['cobrado'] += $cobrado;
                        
                        $this->total_cobrado += $cobrado;
                        $this->total_pago    += $pago;
                        
                        if ( $resultado == 0 ) {
                            $percentual = 0;
                        } else {
                            if ( $cobrado == 0 ) {
                                $percentual = 100;
                            } else {
                                $percentual = ( $resultado / $cobrado ) * 100;
                            }
                        }
                        
                        $percentual = floatval( $percentual );
                        
                        if ( $percentual <= 10 ) {
                            $this->pdf->SetTextColor( 255, 0, 0 );
                        }
                        
                        if ( $percentual > 10 ) {
                            $this->pdf->SetTextColor( 0, 100, 0 );
                        }
                        
                        if ( $percentual > 20 ) {
                            $this->pdf->SetTextColor( 25, 25, 112 );
                        }
                        
                        $this->pdf->Cell( $this->tamanho[ 7 ], 10, number_format( $percentual, 2, ',', '.' ), 1, 0, 'R', $fill );
                        $this->pdf->SetTextColor( 0, 0, 0 );
                        
                        $this->onAddLinha();
                        
                        $fill = !$fill;
                    }
                    
                    // footer row
                    $this->pdf->ln( 20 );
                    $this->pdf->SetFont( 'Arial', '', 6 );
                    $this->pdf->Cell( 310, 10, utf8_decode( 'TOTAL DO PERÍODO' ), 1, 0, 'C', TRUE );
                    $this->pdf->Cell( $this->tamanho[ 4 ], 10, number_format( $this->total_cobrado, 2, ',', '.' ), 1, 0, 'R', $fill );
                    $this->pdf->Cell( $this->tamanho[ 5 ], 10, number_format( $this->total_pago, 2, ',', '.' ), 1, 0, 'R', $fill );
                    $resultado = $this->total_cobrado - $this->total_pago;
                    $this->pdf->Cell( $this->tamanho[ 6 ], 10, number_format( $resultado, 2, ',', '.' ), 1, 0, 'R', $fill );
                    if ( $resultado == 0 ) {
                        $percentual = 0;
                    } else {
                        if ( $this->total_cobrado == 0 ) {
                            $percentual = 100;
                        } else {
                            $percentual = ( $resultado / $this->total_cobrado ) * 100;
                        }
                    }
                    
                    $percentual = floatval( $percentual );
                    
                    if ( $percentual <= 10 ) {
                        $this->pdf->SetTextColor( 255, 0, 0 );
                    }
                    
                    if ( $percentual > 10 ) {
                        $this->pdf->SetTextColor( 0, 100, 0 );
                    }
                    
                    if ( $percentual > 20 ) {
                        $this->pdf->SetTextColor( 25, 25, 112 );
                    }
                    
                    $this->pdf->Cell( $this->tamanho[ 7 ], 10, number_format( $percentual, 2, ',', '.' ), 1, 0, 'R', $fill );
                    $this->pdf->SetTextColor( 0, 0, 0 );
                    
                    if (count($resumo_seguradora) > 0) {
                        ksort($resumo_seguradora);
                        $this->pdf->addPage();
    
                        $this->pdf->SetFont( 'Arial', '', 8 );
    
                        $this->pdf->Cell(270, 12, 'Seguradora', 1, 0,'C', TRUE);
                        $this->pdf->Cell(70, 12, 'Cobrado', 1, 0,'C', TRUE);
                        $this->pdf->Cell(70, 12, 'Pago', 1, 0,'C', TRUE);
                        $this->pdf->Cell(70, 12, 'Saldo', 1, 0,'C', TRUE);
                        $this->pdf->Cell(52, 12, '% Saldo', 1, 1,'C', TRUE);
                        
                        $total_cobrado = 0.00;
                        $total_pago    = 0.00;
                        
                        foreach ($resumo_seguradora as $index => $seguradora){
                            $this->pdf->Cell(270, 12, $index, 1, 0,'L');
                            $this->pdf->Cell(70, 12, number_format($seguradora['cobrado'], 2, ',','.'), 1, 0,'R');
                            $this->pdf->Cell(70, 12, number_format($seguradora['pago'], 2, ',','.'), 1, 0,'R');
                            $resultado = $seguradora['cobrado'] - $seguradora['pago'];
                            $this->pdf->Cell(70, 12, number_format($resultado, 2, ',','.'), 1, 0,'R');
                            if ( $seguradora['pago'] == 0 ) {
                                $percentual = 100;
                            } else {
                                if ( $seguradora['cobrado'] == 0 ) {
                                    $percentual = 0;
                                }else{
                                    $percentual = ( $resultado / $seguradora['cobrado'] ) * 100;
                                }
                            }
                            $this->pdf->Cell(52, 12, number_format($percentual, 4, ',','.'), 1, 1,'R');
                            $total_cobrado +=  $seguradora['cobrado'];
                            $total_pago    +=  $seguradora['pago'];
                        }
                        $resultado = $total_cobrado - $total_pago;
    
                        $this->pdf->SetFont( 'Arial', 'B', 8 );
                        $this->pdf->Cell( 270, 12, utf8_decode( 'TOTAL DO PERÍODO' ), 1, 0, 'C', TRUE );
                        $this->pdf->Cell( 70, 12, number_format( $total_cobrado, 2, ',', '.' ), 1, 0, 'R', $fill );
                        $this->pdf->Cell( 70, 12, number_format( $total_pago, 2, ',', '.' ), 1, 0, 'R', $fill );
                        $this->pdf->Cell( 70, 12, number_format( $resultado, 2, ',', '.' ), 1, 0, 'R', $fill );
                        if ( $resultado == 0 ) {
                            $percentual = 0;
                        } else {
                            if ( $total_cobrado == 0 ) {
                                $percentual = -100;
                            } else {
                                $percentual = ( $resultado / $total_cobrado ) * 100;
                            }
                        }
                        $this->pdf->Cell(52, 12, number_format($percentual, 4, ',','.'), 1, 1,'R');
    
                    }
                    
                    
                    $arquivo = "tmp/Resultado_".str_pad( rand( 1, 10000 ), 6, "0", STR_PAD_LEFT ).".pdf";
                    
                    if ( !file_exists( $arquivo ) or is_writable( $arquivo ) ) {
                        $this->pdf->save( $arquivo );
                    } else {
                        throw new Exception( _t( 'Permission denied' ).': '.$arquivo );
                    }
                    
                    parent::openFile( $arquivo );
                    
                } else {
                    new TMessage( 'error', 'Sem dados para listar!!' );
                }
                
                $this->form->setData( $param );
                
                TTransaction::close();
            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();
            }
        }
        
        function onCabecalho()
        {
            $this->pdf->SetFont( 'Arial', '', 10 );
            $this->pdf->setFillColorRGB( '#CFCFCF' );
            
            $this->pdf->Cell( $this->tamanho[ 0 ], 10, utf8_decode( 'Processo' ), 1, 0, 'C', TRUE );
            $this->pdf->Cell( $this->tamanho[ 1 ], 10, utf8_decode( 'Data' ), 1, 0, 'C', TRUE );
            $this->pdf->Cell( $this->tamanho[ 2 ], 10, utf8_decode( 'Placa' ), 1, 0, 'C', TRUE );
            $this->pdf->Cell( $this->tamanho[ 3 ], 10, utf8_decode( 'Seguradora' ), 1, 0, 'C', TRUE );
            $this->pdf->Cell( $this->tamanho[ 4 ], 10, utf8_decode( 'Valor Cobrado' ), 1, 0, 'C', TRUE );
            $this->pdf->Cell( $this->tamanho[ 5 ], 10, utf8_decode( 'Valor Pago' ), 1, 0, 'C', TRUE );
            $this->pdf->Cell( $this->tamanho[ 6 ], 10, utf8_decode( 'Resultado' ), 1, 0, 'C', TRUE );
            $this->pdf->Cell( $this->tamanho[ 7 ], 10, utf8_decode( '%' ), 1, 0, 'C', TRUE );
            
            $this->linha = 1;
        }
        
        function onAddLinha()
        {
            $this->linha++;
            if ( $this->linha > 67 ) {
                $this->pdf->addPage();
                $this->onCabecalho();
            }
        }
        
        /**
         * method show()
         * Shows the page
         */
        public function show()
        {
            // check if the datagrid is already loaded
            if ( !$this->loaded and ( !isset( $_GET[ 'method' ] ) or !( in_array( $_GET[ 'method' ], [ 'onReload', 'onSearch' ] ) ) ) ) {
                if ( func_num_args() > 0 ) {
                    $this->onReload( func_get_arg( 0 ) );
                } else {
                    $this->onReload();
                }
            }
            parent::show();
        }
        
        /**
         * Load the datagrid with data
         */
        public function onReload( $param = NULL )
        {
            $this->loaded = TRUE;
        }
    }

