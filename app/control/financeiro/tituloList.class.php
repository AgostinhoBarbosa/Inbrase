<?php
    
    use Adianti\Base\TStandardList;
    use Adianti\Database\TCriteria;
    use Adianti\Database\TFilter;
    use Adianti\Database\TTransaction;
    use Adianti\Registry\TSession;
    use Adianti\Widget\Container\TTableRow;
    use Adianti\Widget\Dialog\TMessage;
    use Adianti\Widget\Form\TCombo;
    use Adianti\Widget\Form\TDate;
    use Adianti\Widget\Form\TEntry;
    use Adianti\Widget\Wrapper\TDBCombo;
    use Adianti\Widget\Wrapper\TDBMultiSearch;
    
    class tituloList extends TStandardList
    {
        
        public function __construct()
        {
            parent::__construct();
            
            parent::setDatabase( 'afincco' );
            parent::setActiveRecord( 'Titulo' );
            parent::setDefaultOrder( 'id', 'desc' );
            
            $this->form = new BootstrapFormBuilder( 'form_' . __CLASS__ );
            $this->form->setFormTitle( 'Títulos Financeiros' );
            
            $numero            = new TEntry( 'numero' );
            $pessoa_id         = new TDBCombo( 'pessoa_id', 'afincco', 'Pessoa', 'id', '{documento} - {nome}', 'nome' );
            $tipolancamento_id = new TDBMultiSearch( 'tipolancamento_id', 'afincco', 'TipoLancamento', 'id', 'nome', 'nome' );
            $processo_id       = new TEntry( 'processo_id' );
            $tipo_data         = new TCombo( 'tipo_data' );
            $data_inicial      = new TDate( 'data_inicial' );
            $data_final        = new TDate( 'data_final' );
            $pagar_receber     = new TCombo( 'pagar_receber' );
            $pagos             = new TCombo( 'pagos' );
            
            $pessoa_id->enableSearch();
            $tipolancamento_id->setMinLength( 1 );
            $pagos->addItems( Utilidades::sim_nao() );
            
            $tipo        = [];
            $tipo[ '1' ] = 'Data Entrada';
            $tipo[ '2' ] = 'Data Vencimento';
            $tipo_data->addItems( $tipo );
            
            $pagar_receber->addItems( Utilidades::pagar_receber() );
            
            $numero->style            = ( 'text-align:center;background-color: #F7F2E0;' );
            $pessoa_id->style         = ( 'background-color: #F7F2E0;' );
            $tipolancamento_id->style = ( 'text-align: left;background-color: #F7F2E0;' );
            $data_inicial->style      = ( 'text-align:center !important;' );
            $data_final->style        = ( 'text-align:center !important;' );
            $pagar_receber->style     = ( 'text-align:center !important;' );
            
            $data_inicial->setMask( 'dd/mm/yyyy' );
            $data_final->setMask( 'dd/mm/yyyy' );
            
            $campo_numero         = [ new TLabel( 'Numero' ), $numero ];
            $campo_processo       = [ new TLabel( 'Processo' ), $processo_id ];
            $campo_cliente        = [ new TLabel( 'Cliente/Fornecedor' ), $pessoa_id ];
            $campo_tipolancamento = [ new TLabel( 'Tipo Lançamento' ), $tipolancamento_id ];
            $campo_tipodata       = [ new TLabel( 'Tipo Data' ), $tipo_data ];
            $campo_dataInicial    = [ new TLabel( 'Data Inicial' ), $data_inicial ];
            $campo_dataFinal      = [ new TLabel( 'Data Final' ), $data_final ];
            $campo_pagarereceber  = [ new TLabel( 'Pagar/Receber' ), $pagar_receber ];
            $campo_pagos          = [ new TLabel( 'Somente Pagos' ), $pagos ];
            
            $row         = $this->form->addFields( $campo_numero, $campo_processo, $campo_cliente, $campo_tipolancamento );
            $row->layout = [ 'col-md-2', 'col-md-2', 'col-md-4', 'col-md-4' ];
            $row         = $this->form->addFields( $campo_pagarereceber, $campo_tipodata, $campo_dataInicial, $campo_dataFinal, $campo_pagos );
            $row->layout = [ 'col-md-2', 'col-md-2', 'col-md-2', 'col-md-2', 'col-md-2' ];
            
            $this->form->setData( TSession::getValue( 'Titulo_filter_data' ) );
            
            $this->form->addAction( _t( 'Find' ), new TAction( [ $this, 'onSearch' ] ), 'fa:search blue' );
            $this->form->addActionlink( _t( 'New' ), new TAction( [ 'tituloForm', 'onClear' ], [ 'register_state' => 'false' ] ), 'fa:eraser red' );
            
            $this->form->addActionLink( 'Importar Recibos', new TAction( [ $this, 'onImportarRecibos' ] ), 'bs:plus red' );
            $this->form->addActionLink( 'Importar Planilha NFSe', new TAction( [ $this, 'onImportarNFSe' ] ), 'far:file-excel blue' );
            $this->form->addAction( 'Exportar Pesquisa', new TAction( [ $this, 'onExportar' ] ), 'far:file-excel red' );
            
            $this->datagrid = new BootstrapDatagridWrapper( new TQuickGrid );
            
            $column_id                = new TDataGridColumn( 'id', 'Código', 'center', '5%' );
            $column_pessoa_id         = new TDataGridColumn( 'pessoa->nome', 'Nome', 'left', '20%' );
            $column_tipolancamento_id = new TDataGridColumn( 'tipolancamento->nome', 'Tipo de Lançamento', 'left', '20%' );
            $column_data_vencimento   = new TDataGridColumn( 'data_vencimento', 'Data Vencimento', 'center', '10%' );
            $column_valor             = new TDataGridColumn( 'valor', 'Valor', 'right', '10%' );
            $column_saldo             = new TDataGridColumn( 'saldo', 'Saldo', 'right', '10%' );
            $column_numero            = new TDataGridColumn( 'numero', 'Numero', 'center', '10%' );
            $column_pagar_receber     = new TDataGridColumn( 'pagar_receber', 'Pagar/Receber', 'center', '10%' );
            $column_dc                = new TDataGridColumn( 'dc', 'D/C', 'center', '5%' );
            $column_processo          = new TDataGridColumn( 'processo_id', 'Processo', 'center', '10%' );
    
            $column_id->setTransformer([$this, 'formatRow'] );
    
            $column_valor->setTransformer( function ( $value, $object, $row ) {
                if ( is_object( $object ) ) {
                    if ( $object->dc == "D" ) {
                        return "<p style='color:#ff0000;margin: 0 0 0 0;'><b>" . number_format( $value, 2, ',', '.' ) . "</b></p>";
                    } else {
                        return "<p style='color:#00008B;margin: 0 0 0 0;'><b>" . number_format( $value, 2, ',', '.' ) . "</b></p>";
                    }
                }
                return "<p style='color:#00008B;margin: 0 0 0 0;'><b>" . number_format( $value, 2, ',', '.' ) . "</b></p>";
            } );
            
            $column_saldo->setTransformer( function ( $value, $object, $row ) {
                if ( $value < 0 ) {
                    return "<p style='color:#ff0000;margin: 0 0 0 0;'><b>" . number_format( $value, 2, ',', '.' ) . "</b></p>";
                } else {
                    return "<p style='color:#00008B;margin: 0 0 0 0;'><b>" . number_format( $value, 2, ',', '.' ) . "</b></p>";
                }
            } );
            $column_pagar_receber->setTransformer( function ( $value, $object, $row ) {
                if ( $value == "P" ) {
                    return "<p style='color:#ff0000;margin: 0 0 0 0;'>Contas a Pagar</p>";
                } else {
                    return "<p style='color:#00008B;margin: 0 0 0 0;'>Contas a Receber</b></p>";
                }
                
            } );
            $column_dc->setTransformer( function ( $value, $object, $row ) {
                if ( $value == "D" ) {
                    return "<p style='color:#ff0000;margin: 0 0 0 0;'>Débito</p>";
                } else {
                    return "<p style='color:#00008B;margin: 0 0 0 0;'>Crédito</b></p>";
                }
                
            } );
            
            $column_valor->setTotalFunction( function ( $value ) {
                return array_sum( (array)$value );
            } );
            
            $column_saldo->setTotalFunction( function ( $value ) {
                return array_sum( (array)$value );
            } );
            
            $this->datagrid->addColumn( $column_id );
            $this->datagrid->addColumn( $column_pessoa_id );
            $this->datagrid->addColumn( $column_tipolancamento_id );
            $this->datagrid->addColumn( $column_numero );
            $this->datagrid->addColumn( $column_data_vencimento );
            $this->datagrid->addColumn( $column_processo );
            $this->datagrid->addColumn( $column_pagar_receber );
            $this->datagrid->addColumn( $column_dc );
            $this->datagrid->addColumn( $column_valor );
            $this->datagrid->addColumn( $column_saldo );
            
            $action_select = new TDataGridAction( [ $this, 'onSelect' ], [ 'id' => '{id}', 'register_state' => 'false' ] );
            $action_edit   = new TDataGridAction( [ 'tituloForm', 'onEdit' ], [ 'key' => '{id}', 'register_state' => 'false' ] );
            $action_del    = new TDataGridAction( [ $this, 'onDelete' ], [ 'key' => '{id}', 'register_state' => 'false' ] );
            $action_view   = new TDataGridAction( [ $this, 'onShowDetail' ], [ 'key' => '{id}', 'register_state' => 'false' ] );
            $action_mov    = new TDataGridAction( [ 'movimentoTituloForm', 'onClear' ], [ 'key'            => '{id}',
                                                                                          'origem'         => 'titulo',
                                                                                          'register_state' => 'false' ] );
            $action_libera = new TDataGridAction( [ $this, 'onLibera' ], [ 'key' => '{id}', 'register_state' => 'false' ] );
            
            $action_libera->setDisplayCondition( function ( $object ) {
                return $object->tipolancamento_id == 94;
            } );
            $action_mov->setDisplayCondition( function ( $object ) {
                return $object->saldo > 0.00;
            } );
    
            $action_select->setButtonClass('btn btn-default');
            $action_del->setDisplayCondition( [ $this, 'mostraDeletar' ] );
            $action_view->setDisplayCondition( [ $this, 'mostraDetalhes' ] );
            
            $this->datagrid->addAction( $action_select, 'Selecionar', 'far:square fa-fw black' );
            $this->datagrid->addAction( $action_edit, _t( 'Edit' ), 'far:edit blue fa-lg' );
            $this->datagrid->addAction( $action_del, _t( 'Delete' ), 'far:trash-alt red fa-lg' );
            $this->datagrid->addAction( $action_mov, 'Inserir Movimento', 'far:money-bill-alt brown fa-lg' );
            $this->datagrid->addAction( $action_view, 'Detalhes', 'far:plus-square green fa-lg' );
            $this->datagrid->addAction( $action_libera, 'Liberar Pagamento', 'far:handshake green fa-lg' );
            
            
            $this->datagrid->createModel();
            
            $this->pageNavigation = new TPageNavigation();
            $this->pageNavigation->setAction( new TAction( [ $this, 'onReload' ] ) );
            $this->pageNavigation->setWidth( $this->datagrid->getWidth() );
            
            $dropdown = new TDropDown( _t( 'Export' ), 'fa:list' );
            $dropdown->setPullSide( 'right' );
            $dropdown->setButtonClass( 'btn btn-default waves-effect dropdown-toggle' );
            $dropdown->addAction( _t( 'Save as CSV' ), new TAction( [ $this,
                                                                      'onExportCSV' ], [ 'register_state' => 'false',
                                                                                         'static'         => '1' ] ), 'fa:table fa-fw blue' );
            $dropdown->addAction( _t( 'Save as PDF' ), new TAction( [ $this,
                                                                      'onExportPDF' ], [ 'register_state' => 'false',
                                                                                         'static'         => '1' ] ), 'far:file-pdf fa-fw red' );
            $dropdown->addAction( _t( 'Save as XML' ), new TAction( [ $this,
                                                                      'onExportXML' ], [ 'register_state' => 'false',
                                                                                         'static'         => '1' ] ), 'fa:code fa-fw green' );
            
            $panel                                = new TPanelGroup();
            $panel->add( $this->datagrid )->style = 'overflow-x:auto';
            $panel->addFooter( $this->pageNavigation );
            //$panel->addHeaderWidget( $dropdown );
            $panel->addHeaderActionLink( 'Excluir Selecionados', new TAction([$this, 'deleteSelected']), 'far:trash-alt red' );
            
            $container        = new TVBox();
            $container->style = 'width: 100%';
            $container->add( $this->form );
            $container->add( $panel );
            parent::add( $container );
        }
    
        public function formatRow($value, $object, $row)
        {
            $selected_objects = TSession::getValue(__CLASS__.'_selected_objects');
        
            if ($selected_objects)
            {
                if (in_array( (int) $value, array_keys( $selected_objects ) ) )
                {
                    $row->style = "background: #abdef9";
                
                    $button = $row->find('i', ['class'=>'far fa-square fa-fw black'])[0];
                
                    if ($button)
                    {
                        $button->class = 'far fa-check-square fa-fw black';
                    }
                }
            }
        
            return $value;
        }
    
        public function onSelect($param)
        {
            $selected_objects = TSession::getValue(__CLASS__.'_selected_objects');
        
            $id = $param['id'];
            if (isset($selected_objects[$id]))
            {
                unset($selected_objects[$id]);
            }
            else
            {
                $selected_objects[$id] = $id;
            }
            TSession::setValue(__CLASS__.'_selected_objects', $selected_objects);
        
            $this->onReload( func_get_arg(0) );
        }
    
        /**
         * Delete selected records
         */
        public function deleteSelected()
        {
            $selected_objects = TSession::getValue(__CLASS__.'_selected_objects');
        
            if ($selected_objects)
            {
                TTransaction::open('afincco');
                foreach ($selected_objects as $id)
                {
                    $object = Titulo::find( $id );
                    if ($object)
                    {
                        $object->delete();
                    }
                }
                TTransaction::close();
            
                new TMessage('info', 'Registros Excluidos');
            }
            TSession::delValue(__CLASS__.'_selected_objects');
            $this->onReload();
        }
        
        public function onExportar( $param )
        {
            try {
                TTransaction::open( $this->database );
                
                $repository = new TRepository( $this->activeRecord );
                
                $criteria = new TCriteria;
                
                if ( $this->order ) {
                    $criteria->setProperty( 'order', $this->order );
                    $criteria->setProperty( 'direction', $this->direction );
                }
                
                $criteria->setProperties( $param );
                
                $tem_filtro = FALSE;
                
                if ( TSession::getValue( 'tituloList_filter_numero' ) ) {
                    $criteria->add( TSession::getValue( 'tituloList_filter_numero' ) );
                    $tem_filtro = TRUE;
                }
                
                if ( TSession::getValue( 'tituloList_filter_pessoa_id' ) ) {
                    $criteria->add( TSession::getValue( 'tituloList_filter_pessoa_id' ) );
                    $tem_filtro = TRUE;
                }
                
                if ( TSession::getValue( 'tituloList_filter_tipolancamento_id' ) ) {
                    $criteria->add( TSession::getValue( 'tituloList_filter_tipolancamento_id' ) );
                    $tem_filtro = TRUE;
                }
                
                if ( TSession::getValue( 'tituloList_filter_data' ) ) {
                    $criteria->add( TSession::getValue( 'tituloList_filter_data' ) );
                    $tem_filtro = TRUE;
                }
                
                if ( TSession::getValue( 'tituloList_filter_processo_id' ) ) {
                    $criteria->add( TSession::getValue( 'tituloList_filter_processo_id' ) );
                    $tem_filtro = TRUE;
                }
                
                if ( TSession::getValue( 'tituloList_filter_pagar_receber' ) ) {
                    $criteria->add( TSession::getValue( 'tituloList_filter_pagar_receber' ) );
                    $tem_filtro = TRUE;
                }
                
                if ( TSession::getValue( 'tituloList_filter_pagos' ) ) {
                    $criteria->add( TSession::getValue( 'tituloList_filter_pagos' ) );
                    $tem_filtro = TRUE;
                }
                
                if ( !$tem_filtro ) {
                    $filter = new TFilter( 'id', '=', '0' );
                    $criteria->add( $filter );
                }
                
                $objects = $repository->load( $criteria, FALSE );
                
                if ( $objects ) {
                    require_once( 'app/lib/PHPExcel/PHPExcel/IOFactory.php' );
                    set_include_path( get_include_path() . PATH_SEPARATOR . '../../../Classes/' );
                    
                    $Arquivo = "tmp/titulos_" . rand() . ".xlsx";
                    
                    $objPHPExcel = new PHPExcel();
                    $objPHPExcel->setActiveSheetIndex( 0 );
                    $objPHPExcel->getActiveSheet()->setTitle( 'Títulos' );
                    
                    $objPHPExcel->setActiveSheetIndex( 0 );
                    
                    $sharedStyle1 = new PHPExcel_Style();
                    $sharedStyle2 = new PHPExcel_Style();
                    $sharedStyle3 = new PHPExcel_Style();
                    $sharedStyle4 = new PHPExcel_Style();
                    $sharedStyle5 = new PHPExcel_Style();
                    
                    $sharedStyle1->applyFromArray( [ 'fill' => [ 'type'      => PHPExcel_Style_Fill::FILL_SOLID,
                                                                 'color'     => [ 'argb' => 'FFCCFFCC' ],
                                                                 'borders'   => [
                                                                     'bottom' => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ],
                                                                     'top'    => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ],
                                                                     'right'  => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ],
                                                                     'left'   => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ],
                                                                 ],
                                                                 'alignment' => [ 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER ] ] ] );
                    $sharedStyle2->applyFromArray( [ 'fill' => [ 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => [ 'argb' => 'FFFFFF00' ], ], 'borders' => [ 'bottom' => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ], 'top' => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ], 'right' => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ], 'left' => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ], ], 'alignment' => [ 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, ], ] );
                    $sharedStyle3->applyFromArray( [ 'borders' => [ 'bottom' => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ], 'top' => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ], 'right' => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ], 'left' => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ], ], 'alignment' => [ 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, ], ] );
                    $sharedStyle4->applyFromArray( [
                                                       'borders'   => [ 'bottom' => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ],
                                                                        'top'    => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ],
                                                                        'right'  => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ],
                                                                        'left'   => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ] ],
                                                       'alignment' => [ 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT ] ] );
                    $sharedStyle5->applyFromArray( [ 'borders' => [ 'bottom' => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ], 'top' => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ], 'right' => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ], 'left' => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ], ], 'alignment' => [ 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT, ], ] );
                    
                    $objPHPExcel->getActiveSheet()->setSharedStyle( $sharedStyle1, "A1:J1" );
                    
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'A1', 'LISTAGEM DE TÍTULOS FINANCEIRO' );
                    $objPHPExcel->getActiveSheet()->mergeCells( 'A1:J1' );
                    
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'A2', 'CÓDIGO' );
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'B2', 'CLIENTE/FORNECEDOR' );
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'C2', 'TIPO LANÇAMENTO' );
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'D2', 'NUMERO' );
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'E2', 'VENCIMENTO' );
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'F2', 'PROCESSO' );
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'G2', 'PAGAR/RECEBER' );
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'H2', 'DC' );
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'I2', 'VALOR' );
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'J2', 'SALDO' );
                    $objPHPExcel->getActiveSheet()->setSharedStyle( $sharedStyle2, "A2:J2" );
                    
                    $linha = 2;
                    $valor = 0;
                    $saldo = 0;
                    date_default_timezone_set( 'America/Sao_Paulo' );
                    
                    foreach ( $objects as $object ) {
                        set_time_limit( 0 );
                        
                        $linha++;
                        
                        $date = new DateTime( $object->data_vencimento );
                        
                        $objPHPExcel->getActiveSheet()->SetCellValue( 'A' . $linha, $object->id, PHPExcel_Cell_DataType::TYPE_STRING );
                        $objPHPExcel->getActiveSheet()->SetCellValue( 'B' . $linha, utf8_encode( $object->pessoa->nome ), PHPExcel_Cell_DataType::TYPE_STRING );
                        $objPHPExcel->getActiveSheet()->SetCellValue( 'C' . $linha, utf8_encode( $object->tipolancamento->nome ), PHPExcel_Cell_DataType::TYPE_STRING );
                        $objPHPExcel->getActiveSheet()->SetCellValue( 'D' . $linha, utf8_encode( $object->numero ), PHPExcel_Cell_DataType::TYPE_STRING );
                        $objPHPExcel->getActiveSheet()->SetCellValue( 'E' . $linha, $date->format( 'd/m/Y' ) );
                        $objPHPExcel->getActiveSheet()->SetCellValue( 'F' . $linha, utf8_encode( $object->processo_id ), PHPExcel_Cell_DataType::TYPE_STRING );
                        $objPHPExcel->getActiveSheet()->SetCellValue( 'G' . $linha, utf8_encode( $object->pagar_receber ), PHPExcel_Cell_DataType::TYPE_STRING );
                        $objPHPExcel->getActiveSheet()->SetCellValue( 'H' . $linha, utf8_encode( $object->dc ), PHPExcel_Cell_DataType::TYPE_STRING );
                        $objPHPExcel->getActiveSheet()->SetCellValue( 'I' . $linha, utf8_encode( $object->valor ), PHPExcel_Cell_DataType::TYPE_NUMERIC );
                        $objPHPExcel->getActiveSheet()->SetCellValue( 'J' . $linha, utf8_encode( $object->saldo ), PHPExcel_Cell_DataType::TYPE_NUMERIC );
                        $objPHPExcel->getActiveSheet()->getStyle( 'I' . $linha )->getNumberFormat()->setFormatCode( "#,##0.00" );
                        $objPHPExcel->getActiveSheet()->getStyle( 'J' . $linha )->getNumberFormat()->setFormatCode( "#,##0.00" );
                        
                        $valor += $object->valor;
                        $saldo += $object->saldo;
                    }
                    
                    $linha++;
                    
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'A' . $linha, '' );
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'B' . $linha, '' );
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'C' . $linha, '' );
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'D' . $linha, '' );
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'E' . $linha, '' );
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'F' . $linha, '' );
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'G' . $linha, '' );
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'H' . $linha, '' );
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'I' . $linha, $valor );
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'J' . $linha, $saldo );
                    $objPHPExcel->getActiveSheet()->setSharedStyle( $sharedStyle2, "A" . $linha . ":J" . $linha );
                    $objPHPExcel->getActiveSheet()->getStyle( 'I' . $linha )->getNumberFormat()->setFormatCode( "#,##0.00" );
                    $objPHPExcel->getActiveSheet()->getStyle( 'J' . $linha )->getNumberFormat()->setFormatCode( "#,##0.00" );
                    $linha--;
                    
                    
                    $objPHPExcel->getActiveSheet()->setSharedStyle( $sharedStyle3, "A3:A" . $linha );
                    $objPHPExcel->getActiveSheet()->setSharedStyle( $sharedStyle4, "B3:C" . $linha );
                    $objPHPExcel->getActiveSheet()->setSharedStyle( $sharedStyle3, "D3:H" . $linha );
                    
                    $objPHPExcel->getActiveSheet()->getStyle( "E3:E" . $linha )->getNumberFormat()->setFormatCode( 'dd/mm/yyyy hh:mm:ss' );
                    
                    foreach ( $objPHPExcel->getWorksheetIterator() as $worksheet ) {
                        $objPHPExcel->setActiveSheetIndex( $objPHPExcel->getIndex( $worksheet ) );
                        
                        $sheet        = $objPHPExcel->getActiveSheet();
                        $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
                        $cellIterator->setIterateOnlyExistingCells( TRUE );
                        foreach ( $cellIterator as $cell ) {
                            $sheet->getColumnDimension( $cell->getColumn() )->setAutoSize( TRUE );
                        }
                    }
                    
                    $objWriter = new PHPExcel_Writer_Excel2007( $objPHPExcel );
                    $objWriter->save( $Arquivo, __FILE__ );
                    parent::openFile( $Arquivo );
                }
                
                TTransaction::close();
            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();
            }
            
        }
        
        public function onSearch( $param = NULL )
        {
            $data = $this->form->getData();
            $this->form->setData( $data );
            
            TSession::setValue( 'Titulo_filter_data', $data );
            
            TSession::delValue( 'tituloList_filter_numero' );
            TSession::delValue( 'tituloList_filter_processo_id' );
            TSession::delValue( 'tituloList_filter_pessoa_id' );
            TSession::delValue( 'tituloList_filter_tipolancamento_id' );
            TSession::delValue( 'tituloList_filter_data' );
            TSession::delValue( 'tituloList_filter_pagar_receber' );
            TSession::delValue( 'tituloList_filter_pagos' );
            
            if ( isset( $data->numero ) and ( $data->numero ) ) {
                $filter = new TFilter( 'numero', '=', "{$data->numero}" );
                TSession::setValue( 'tituloList_filter_numero', $filter );
            }
            
            if ( isset( $data->pessoa_id ) and ( $data->pessoa_id ) ) {
                $filter = new TFilter( 'pessoa_id', '=', "{$data->pessoa_id}" );
                TSession::setValue( 'tituloList_filter_pessoa_id', $filter );
            }
            
            if ( isset( $data->tipolancamento_id ) and ( $data->tipolancamento_id ) ) {
                $filter = new TFilter( 'tipolancamento_id', 'IN', $data->tipolancamento_id );
                TSession::setValue( 'tituloList_filter_tipolancamento_id', $filter );
            }
            
            if ( isset( $data->processo_id ) and ( $data->processo_id ) ) {
                $filter = new TFilter( 'processo_id', '=', "{$data->processo_id}" );
                TSession::setValue( 'tituloList_filter_processo_id', $filter );
            }
            
            if ( isset( $data->pagos ) ) {
                if ( $data->pagos == '0' ) {
                    $filter = new TFilter( 'saldo', '<>', "0" );
                    TSession::setValue( 'tituloList_filter_pagos', $filter );
                }
                if ( $data->pagos == '1' ) {
                    $filter = new TFilter( 'saldo', '=', "0" );
                    TSession::setValue( 'tituloList_filter_pagos', $filter );
                }
            }
            
            if ( isset( $data->tipo_data ) and ( $data->tipo_data ) ) {
                $data_inicial = date( 'Y-m-d' );
                $data_final   = date( 'Y-m-d' );
                if ( isset( $data->data_inicial ) and ( $data->data_inicial ) ) {
                    $data_inicial = TDate::date2us( $data->data_inicial );
                }
                if ( isset( $data->data_final ) and ( $data->data_final ) ) {
                    $data_final = TDate::date2us( $data->data_final );
                }
                
                if ( $data->tipo_data == 1 ) {
                    $filter = new TFilter( 'data_entrada', 'between', $data_inicial, $data_final );
                    TSession::setValue( 'tituloList_filter_data', $filter );
                }
                if ( $data->tipo_data == 2 ) {
                    $filter = new TFilter( 'data_vencimento', 'between', $data_inicial, $data_final );
                    TSession::setValue( 'tituloList_filter_data', $filter );
                }
            }
            
            if ( isset( $data->pagar_receber ) and ( $data->pagar_receber ) ) {
                $filter = new TFilter( 'pagar_receber', '=', "{$data->pagar_receber}" );
                TSession::setValue( 'tituloList_filter_pagar_receber', $filter );
            }
            
            $param                 = [];
            $param[ 'offset' ]     = 0;
            $param[ 'first_page' ] = 1;
            $this->onReload( $param );
        }
        
        public function onImportarNFSe( $param )
        {
            
            TSession::setValue( 'Titulo_param', $param );
            
            $data = $this->form->getData();
            TSession::setValue( 'Titulo_data', $data );
            
            $form_arq        = new TQuickForm( 'input_form' );
            $form_arq->style = 'padding:20px';
            
            $Arquivo = new TMultiFile( 'Arquivo' );
            
            $form_arq->addQuickField( 'Arquivo: ', $Arquivo );
            $form_arq->addQuickAction( 'Importar', new TAction( [ $this, 'onImportarExcel_NFSe' ] ), 'ico_save.png' );
            
            new TInputDialog( 'Importar Planilha Excel', $form_arq );
            
            $this->onReload( $param );
        }
        
        public function onReload( $param = NULL )
        {
            try {
                TTransaction::open( 'afincco' );
                
                $repository = new TRepository( 'Titulo' );
                $limit      = 10;
                $criteria   = new TCriteria;
                
                if ( empty( $param[ 'order' ] ) ) {
                    $param[ 'order' ]     = 'id';
                    $param[ 'direction' ] = 'desc';
                }
                
                $criteria->setProperties( $param );
                $criteria->setProperty( 'limit', $limit );
                
                $tem_filtro = FALSE;
                
                if ( TSession::getValue( 'tituloList_filter_numero' ) ) {
                    $criteria->add( TSession::getValue( 'tituloList_filter_numero' ) );
                    $tem_filtro = TRUE;
                }
                
                if ( TSession::getValue( 'tituloList_filter_pessoa_id' ) ) {
                    $criteria->add( TSession::getValue( 'tituloList_filter_pessoa_id' ) );
                    $tem_filtro = TRUE;
                }
                
                if ( TSession::getValue( 'tituloList_filter_tipolancamento_id' ) ) {
                    $criteria->add( TSession::getValue( 'tituloList_filter_tipolancamento_id' ) );
                    $tem_filtro = TRUE;
                }
                
                if ( TSession::getValue( 'tituloList_filter_data' ) ) {
                    $criteria->add( TSession::getValue( 'tituloList_filter_data' ) );
                    $tem_filtro = TRUE;
                }
                
                if ( TSession::getValue( 'tituloList_filter_processo_id' ) ) {
                    $criteria->add( TSession::getValue( 'tituloList_filter_processo_id' ) );
                    $tem_filtro = TRUE;
                }
                
                if ( TSession::getValue( 'tituloList_filter_pagar_receber' ) ) {
                    $criteria->add( TSession::getValue( 'tituloList_filter_pagar_receber' ) );
                    $tem_filtro = TRUE;
                }
                
                if ( TSession::getValue( 'tituloList_filter_pagos' ) ) {
                    $criteria->add( TSession::getValue( 'tituloList_filter_pagos' ) );
                    $tem_filtro = TRUE;
                }
                
                if ( !$tem_filtro ) {
                    $filter = new TFilter( 'id', '=', '0' );
                    $criteria->add( $filter );
                }
                
                $objects = $repository->load( $criteria, FALSE );
                
                if ( is_callable( $this->transformCallback ) ) {
                    call_user_func( $this->transformCallback, $objects, $param );
                }
                
                $this->datagrid->clear();
                if ( $objects ) {
                    foreach ( $objects as $object ) {
                        $object->data_emissao    = TDate::date2br( $object->data_emissao );
                        $object->data_entrada    = TDate::date2br( $object->data_entrada );
                        $object->data_vencimento = TDate::date2br( $object->data_vencimento );
                        $object->pessoa->nome    = $object->pessoa->nome;
                        
                        $this->datagrid->addItem( $object );
                    }
                }
                
                $criteria->resetProperties();
                $count = $repository->count( $criteria );
                
                $this->pageNavigation->setCount( $count );
                $this->pageNavigation->setProperties( $param );
                $this->pageNavigation->setLimit( $limit );
                
                $this->loaded = TRUE;
            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();
            } finally {
                TTransaction::close();
            }
        }
        
        public function onImportarExcel_NFSe( $param )
        {
            require_once( 'app/lib/PHPExcel/PHPExcel/IOFactory.php' );
            set_include_path( get_include_path() . PATH_SEPARATOR . '../../../Classes/' );
            
            foreach ( $param as $chave => $obj ) {
                
                if ( $chave == "Arquivo" ) {
                    TTransaction::open( 'afincco' );
                    
                    try {
                        foreach ( $obj as $arq => $valor ) {
                            if ( !empty( $valor ) ) {
                                $source_file   = 'tmp/' . $valor;
                                $inputFileType = PHPExcel_IOFactory::identify( $source_file );
                                $objReader     = PHPExcel_IOFactory::createReader( $inputFileType );
                                $objPHPExcel   = $objReader->load( $source_file );
                                
                                $objPHPExcel->setActiveSheetIndex( 0 );
                                
                                PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
                                $highestRow = $objPHPExcel->getActiveSheet()->getHighestRow();
                                $objPHPExcel->getActiveSheet()->getStyle( 'E1:E' . $highestRow )->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2 );
                                
                                $sheetData = $objPHPExcel->getActiveSheet()->toArray( NULL, TRUE, TRUE, TRUE );
                                $x         = 0;
                                foreach ( $sheetData as $linha ) {
                                    if ( $x > 0 ) {
                                        $numero = $linha[ 'A' ];
                                        if ( !empty( $linha[ 'G' ] ) ) {
                                            $x++;
                                            continue;
                                        }
                                        if ( empty( $linha[ 'W' ] ) ) {
                                            unlink( $source_file );
                                            throw new Exception( "Coluna W com numero do processo vazia." );
                                        }
                                        
                                        $titulo = titulo::where( 'numero', '=', $numero )->where( 'tipodoc', '=', 'N' )->first();
                                        
                                        if ( $titulo ) {
                                            $titulo->valor       = Utilidades::Valor_Excel( $linha[ 'H' ] );
                                            $titulo->saldo       = $titulo->valor;
                                            $titulo->processo_id = $linha[ 'W' ];
                                            $titulo->store();
                                        } else {
                                            $documento = $linha[ 'M' ];
                                            
                                            if ( strlen( $documento ) < 14 ) {
                                                $documento = str_pad( $documento, 14, '0', STR_PAD_LEFT );
                                            }
                                            
                                            $valida    = new validacpfcnpj( $documento );
                                            $documento = $valida->formata();
                                            unset( $valida );
                                            
                                            if ( !$documento ) {
                                                $documento = $linha[ 'M' ];
                                            }
                                            
                                            $pessoa = pessoa::where( 'documento', '=', $documento )->first();
                                            
                                            if ( !$pessoa ) {
                                                if ( strlen( $documento ) > 14 ) {
                                                    $retorno = Utilidades::onCNPJ( $documento );
                                                    $objeto  = json_decode( $retorno );
                                                    if ( isset( $objeto->logradouro ) ) {
                                                        $pessoa                  = new pessoa();
                                                        $pessoa->nome            = $objeto->nome;
                                                        $pessoa->documento       = $documento;
                                                        $pessoa->tipo_pessoa     = 2;
                                                        $pessoa->rua             = $objeto->logradouro;
                                                        $pessoa->numero          = $objeto->numero;
                                                        $pessoa->bairro          = $objeto->bairro;
                                                        $pessoa->cidade          = $objeto->municipio;
                                                        $pessoa->uf              = $objeto->uf;
                                                        $pessoa->data_nascimento = TDate::date2us( $objeto->abertura );
                                                        $pessoa->cep             = $objeto->cep;
                                                        $pessoa->observacao      = "";
                                                        for ( $i = 0, $iMax = count( $objeto->qsa ); $i < $iMax; $i++ ) {
                                                            $pessoa->observacao .= $objeto->qsa[ $i ]->qual . " - ";
                                                            $pessoa->observacao .= $objeto->qsa[ $i ]->nome . "<br>";
                                                        }
                                                        for ( $i = 0, $iMax = count( $objeto->atividade_principal ); $i < $iMax; $i++ ) {
                                                            $pessoa->observacao .= "Atividade Principal " . $objeto->atividade_principal[ $i ]->code . " - ";
                                                            $pessoa->observacao .= $objeto->atividade_principal[ $i ]->text . "<br>";
                                                        }
                                                        for ( $i = 0, $iMax = count( $objeto->atividades_secundarias ); $i < $iMax; $i++ ) {
                                                            $pessoa->observacao .= "Atividade Secundaria " . $objeto->atividades_secundarias[ $i ]->code . " - ";
                                                            $pessoa->observacao .= $objeto->atividades_secundarias[ $i ]->text . "<br>";
                                                        }
                                                        
                                                        $pessoa->observacao .= "Natureza Juridica - " . $objeto->natureza_juridica . "<br>";
                                                        $pessoa->observacao .= "Capital Social - R$-" . number_format( $objeto->capital_social, 2, ',', '.' ) . "<br>";
                                                        
                                                        $pessoa->store();
                                                    }
                                                }
                                            }
                                            if ( !$pessoa ) {
                                                continue;
                                            }
                                            
                                            $titulo                    = new titulo;
                                            $titulo->pessoa_id         = $pessoa->id;
                                            $titulo->tipolancamento_id = 79;
                                            $titulo->tipodoc           = 'N';
                                            $titulo->data_entrada      = $linha[ 'E' ];
                                            $data_entrada              = new DateTime( $titulo->data_entrada );
                                            $data_entrada->add( new DateInterval( 'P30D' ) );
                                            $titulo->data_vencimento = $data_entrada->format( 'Y-m-d' );
                                            $titulo->data_emissao    = $titulo->data_entrada;
                                            $titulo->valor           = Utilidades::Valor_Excel( $linha[ 'H' ] );
                                            $titulo->saldo           = $titulo->valor;
                                            $titulo->numero          = $numero;
                                            $titulo->processo_id     = $linha[ 'W' ];
                                            $titulo->parcela         = 1;
                                            $titulo->pagar_receber   = 'R';
                                            $titulo->dc              = 'C';
                                            $titulo->observacao      = '';
                                            $titulo->store();
                                        }
                                    }
                                    $x++;
                                }
                                unlink( $source_file );
                            }
                        }
                    } catch ( Exception $e ) {
                        new TMessage( 'error', $e->getMessage() );
                        TTransaction::rollback();
                    } finally {
                        TTransaction::close();
                    }
                }
            }
            
            $data = TSession::getValue( 'Titulo_data' );
            $this->form->setData( $data );
            TSession::delValue( 'Titulo_data' );
            $param = TSession::getValue( 'Titulo_param' );
            TSession::delValue( 'param' );
            $this->onReload( $param );
            
        }
        
        public function onImportarRecibos( $param )
        {
            try {
                set_time_limit( 0 );
                TTransaction::open( 'afincco' );
                $conn = TTransaction::get();
                
                $repository = new TRepository( 'Comprovante' );
                $criteria   = new TCriteria;
                
                if ( empty( $param[ 'order' ] ) ) {
                    $param[ 'order' ]     = 'IdComprovante';
                    $param[ 'direction' ] = 'asc';
                }
                
                $objects = $repository->load();
                
                if ( $objects ) {
                    foreach ( $objects as $object ) {
                        if ( $object->onVerificaStatus() ) {
                            $observacao = $object->despesa->observacao;
                            $seguradora = $object->seguradora->nome;
                            $posicao    = stripos( $seguradora, 'CNPJ' );
                            if ( $posicao > 0 ) {
                                $posicao += 5;
                                $cnpj    = substr( $seguradora, $posicao );
                                $titulo  = Titulo::where( 'numero', '=', $object->IdComprovante )->where( 'tipolancamento_id', 'IN', [ 36, 94 ] )->first();
                                if ( !$titulo ) {
                                    $pessoa = Pessoa::where( 'documento', '=', $cnpj )->first();
                                    if ( $pessoa ) {
                                        $titulo                    = new Titulo();
                                        $titulo->numero            = $object->IdComprovante;
                                        $titulo->pessoa_id         = $pessoa->id;
                                        $titulo->tipolancamento_id = 36;
                                        $titulo->data_entrada      = $object->Data_processo;
                                        $titulo->data_emissao      = $object->Data_processo;
                                        $data_entrada              = new DateTime( $object->Data_processo );
                                        $data_entrada->add( new DateInterval( 'P30D' ) );
                                        $titulo->data_vencimento = $data_entrada->format( 'Y-m-d' );
                                        $titulo->valor           = $object->ValorTotal;
                                        $titulo->saldo           = $object->ValorTotal;
                                        $titulo->parcela         = 1;
                                        $titulo->pagar_receber   = 'R';
                                        $titulo->processo_id     = $object->id_processo;
                                        $titulo->dc              = 'C';
                                        $titulo->observacao      = $observacao;
                                        $titulo->store();
                                        $conn->commit();
                                        $conn->beginTransaction();
                                    } else {
                                        throw  new Exception( 'Cliente não localizado CNPJ/CPF: ' . $cnpj );
                                    }
                                } else {
                                    $titulo->processo_id = $object->id_processo;
                                    $titulo->store();
                                    $conn->commit();
                                    $conn->beginTransaction();
                                }
                            }
                        }
                    }
                }
                
                $repository = new TRepository( 'Recibos' );
                $criteria   = new TCriteria;
                
                if ( empty( $param[ 'order' ] ) ) {
                    $param[ 'order' ]     = 'id';
                    $param[ 'direction' ] = 'asc';
                }
                
                $objects = $repository->load();
                
                if ( $objects ) {
                    foreach ( $objects as $object ) {
                        if ( $object->onVerificaStatus() ) {
                            $observacao = $object->get_observacao();
                            $titulo     = titulo::where( 'numero', '=', $object->id )->where( 'tipolancamento_id', 'IN', [ 91, 94 ] )->first();
                            if ( !$titulo ) {
                                $titulo                    = new Titulo();
                                $titulo->numero            = $object->id;
                                $titulo->pessoa_id         = $object->pessoa_id;
                                $titulo->tipolancamento_id = 94;
                                $titulo->data_entrada      = $object->data_emissao;
                                $titulo->data_emissao      = $object->data_emissao;
                                $data_entrada              = new DateTime( $object->data_emissao );
                                $data_entrada->add( new DateInterval( 'P30D' ) );
                                $titulo->data_vencimento = $data_entrada->format( 'Y-m-d' );
                                $titulo->valor           = $object->valor_recibo;
                                $titulo->saldo           = $object->valor_recibo;
                                $titulo->parcela         = 1;
                                $titulo->pagar_receber   = 'P';
                                $titulo->processo_id     = $object->processo_id;
                                $titulo->dc              = 'C';
                                $titulo->observacao      = $observacao;
                                $titulo->store();
                                $conn->commit();
                                $conn->beginTransaction();
                            }
                        }
                    }
                }
                $this->loaded = TRUE;
            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();
            } finally {
                TTransaction::close();
            }
        }
        
        public function mostraDeletar( $object )
        {
            if ( $object->onVerMovimentoTitulo() ) {
                return FALSE;
            }
            return TRUE;
        }
        
        public function mostraDetalhes( $object )
        {
            if ( $object->onVerMovimentoTitulo() ) {
                return TRUE;
            }
            return FALSE;
        }
        
        public function onShowDetail( $param )
        {
            $ultimo_detalhe = TSession::getValue( 'Titulo_ultimo_detalhe' );
            
            if ( $ultimo_detalhe ) {
                if ( $ultimo_detalhe == $param[ 'key' ] ) {
                    TSession::setValue( 'Titulo_ultimo_detalhe', NULL );
                    return;
                }
            }
            
            TSession::setValue( 'Titulo_ultimo_detalhe', $param[ 'key' ] );
            TTransaction::open( 'afincco' );
            try {
                $titulo = Titulo::find( $param[ 'key' ] );
                
                $movimento = $titulo->movimentotitulo;
                
                if ( $movimento ) {
                    $pos = $this->datagrid->getRowIndex( 'id', $param[ 'key' ] );
                    
                    $current_row        = $this->datagrid->getRow( $pos );
                    $current_row->style = "background-color: #CDB38B; color:white; text-shadow:none";
                    
                    $row              = new TTableRow;
                    $row->style       = "";
                    $cab_ini          = $row->addCell( '' );
                    $cab_ini->colspan = 5;
                    $cab_ini->style   = 'background-color: #F0E68C;';
                    $cab_1            = $row->addCell( 'Código' );
                    $cab_1->style     = 'padding:10px;background-color: #ADD8E6;border: 1px solid;text-align:center;';
                    $cab_2            = $row->addCell( 'Data Movimento' );
                    $cab_2->style     = 'padding:10px;background-color: #ADD8E6;border-top: 1px solid;border-bottom:1px solid;border-right: 1px solid;text-align:center;';
                    $cab_3            = $row->addCell( 'Local Movimento' );
                    $cab_3->style     = 'padding:10px;background-color: #ADD8E6;border-top: 1px solid;border-bottom:1px solid;border-right: 1px solid;text-align:center;';
                    $cab_4            = $row->addCell( 'Tipo de Lançamento' );
                    $cab_4->colspan   = 3;
                    $cab_4->style     = 'padding:10px;background-color: #ADD8E6;border-top: 1px solid;border-bottom:1px solid;border-right: 1px solid;text-align:center;';
                    $cab_5            = $row->addCell( 'Débito' );
                    $cab_5->style     = 'padding:10px;background-color: #ADD8E6;border-top: 1px solid;border-bottom:1px solid;text-align:center;';
                    $cab_6            = $row->addCell( 'Crédito' );
                    $cab_6->style     = 'padding:10px;background-color: #ADD8E6;border: 1px solid;text-align:center;';
                    $cab_fim          = $row->addCell( '' );
                    $cab_fim->style   = 'background-color: #F0E68C;';
                    
                    $this->datagrid->insert( $pos + 1, $row );
                    
                    $x       = 1;
                    $tot_deb = 0.00;
                    $tot_cre = 0.00;
                    
                    foreach ( $movimento as $object ) {
                        $x++;
                        $linha            = new TTableRow();
                        $linha->style     = "";
                        $cab_ini          = $linha->addCell( '' );
                        $cab_ini->colspan = 4;
                        $cab_ini->style   = 'background-color: #F0E68C;';
                        $cab_0            = $linha->addCell( '<a href="index.php?class=tituloList&amp;method=onDeleteMovimento&amp;key=' . $object->id . '&amp;register_state=false&amp;" generator="adianti"><i class="fa fa-trash red fa-lg" title="" data-original-title="Excluir Movimento"></i></a>' );
                        $cab_0->colspan   = 1;
                        $cab_0->style     = 'background-color: #F0E68C;';
                        $cab_1            = $linha->addCell( $object->id );
                        $cab_1->style     = 'background-color: #FFFAFA;border-left: 1px solid;border-right:1px solid;border-bottom:1px solid;text-align:center;';
                        $cab_2            = $linha->addCell( TDate::date2br( $object->data_movimento ) );
                        $cab_2->style     = 'background-color: #FFFAFA;border-bottom:1px solid;border-right: 1px solid;text-align:center;';
                        $cab_2            = $linha->addCell( $object->get_caixa()->get_contacorrente()->nome );
                        $cab_2->style     = 'background-color: #FFFAFA;border-bottom:1px solid;border-right: 1px solid;text-align:center;';
                        $cab_3            = $linha->addCell( $object->get_tipolancamento()->nome );
                        $cab_3->colspan   = 3;
                        $cab_3->style     = 'padding-left:2px;background-color: #FFFAFA;border-bottom:1px solid;border-right: 1px solid;';
                        if ( $object->dc == "D" ) {
                            $cab_4        = $linha->addCell( number_format( $object->valor, 2, ',', '.' ) );
                            $cab_4->style = 'padding-right:5px;background-color: #FFFAFA;border-bottom:1px solid;text-align:right;';
                            $cab_5        = $linha->addCell( '' );
                            $cab_5->style = 'background-color: #FFFAFA;border-left: 1px solid;border-right: 1px solid;border-bottom: 1px solid;';
                            $tot_deb      += $object->valor;
                        } else {
                            $cab_4        = $linha->addCell( '' );
                            $cab_4->style = 'background-color: #FFFAFA;border-right: 1px solid;border-bottom: 1px solid;';
                            $cab_5        = $linha->addCell( number_format( $object->valor, 2, ',', '.' ) );
                            $cab_5->style = 'padding-right:5px;background-color: #FFFAFA;border-right:1px solid;border-bottom:1px solid;text-align:right;';
                            $tot_cre      += $object->valor;
                        }
                        $cab_fim        = $linha->addCell( '' );
                        $cab_fim->style = 'background-color: #F0E68C;';
                        
                        $this->datagrid->insert( $pos + $x, $linha );
                    }
                    $x++;
                    $sumario          = new TTableRow;
                    $cab_ini          = $sumario->addCell( '' );
                    $cab_ini->colspan = 5;
                    $cab_ini->style   = 'background-color: #F0E68C;';
                    $sum_tit          = $sumario->addCell( "Total dos Lançamentos" );
                    $sum_tit->colspan = 6;
                    $sum_tit->style   = 'background-color: #ADD8E6;border-left:1px solid;border-right: 1px solid;border-bottom: 1px solid;text-align:center;';
                    $sum_deb          = $sumario->addCell( number_format( $tot_deb, 2, ',', '.' ) );
                    $sum_deb->style   = 'background-color: #ADD8E6;border-left:1px solid;border-right: 1px solid;border-bottom: 1px solid;text-align:right;padding-right:5px;';
                    $sum_cre          = $sumario->addCell( number_format( $tot_cre, 2, ',', '.' ) );
                    $sum_cre->style   = 'background-color: #ADD8E6;border-left:1px solid;border-right: 1px solid;border-bottom: 1px solid;text-align:right;padding-right:5px;';
                    $this->datagrid->insert( $pos + $x, $sumario );
                    $cab_fim        = $sumario->addCell( '' );
                    $cab_fim->style = 'background-color: #F0E68C;';
                    
                    $x++;
                    $rodape = new TTableRow;
                    
                    $rodape->addCell( "<hr style='border-color:red;'/>" )->colspan = 14;
                    
                    $this->datagrid->insert( $pos + $x, $rodape );
                } else {
                    $pos = $this->datagrid->getRowIndex( 'id', $param[ 'key' ] );
                    
                    $current_row        = $this->datagrid->getRow( $pos );
                    $current_row->style = "background-color: #CDB38B; color:white; text-shadow:none";
                    $row                = new TTableRow;
                    
                    $cell          = $row->addCell( 'Sem Movimento a Listar' );
                    $cell->colspan = 14;
                    $cell->style   = "height:5px;background-color: #F5DEB3;border: 1px solid;border-color:#ff0000;text-align:center;color:#ff0000;";
                    
                    $this->datagrid->insert( $pos + 1, $row );
                    
                }
                TTransaction::close();
            } catch ( Exception $e ) {
                new TMessage( 'error', '<b>Erro</b> ' . $e->getMessage() );
                TTransaction::close();
            }
            
        }
        
        public function onDeleteMovimento( $param )
        {
            try {
                $key = $param[ 'key' ];
                if ( !empty( $key ) ) {
                    TTransaction::open( 'afincco' );
                    MovimentoTitulo::find( $key )->delete();
                    TTransaction::close();
                    TSession::delValue( 'Titulo_ultimo_detalhe' );
                    $this->onReload( NULL );
                }
            } catch ( Exception $e ) {
                TTransaction::rollback();
                new TMessage( 'error', $e->getMessage() );
            }
        }
        
        public function onLibera( $param )
        {
            try {
                $key = $param[ 'key' ];
                if ( !empty( $key ) ) {
                    TTransaction::open( 'afincco' );
                    Titulo::where( 'id', '=', $key )->set( 'tipolancamento_id', '91' )->update();
                    TTransaction::close();
                    $this->onReload( NULL );
                }
            } catch ( Exception $e ) {
                TTransaction::rollback();
                new TMessage( 'error', $e->getMessage() );
            }
        }
    }
