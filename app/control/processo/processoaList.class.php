<?php
    
    use Adianti\Base\TStandardList;
    use Adianti\Database\TCriteria;
    use Adianti\Database\TExpression;
    use Adianti\Database\TFilter;
    use Adianti\Database\TTransaction;
    use Adianti\Registry\TSession;
    use Adianti\Widget\Form\TCombo;
    use Adianti\Widget\Form\TDate;
    use Adianti\Wrapper\BootstrapFormBuilder;
    
    class processoaList extends TStandardList
    {

        public function __construct() {
            parent::__construct();

            parent::setDatabase( 'afincco' );
            parent::setActiveRecord( 'Processo' );
            parent::setDefaultOrder( 'liberador asc, id desc', '' );

            $this->form = new BootstrapFormBuilder( 'form_'.__CLASS__ );
            $this->form->setFormTitle( 'Processos' );

            $criteria_liberador = new TCriteria();
            $filter             = new TFilter( 'liberador', '=', '1' );
            $criteria_liberador->add( $filter );

            $criteria_seguradora = new TCriteria();
            $filter             = new TFilter( 'seguradora', '=', '1' );
            $criteria_seguradora->add( $filter );

            if ( in_array( '5', TSession::getValue( 'usergroupids' ) ) ) {
                $filter = new TFilter( 'usuario', '=', TSession::getValue( 'userid' ) );
                $criteria_liberador->add( $filter );
            }

            $id                    = new TEntry( 'id' );
            $placa                 = new TEntry( 'placa' );
            $chassi                = new TEntry( 'chassi' );
            $motor                 = new TEntry( 'motor' );
            $data_cadastro_ini     = new TDate( 'data_cadastro_ini' );
            $data_cadastro_fim     = new TDate( 'data_cadastro_fim' );
            $id_seguradora         = new TDBCombo( 'id_seguradora', 'afincco', 'Pessoa', 'id', 'nome', 'nome', $criteria_seguradora );
            $sinistro              = new TEntry( 'sinistro' );
            $usuario               = new TDBCombo( 'usuario', 'permission', 'SystemUser', 'id', 'name', 'name' );
            $status                = new TDBCombo( 'status', 'afincco', 'Status', 'id', 'statu', 'statu' );
            $processo_origem       = new TEntry( 'processo_origem' );
            $processo_reintegracao = new TEntry( 'processo_reintegracao' );
            $cidade_rec            = new TEntry( 'cidade_rec' );
            $uf_rec                = new TCombo( 'uf_rec' );
            $liberador             = new TDBCombo( 'liberador', 'afincco', 'Pessoa', 'id', 'nome', 'nome', $criteria_liberador );
            $tipo_liberacao_dev    = new TCombo( 'tipo_liberacao_dev' );

            $data_cadastro_ini->setMask( 'dd/mm/yyyy' );
            $data_cadastro_fim->setMask( 'dd/mm/yyyy' );
            $data_cadastro_ini->setDatabaseMask( 'yyyy-mm-dd' );
            $data_cadastro_fim->setDatabaseMask( 'yyyy-mm-dd' );

            $uf_rec->addItems( Utilidades::uf() );
            $tipo_liberacao_dev->addItems( Utilidades::tipo_liberacao() );

            $id_seguradora->enableSearch();

            $id->style                = "background-color: #FFFEEB;";
            $placa->style             = "background-color: #FFFEEB;text-transform: uppercase;";
            $chassi->style            = "background-color: #FFFEEB;text-transform: uppercase;";
            $motor->style             = "background-color: #FFFEEB;text-transform: uppercase;";
            $data_cadastro_ini->style = "background-color: #FFFEEB;";
            $data_cadastro_fim->style = "background-color: #FFFEEB;";
            $id_seguradora->style     = "background-color: #FFFEEB;";

            $campo_codigo                 = [ new TLabel( 'Processo' ), $id ];
            $campo_placa                  = [ new TLabel( 'Placa' ), $placa ];
            $campo_chassi                 = [ new TLabel( 'Nº Chassi' ), $chassi ];
            $campo_motor                  = [ new TLabel( 'Nº Motor' ), $motor ];
            $campo_sinistro               = [ new TLabel( 'Nº. Sinistro' ), $sinistro ];
            $campo_usuario                = [ new TLabel( 'Usuário' ), $usuario ];
            $campo_data_cadastro_ini      = [ new TLabel( 'Data Cadastro Inicial' ), $data_cadastro_ini ];
            $campo_data_cadastro_fim      = [ new TLabel( 'Data Cadastro Final' ), $data_cadastro_fim ];
            $campo_seguradora             = [ new TLabel( 'Seguradora' ), $id_seguradora ];
            $campo_status                 = [ new TLabel( 'Status' ), $status ];
            $campo_processo_origem        = [ new TLabel( 'Nº. Processo de Origem' ), $processo_origem ];
            $campo_processo_reintewrgacao = [ new TLabel( 'Nº. Processo de Reintegração' ), $processo_reintegracao ];
            $campo_cidade_recuperacao     = [ new TLabel( 'Cidade Recuperação' ), $cidade_rec ];
            $campo_uf_recuperacao         = [ new TLabel( 'UF Recuperação' ), $uf_rec ];
            $campo_liberador              = [ new TLabel( 'Liberador' ), $liberador ];
            $campo_tipo_liberacao_dev     = [ new TLabel( 'Tipo Liberação' ), $tipo_liberacao_dev ];

            $row         = $this->form->addFields( $campo_codigo, $campo_placa, $campo_chassi, $campo_motor, $campo_sinistro, $campo_usuario );
            $row->layout = [ 'col-md-1', 'col-md-1', 'col-md-2', 'col-md-2', 'col-md-2', 'col-md-4' ];
            $row         = $this->form->addFields( $campo_data_cadastro_ini, $campo_data_cadastro_fim, $campo_seguradora, $campo_status );
            $row->layout = [ 'col-md-2', 'col-md-2', 'col-md-4', 'col-md-4' ];
            $row         = $this->form->addFields( $campo_processo_origem, $campo_processo_reintewrgacao, $campo_cidade_recuperacao, $campo_uf_recuperacao, $campo_liberador, $campo_tipo_liberacao_dev );
            $row->layout = [ 'col-md-2', 'col-md-2', 'col-md-2', 'col-md-2', 'col-md-2', 'col-md-2' ];
            $row->layout = [ 'col-md-2', 'col-md-2', 'col-md-2', 'col-md-2', 'col-md-2', 'col-md-2' ];

            $this->form->addAction( _t( 'Find' ), new TAction( [ $this, 'onSearch' ] ), 'fa:search blue' );
            $this->form->addActionlink( _t( 'New' ), new TAction( [ 'processoaForm', 'onEdit' ] ), 'fa:eraser red' );

            if ( !in_array( '4', TSession::getValue( 'usergroupids' ) ) ) {
                if ( !in_array( '5', TSession::getValue( 'usergroupids' ) ) ) {
                    $this->form->addAction( 'Exportar', new TAction( [ $this,
                                                                       'onExportCollection' ] ), 'fa:table green' );
                }
            }

            if (TSession::getValue( 'Processoa_filter_data' )) {
                $this->form->setData( TSession::getValue( 'Processoa_filter_data' ) );
            }


            $this->datagrid        = new BootstrapDatagridWrapper( new TDataGrid() );
            $this->datagrid->setGroupColumn('liberador', '<b>Liberador</b>: <i>{liberadores->nome}</i>');

            $column_id            = new TDataGridColumn( 'id', 'Processo', 'center', '5%' );
            $column_data_cadastro = new TDataGridColumn( 'data_cadastro', 'Cadastro em', 'center', '10%' );
            $column_id_seg        = new TDataGridColumn( 'seguradoras->nome', 'Seguradora', 'left');
            $column_placa         = new TDataGridColumn( 'placa', 'Placa', 'center', '5%' );
            $column_chassi        = new TDataGridColumn( 'chassi', 'Chassi', 'center', '5%' );
            $column_marca_modelo  = new TDataGridColumn( 'marca_modelo', 'Marca/Modelo', 'center', '10%' );
            $column_cor           = new TDataGridColumn( 'cor', 'Cor', 'center', '5%' );
            $column_sinistro      = new TDataGridColumn( 'sinistro', 'Sinistro', 'center', '10%' );
            $column_usuario       = new TDataGridColumn( 'usuario', 'Usuario', 'left' );
            $column_cidaderec     = new TDataGridColumn( '{cidade_rec}({uf_rec})', 'Cidade Rec', 'center', '15%' );

            $column_id_seg->style = 'white-space: nowrap;';

            $this->datagrid->addColumn( $column_id );
            $this->datagrid->addColumn( $column_data_cadastro );
            $this->datagrid->addColumn( $column_id_seg );
            $this->datagrid->addColumn( $column_placa );
            $this->datagrid->addColumn( $column_chassi );
            $this->datagrid->addColumn( $column_marca_modelo );
            $this->datagrid->addColumn( $column_cor );
            $this->datagrid->addColumn( $column_sinistro );
            $this->datagrid->addColumn( $column_usuario );
            $this->datagrid->addColumn( $column_cidaderec );

            $order_id = new TAction( [ $this, 'onReload' ] );
            $order_id->setParameter( 'order', 'id' );
            $column_id->setAction( $order_id );

            $order_data_cadastro = new TAction( [ $this, 'onReload' ] );
            $order_data_cadastro->setParameter( 'order', 'data_cadastro' );
            $column_data_cadastro->setAction( $order_data_cadastro );

            $order_id_seg = new TAction( [ $this, 'onReload' ] );
            $order_id_seg->setParameter( 'order', 'id_seg' );
            $column_id_seg->setAction( $order_id_seg );

            $order_placa = new TAction( [ $this, 'onReload' ] );
            $order_placa->setParameter( 'order', 'placa' );
            $column_placa->setAction( $order_placa );

            $order_chassi = new TAction( [ $this, 'onReload' ] );
            $order_chassi->setParameter( 'order', 'chassi' );
            $column_chassi->setAction( $order_chassi );

            $order_marca_modelo = new TAction( [ $this, 'onReload' ] );
            $order_marca_modelo->setParameter( 'order', 'marca_modelo' );
            $column_marca_modelo->setAction( $order_marca_modelo );

            $order_cor = new TAction( [ $this, 'onReload' ] );
            $order_cor->setParameter( 'order', 'cor' );
            $column_cor->setAction( $order_cor );

            $order_sinistro = new TAction( [ $this, 'onReload' ] );
            $order_sinistro->setParameter( 'order', 'sinistro' );
            $column_sinistro->setAction( $order_sinistro );

            $column_data_cadastro->setTransformer( function( $value, $object, $row ) {
                return TDate::date2br( $value );
            } );

            $column_usuario->setTransformer( function( $value, $object, $row ) {
                return "<p style='color:red'>".$object->usuarios->name."</p>";
            } );

            $action_edit = new TDataGridAction( [ 'processoaForm', 'onEdit' ], [ 'key'            => '{id}'] );
            $action_del  = new TDataGridAction( [ $this, 'onDelete' ], [ 'key'            => '{id}',
                                                                         'register_state' => 'false' ] );

            $action_edit->setDisplayCondition( [ $this, 'onDisplayEdit' ] );
            $action_del->setDisplayCondition( [ $this, 'displayColumn' ] );

            $this->datagrid->addAction( $action_edit, _t( 'Edit' ), 'far:edit blue fa-lg' );
            $this->datagrid->addAction( $action_del, _t( 'Delete' ), 'far:trash-alt red fa-lg' );

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
            $panel->addHeaderWidget( $dropdown );

            $container        = new TVBox();
            $container->style = 'width: 100%';
            $container->add( $this->form );
            $container->add( $panel );

            $container->adianti_target_container = 'processo';
            $container->adianti_target_title     = 'Processos';

            parent::add( $container );

        }

        public function onDisplayEdit( $object ) {
            if ( in_array( '1', TSession::getValue( 'usergroupids' ) ) ) {
                return TRUE;
            }
            if ( in_array( '5', TSession::getValue( 'usergroupids' ) ) ) {
                if ( $object->liberadores->usuario == TSession::getValue( 'userid' ) ) {
                    if ( $object->onValidaStatus( 19 ) > 0 ) {
                        return FALSE;
                    }
                    return TRUE;
                } else {
                    if ( empty( $object->liberadores ) ) {
                        if ( $object->usuario == TSession::getValue( 'userid' ) ) {
                            return TRUE;
                        }
                    }
                }
                return FALSE;
            }

            return TRUE;
        }

        public function displayColumn( $object ) {
            $grupos = TSession::getValue( 'usergroupids' );
            if ( in_array( '1', $grupos ) ) {
                return TRUE;
            }

            return FALSE;
        }

        public function onExportCollection( $param ) {
            try {
                TTransaction::open( $this->database );

                $repository = new TRepository( $this->activeRecord );

                $criteria = new TCriteria();

                if ( $this->order ) {
                    $criteria->setProperty( 'order', $this->order );
                    $criteria->setProperty( 'direction', $this->direction );
                }

                $criteria->setProperties( $param );

                $tem_filtro = FALSE;

                if ( TSession::getValue( 'ProcessoaList_filter_id' ) ) {
                    $criteria->add( TSession::getValue( 'ProcessoaList_filter_id' ) );
                    $tem_filtro = TRUE;
                }

                if ( TSession::getValue( 'ProcessoaList_filter_placa' ) ) {
                    $criteria->add( TSession::getValue( 'ProcessoaList_filter_placa' ) );
                    $tem_filtro = TRUE;
                }
                if ( TSession::getValue( 'ProcessoaList_filter_chassi' ) ) {
                    $criteria->add( TSession::getValue( 'ProcessoaList_filter_chassi' ) );
                    $tem_filtro = TRUE;
                }
                if ( TSession::getValue( 'ProcessoaList_filter_motor' ) ) {
                    $criteria->add( TSession::getValue( 'ProcessoaList_filter_motor' ) );
                    $tem_filtro = TRUE;
                }
                if ( TSession::getValue( 'ProcessoaList_filter_data' ) ) {
                    $criteria->add( TSession::getValue( 'ProcessoaList_filter_data' ) );
                    $tem_filtro = TRUE;
                }
                if ( TSession::getValue( 'ProcessoaList_filter_seguradora' ) ) {
                    $criteria->add( TSession::getValue( 'ProcessoaList_filter_seguradora' ) );
                    $tem_filtro = TRUE;
                }

                if ( TSession::getValue( 'ProcessoaList_filter_sinistro' ) ) {
                    $criteria->add( TSession::getValue( 'ProcessoaList_filter_sinistro' ) );
                    $tem_filtro = TRUE;
                }
                if ( TSession::getValue( 'ProcessoaList_filter_cidade_rec' ) ) {
                    $criteria->add( TSession::getValue( 'ProcessoaList_filter_cidade_rec' ) );
                    $tem_filtro = TRUE;
                }
                if ( TSession::getValue( 'ProcessoaList_filter_uf_rec' ) ) {
                    $criteria->add( TSession::getValue( 'ProcessoaList_filter_uf_rec' ) );
                    $tem_filtro = TRUE;
                }
                if ( TSession::getValue( 'ProcessoaList_filter_processo_origem' ) ) {
                    $criteria->add( TSession::getValue( 'ProcessoaList_filter_processo_origem' ) );
                    $tem_filtro = TRUE;
                }
                if ( TSession::getValue( 'ProcessoaList_filter_processo_reintegracao' ) ) {
                    $criteria->add( TSession::getValue( 'ProcessoaList_filter_processo_reintegracao' ) );
                    $tem_filtro = TRUE;
                }
                if ( TSession::getValue( 'ProcessoaList_filter_usuario' ) ) {
                    $criteria->add( TSession::getValue( 'ProcessoaList_filter_usuario' ) );
                    $tem_filtro = TRUE;
                }

                if ( TSession::getValue( 'ProcessoaList_filter_status' ) ) {
                    $criteria->add( TSession::getValue( 'ProcessoaList_filter_status' ) );
                    $tem_filtro = TRUE;
                }

                if ( TSession::getValue( 'ProcessoaList_filter_liberador' ) ) {
                    $criteria->add( TSession::getValue( 'ProcessoaList_filter_liberador' ) );
                    $tem_filtro = TRUE;
                }
                if ( TSession::getValue( 'ProcessoaList_filter_tipo_liberacao' ) ) {
                    $criteria->add( TSession::getValue( 'ProcessoaList_filter_tipo_liberacao' ) );
                    $tem_filtro = TRUE;
                }


                if ( $tem_filtro == FALSE ) {
                    $filter = new TFilter( 'id', '=', 0 );
                    $criteria->add( $filter );

                }

                $objects = $repository->load( $criteria, FALSE );

                if ( $objects ) {
                    require_once( 'app/lib/PHPExcel/PHPExcel/IOFactory.php' );
                    set_include_path( get_include_path().PATH_SEPARATOR.'../../../Classes/' );

                    $Arquivo = "tmp/processos_".rand().".xlsx";

                    $objPHPExcel = new PHPExcel();
                    $objPHPExcel->setActiveSheetIndex( 0 );
                    $objPHPExcel->getActiveSheet()->setTitle( 'Processos' );

                    $objPHPExcel->setActiveSheetIndex( 0 );

                    $sharedStyle1 = new PHPExcel_Style();
                    $sharedStyle2 = new PHPExcel_Style();
                    $sharedStyle3 = new PHPExcel_Style();
                    $sharedStyle4 = new PHPExcel_Style();
                    $sharedStyle5 = new PHPExcel_Style();

                    $sharedStyle1->applyFromArray( [ 'fill' => [ 'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                                                                 'color' => [ 'argb' => 'FFCCFFCC' ], ],
                                                     'borders' => [ 'bottom' => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ],
                                                                    'top'    => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ],
                                                                    'right'  => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ],
                                                                    'left'   => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ], ],
                                                     'alignment' => [ 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, ], ] );
                    $sharedStyle2->applyFromArray( [ 'fill' => [ 'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                                                                 'color' => [ 'argb' => 'FFFFFF00' ], ],
                                                     'borders' => [ 'bottom' => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ],
                                                                    'top'    => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ],
                                                                    'right'  => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ],
                                                                    'left'   => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ], ],
                                                     'alignment' => [ 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, ], ] );
                    $sharedStyle3->applyFromArray( [ 'borders'   => [ 'bottom' => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ],
                                                                      'top'    => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ],
                                                                      'right'  => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ],
                                                                      'left'   => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ], ],
                                                     'alignment' => [ 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, ], ] );
                    $sharedStyle4->applyFromArray( [ 'borders'   => [ 'bottom' => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ],
                                                                      'top'    => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ],
                                                                      'right'  => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ],
                                                                      'left'   => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ], ],
                                                     'alignment' => [ 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, ], ] );
                    $sharedStyle5->applyFromArray( [ 'borders'   => [ 'bottom' => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ],
                                                                      'top'    => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ],
                                                                      'right'  => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ],
                                                                      'left'   => [ 'style' => PHPExcel_Style_Border::BORDER_THIN ], ],
                                                     'alignment' => [ 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT, ], ] );

                    $objPHPExcel->getActiveSheet()->setSharedStyle( $sharedStyle1, "A1:P1" );

                    $objPHPExcel->getActiveSheet()->SetCellValue( 'A1', 'LISTAGEM DE PROCESSOS' );
                    $objPHPExcel->getActiveSheet()->mergeCells( 'A1:P1' );

                    $objPHPExcel->getActiveSheet()->SetCellValue( 'A2', 'PROCESSO' );
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'B2', 'DATA CADASTRO' );
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'C2', 'SEGURADORA' );
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'D2', 'TIPO' );
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'E2', 'PLACA' );
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'F2', 'CHASSI' );
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'G2', 'MARCA/MODELO' );
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'H2', 'COR' );
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'I2', 'SINISTRO' );
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'J2', 'USUARIO' );
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'K2', 'UF REC' );
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'L2', 'STATUS' );
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'M2', 'DATA STATUS' );
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'N2', 'LIBERADOR' );
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'O2', 'CIDADE REC' );
                    $objPHPExcel->getActiveSheet()->SetCellValue( 'P2', 'UF REC' );
                    $objPHPExcel->getActiveSheet()->setSharedStyle( $sharedStyle2, "A2:P2" );

                    $linha = 2;
                    date_default_timezone_set( 'America/Sao_Paulo' );

                    foreach ( $objects as $object ) {
                        set_time_limit( 0 );

                        $linha++;

                        $date    = new DateTime( $object->data_cadastro );
                        $hstatus = $object->get_hstatus()[ 0 ];
                        $status  = str_replace( '<p style=\'color:#ff0000;font-weight:bold;\'>', '', $hstatus->get_status()->statu );
                        $status  = str_replace( '</p>', '', $status );

                        $objPHPExcel->getActiveSheet()->SetCellValue( 'A'.$linha, $object->id, PHPExcel_Cell_DataType::TYPE_STRING );
                        $objPHPExcel->getActiveSheet()->SetCellValue( 'B'.$linha, $date->format( 'd/m/Y H:i:s' ) );
                        $objPHPExcel->getActiveSheet()->SetCellValue( 'C'.$linha, utf8_encode( $object->seguradoras->nome ), PHPExcel_Cell_DataType::TYPE_STRING );
                        $objPHPExcel->getActiveSheet()->SetCellValue( 'D'.$linha, utf8_encode( $object->tipo ), PHPExcel_Cell_DataType::TYPE_STRING );
                        $objPHPExcel->getActiveSheet()->SetCellValue( 'E'.$linha, utf8_encode( $object->placa ), PHPExcel_Cell_DataType::TYPE_STRING );
                        $objPHPExcel->getActiveSheet()->SetCellValue( 'F'.$linha, utf8_encode( $object->chassi ), PHPExcel_Cell_DataType::TYPE_STRING );
                        $objPHPExcel->getActiveSheet()->SetCellValue( 'G'.$linha, utf8_encode( $object->marca_modelo ), PHPExcel_Cell_DataType::TYPE_STRING );
                        $objPHPExcel->getActiveSheet()->SetCellValue( 'H'.$linha, utf8_encode( $object->cor ), PHPExcel_Cell_DataType::TYPE_STRING );
                        $objPHPExcel->getActiveSheet()->SetCellValue( 'I'.$linha, utf8_encode( $object->sinistro ), PHPExcel_Cell_DataType::TYPE_STRING );
                        $objPHPExcel->getActiveSheet()->SetCellValue( 'J'.$linha, utf8_encode( $object->usuarios->name ), PHPExcel_Cell_DataType::TYPE_STRING );
                        $objPHPExcel->getActiveSheet()->SetCellValue( 'K'.$linha, utf8_encode( $object->uf_rec ), PHPExcel_Cell_DataType::TYPE_STRING );
                        $objPHPExcel->getActiveSheet()->SetCellValue( 'L'.$linha, $status, PHPExcel_Cell_DataType::TYPE_STRING );
                        $data_status = date( 'd/m/Y H:i:s', strtotime( $hstatus->data_cadastro ) );
                        $objPHPExcel->getActiveSheet()->SetCellValue( 'M'.$linha, utf8_encode( $data_status ), PHPExcel_Cell_DataType::TYPE_STRING );
                        $objPHPExcel->getActiveSheet()->SetCellValue( 'N'.$linha, utf8_encode( $object->liberadores->nome ), PHPExcel_Cell_DataType::TYPE_STRING );
                        $objPHPExcel->getActiveSheet()->SetCellValue( 'O'.$linha, utf8_encode( $object->cidade_rec ), PHPExcel_Cell_DataType::TYPE_STRING );
                        $objPHPExcel->getActiveSheet()->SetCellValue( 'P'.$linha, utf8_encode( $object->uf_rec ), PHPExcel_Cell_DataType::TYPE_STRING );
                    }

                    $objPHPExcel->getActiveSheet()->setSharedStyle( $sharedStyle3, "A3:B".$linha );
                    $objPHPExcel->getActiveSheet()->setSharedStyle( $sharedStyle4, "C3:C".$linha );
                    $objPHPExcel->getActiveSheet()->setSharedStyle( $sharedStyle3, "D3:F".$linha );
                    $objPHPExcel->getActiveSheet()->setSharedStyle( $sharedStyle4, "G3:G".$linha );
                    $objPHPExcel->getActiveSheet()->setSharedStyle( $sharedStyle3, "H3:I".$linha );
                    $objPHPExcel->getActiveSheet()->setSharedStyle( $sharedStyle4, "J3:J".$linha );
                    $objPHPExcel->getActiveSheet()->setSharedStyle( $sharedStyle3, "K3:K".$linha );
                    $objPHPExcel->getActiveSheet()->setSharedStyle( $sharedStyle4, "L3:L".$linha );
                    $objPHPExcel->getActiveSheet()->setSharedStyle( $sharedStyle3, "M3:M".$linha );
                    $objPHPExcel->getActiveSheet()->setSharedStyle( $sharedStyle4, "N3:P".$linha );

                    $objPHPExcel->getActiveSheet()->getStyle( "B3:B".$linha )->getNumberFormat()->setFormatCode( 'dd/mm/yyyy hh:mm:ss' );
                    $objPHPExcel->getActiveSheet()->getStyle( "M3:M".$linha )->getNumberFormat()->setFormatCode( 'dd/mm/yyyy hh:mm:ss' );

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

        public function onReload( $param = NULL ) {
            TSession::delValue( 'Processoa_status' );
            TSession::delValue( 'Processoa_arquivos' );
            TSession::delValue( 'Processoa_comprovante' );
            TSession::delValue( 'Processoa_data' );
            TSession::delValue( 'Processoa_chamador_comprovante' );

            try {
                if ( empty( $this->database ) ) {
                    throw new Exception( AdiantiCoreTranslator::translate( '^1 was not defined. You must call ^2 in ^3', AdiantiCoreTranslator::translate( 'Database' ), 'setDatabase()', AdiantiCoreTranslator::translate( 'Constructor' ) ) );
                }

                if ( empty( $this->activeRecord ) ) {
                    throw new Exception( AdiantiCoreTranslator::translate( '^1 was not defined. You must call ^2 in ^3', 'Active Record', 'setActiveRecord()', AdiantiCoreTranslator::translate( 'Constructor' ) ) );
                }

                TTransaction::open( $this->database );

                $repository = new TRepository( $this->activeRecord );
                $limit      = isset( $this->limit ) ? ( $this->limit > 0 ? $this->limit : NULL ) : 10;

                $criteria = new TCriteria();
    
                $param = KeepNavigation::update($param, get_class($this));
                
                if ( $this->order ) {
                    $criteria->setProperty( 'order', $this->order );
                    $criteria->setProperty( 'direction', $this->direction );
                }

                $criteria->setProperties( $param );
                $criteria->setProperty( 'limit', $limit );

                $tem_filtro = FALSE;

                if ( in_array( '5', TSession::getValue( 'usergroupids' ) ) ) {
                    $criteria_liberador = new TCriteria();
                    $filter             = new TFilter( 'liberador', '=', TSession::getValue( 'LIBERADOR' ) );
                    $criteria_liberador->add( $filter, TExpression::OR_OPERATOR );
                    $filter = new TFilter( 'usuario', '=', TSession::getValue( 'userid' ) );
                    $criteria_liberador->add( $filter, TExpression::OR_OPERATOR );
                    $criteria->add( $criteria_liberador, TExpression::OR_OPERATOR );
                    $tem_filtro = TRUE;
                }


                if ( TSession::getValue( 'ProcessoaList_filter_id' ) ) {
                    $criteria->add( TSession::getValue( 'ProcessoaList_filter_id' ) );
                    $tem_filtro = TRUE;
                }

                if ( TSession::getValue( 'ProcessoaList_filter_placa' ) ) {
                    $criteria->add( TSession::getValue( 'ProcessoaList_filter_placa' ) );
                    $tem_filtro = TRUE;
                }
                if ( TSession::getValue( 'ProcessoaList_filter_chassi' ) ) {
                    $criteria->add( TSession::getValue( 'ProcessoaList_filter_chassi' ) );
                    $tem_filtro = TRUE;
                }
                if ( TSession::getValue( 'ProcessoaList_filter_motor' ) ) {
                    $criteria->add( TSession::getValue( 'ProcessoaList_filter_motor' ) );
                    $tem_filtro = TRUE;
                }
                if ( TSession::getValue( 'ProcessoaList_filter_data' ) ) {
                    $criteria->add( TSession::getValue( 'ProcessoaList_filter_data' ) );
                    $tem_filtro = TRUE;
                }
                if ( TSession::getValue( 'ProcessoaList_filter_seguradora' ) ) {
                    $criteria->add( TSession::getValue( 'ProcessoaList_filter_seguradora' ) );
                    $tem_filtro = TRUE;
                }

                if ( TSession::getValue( 'ProcessoaList_filter_sinistro' ) ) {
                    $criteria->add( TSession::getValue( 'ProcessoaList_filter_sinistro' ) );
                    $tem_filtro = TRUE;
                }
                if ( TSession::getValue( 'ProcessoaList_filter_cidade_rec' ) ) {
                    $criteria->add( TSession::getValue( 'ProcessoaList_filter_cidade_rec' ) );
                    $tem_filtro = TRUE;
                }
                if ( TSession::getValue( 'ProcessoaList_filter_uf_rec' ) ) {
                    $criteria->add( TSession::getValue( 'ProcessoaList_filter_uf_rec' ) );
                    $tem_filtro = TRUE;
                }
                if ( TSession::getValue( 'ProcessoaList_filter_processo_origem' ) ) {
                    $criteria->add( TSession::getValue( 'ProcessoaList_filter_processo_origem' ) );
                    $tem_filtro = TRUE;
                }
                if ( TSession::getValue( 'ProcessoaList_filter_processo_reintegracao' ) ) {
                    $criteria->add( TSession::getValue( 'ProcessoaList_filter_processo_reintegracao' ) );
                    $tem_filtro = TRUE;
                }
                if ( TSession::getValue( 'ProcessoaList_filter_usuario' ) ) {
                    $criteria->add( TSession::getValue( 'ProcessoaList_filter_usuario' ) );
                    $tem_filtro = TRUE;
                }
                if ( TSession::getValue( 'ProcessoaList_filter_status' ) ) {
                    $criteria->add( TSession::getValue( 'ProcessoaList_filter_status' ) );
                    $tem_filtro = TRUE;
                }
                if ( TSession::getValue( 'ProcessoaList_filter_liberador' ) ) {
                    $criteria->add( TSession::getValue( 'ProcessoaList_filter_liberador' ) );
                    $tem_filtro = TRUE;
                }
                if ( TSession::getValue( 'ProcessoaList_filter_tipo_liberacao' ) ) {
                    $criteria->add( TSession::getValue( 'ProcessoaList_filter_tipo_liberacao' ) );
                    $tem_filtro = TRUE;
                }

                if ( $tem_filtro == FALSE ) {
                    $filter = new TFilter( 'id', '=', 0 );
                    $criteria->add( $filter );

                }

                $objects = $repository->load( $criteria, FALSE );

                if ( is_callable( $this->transformCallback ) ) {
                    call_user_func( $this->transformCallback, $objects, $param );
                }

                $this->datagrid->clear();

                if ( $objects ) {
                    foreach ( $objects as $object ) {
                        if (!$object->liberador){
                            $object->liberador = 0;
                        }
                        $this->datagrid->addItem( $object );
                    }
                }

                $criteria->resetProperties();
                $count = $repository->count( $criteria );

                if ( isset( $this->pageNavigation ) ) {
                    $this->pageNavigation->setCount( $count );
                    $this->pageNavigation->setProperties( $param );
                    $this->pageNavigation->setLimit( $limit );
                }

                $this->loaded = TRUE;
            } catch ( Exception $e )
            {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();
            } finally {
                TTransaction::close();
            }
        }

        public function onSearch( $param = null) {
            $data = $this->form->getData();

            TSession::delValue( 'ProcessoaList_filter_id' );
            TSession::delValue( 'ProcessoaList_filter_placa' );
            TSession::delValue( 'ProcessoaList_filter_chassi' );
            TSession::delValue( 'ProcessoaList_filter_motor' );
            TSession::delValue( 'ProcessoaList_filter_sinistro' );
            TSession::delValue( 'ProcessoaList_filter_usuario' );
            TSession::delValue( 'ProcessoaList_filter_liberador' );
            TSession::delValue( 'ProcessoaList_filter_seguradora' );
            TSession::delValue( 'ProcessoaList_filter_status' );
            TSession::delValue( 'ProcessoaList_filter_cidade_rec' );
            TSession::delValue( 'ProcessoaList_filter_processo_origem' );
            TSession::delValue( 'ProcessoaList_filter_processo_reintegracao' );
            TSession::delValue( 'ProcessoaList_filter_uf_rec' );
            TSession::delValue( 'ProcessoaList_filter_tipo_liberacao' );
    
            KeepNavigation::clear(get_class($this));
            
            if ( isset( $data->tipo_liberacao_dev ) AND ( $data->tipo_liberacao_dev ) ) {
                $filter = new TFilter( 'tipo_liberacao_dev', '=', $data->tipo_liberacao_dev );
                TSession::setValue( 'ProcessoaList_filter_tipo_liberacao', $filter );
            }

            if ( isset( $data->id ) AND ( $data->id ) ) {
                $filter = new TFilter( 'id', '=', $data->id );
                TSession::setValue( 'ProcessoaList_filter_id', $filter );
            }

            if ( isset( $data->placa ) AND ( $data->placa ) ) {
                $value  = strtoupper( $data->placa );
                $filter = new TFilter( 'placa', 'LIKE', "%".$value."%" );
                TSession::setValue( 'ProcessoaList_filter_placa', $filter );
            }
            if ( isset( $data->chassi ) AND ( $data->chassi ) ) {
                $value  = strtoupper( $data->chassi );
                $filter = new TFilter( 'chassi', 'LIKE', "%".$value."%" );
                TSession::setValue( 'ProcessoaList_filter_chassi', $filter );
            }

            if ( isset( $data->motor ) AND ( $data->motor ) ) {
                $value  = strtoupper( $data->motor );
                $filter = new TFilter( 'motor', 'LIKE', "%{$value}%" );
                TSession::setValue( 'ProcessoaList_filter_motor', $filter );
            }

            if ( isset( $data->cidade_rec ) AND ( $data->cidade_rec ) ) {
                $cidade = strtoupper( $data->cidade_rec );
                $filter = new TFilter( 'cidade_rec', 'LIKE', "{$cidade}%" );
                TSession::setValue( 'ProcessoaList_filter_cidade_rec', $filter );
            }

            if ( isset( $data->uf_rec ) AND ( $data->uf_rec ) ) {
                $ufrec  = strtoupper( $data->uf_rec );
                $filter = new TFilter( 'uf_rec', '=', "{$ufrec}" );
                TSession::setValue( 'ProcessoaList_filter_uf_rec', $filter );
            }

            if ( isset( $data->processo_origem ) AND ( $data->processo_origem ) ) {
                $filter = new TFilter( 'processo_origem', 'LIKE', "{$data->processo_origem}%" );
                TSession::setValue( 'ProcessoaList_filter_processo_origem', $filter );
            }

            if ( isset( $data->processo_reintegracao ) AND ( $data->processo_reintegracao ) ) {
                $filter = new TFilter( 'processo_reintegracao', 'LIKE', "{$data->processo_reintegracao}%" );
                TSession::setValue( 'ProcessoaList_filter_processo_reintegracao', $filter );
            }

            if ( isset( $data->id_seguradora ) AND ( $data->id_seguradora ) ) {
                $filter = new TFilter( 'id_seg', '=', "{$data->id_seguradora}" );
                TSession::setValue( 'ProcessoaList_filter_seguradora', $filter );
            }

            if ( isset( $data->liberador ) AND ( $data->liberador ) ) {
                $filter = new TFilter( 'liberador', '=', "{$data->liberador}" );
                TSession::setValue( 'ProcessoaList_filter_liberador', $filter );
            }

            if ( isset( $data->sinistro ) AND ( $data->sinistro ) ) {
                $filter = new TFilter( 'sinistro', 'like', "%{$data->sinistro}%" );
                TSession::setValue( 'ProcessoaList_filter_sinistro', $filter );
            }
            if ( isset( $data->usuario ) AND ( $data->usuario ) ) {
                $filter = new TFilter( 'usuario', '=', $data->usuario );
                TSession::setValue( 'ProcessoaList_filter_usuario', $filter );
            }
            if ( isset( $data->status ) AND ( $data->status ) ) {
                $filter = new TFilter( 'id', 'in', '(select id_processo from hstatus where hstatus.id in  (select max(id) from hstatus GROUP BY id_processo order by id_processo) and hstatus.id_status = '.$data->status.')' );
                TSession::setValue( 'ProcessoaList_filter_status', $filter );
            }

            if ( isset( $data->data_cadastro_ini ) AND ( $data->data_cadastro_ini ) ) {
                if ( !$data->data_cadastro_fim ) {
                    $data->data_cadastro_fim = $data->data_cadastro_ini;
                }
                $filter = new TFilter( 'data_cadastro', 'BETWEEN', $data->data_cadastro_ini.' 00:00:00', $data->data_cadastro_fim.' 23:59:59' );
                TSession::setValue( 'ProcessoaList_filter_data', $filter );
            }

            TSession::setValue( $this->activeRecord . '_filter_data', $data );
            TSession::setValue( get_class( $this ) . '_filter_data', $data );

            $this->form->setData( $data );

            $param                 = [];
            $param[ 'offset' ]     = 0;
            $param[ 'first_page' ] = 1;
            $this->onReload( $param );
        }

    }
