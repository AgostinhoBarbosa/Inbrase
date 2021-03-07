<?php
    
    use Adianti\Database\TCriteria;
    use Adianti\Database\TTransaction;
    use Adianti\Widget\Datagrid\TDataGrid;
    use Adianti\Widget\Datagrid\TPageNavigation;
    
    class pendenciasList extends TStandardList {

        public function __construct() {
            parent::__construct();
            parent::setDatabase( 'afincco' );
            parent::setActiveRecord( 'viewPendencias' );
            parent::setDefaultOrder( 'prazo', 'desc' );

            $this->form = new BootstrapFormBuilder( 'form_'.__CLASS__ );
            $this->form->setFormTitle( 'Listagem de Pendências' );

            $this->form->addAction( _t( 'Find' ), new TAction( array( $this, 'onSearch' ) ), 'fa:search' );
            $this->form->addAction('Exportar', new TAction(array($this, 'onExportCollection')), 'fa:table white');

            $this->datagrid        = new BootstrapDatagridWrapper( new TDataGrid() );

            $column_id           = new TDataGridColumn( 'id', 'Processo', 'center', '5%' );
            $column_placa        = new TDataGridColumn( 'placa', 'Placa', 'center', 20 );
            $column_chassi       = new TDataGridColumn( 'chassi', 'Chassi', 'center', 20 );
            $column_motor        = new TDataGridColumn( 'motor', 'Motor', 'center', 20 );
            $column_marca_modelo = new TDataGridColumn( 'marca_modelo', 'Marca/Modelo', 'center' );
            $column_cor          = new TDataGridColumn( 'cor', 'Cor', 'center', 10 );
            $column_sinistro     = new TDataGridColumn( 'sinistro', 'Sinistro', 'center' );
            $column_seguradora   = new TDataGridColumn( 'nome', 'Seguradora', 'left' );
            $column_status       = new TDataGridColumn( 'statu', 'Status Atual', 'left' );
            $column_data         = new TDataGridColumn( 'data_cadastro', 'Data Status', 'center' );
            $column_prazo        = new TDataGridColumn( 'prazo', 'Dias Status', 'center' );
            $column_usuario      = new TDataGridColumn( 'representante', 'Usuário', 'left', 150 );

            $this->datagrid->addColumn( $column_id );
            $this->datagrid->addColumn( $column_placa );
            $this->datagrid->addColumn( $column_chassi );
            $this->datagrid->addColumn( $column_motor );
            $this->datagrid->addColumn( $column_marca_modelo );
            $this->datagrid->addColumn( $column_cor );
            $this->datagrid->addColumn( $column_sinistro );
            $this->datagrid->addColumn( $column_seguradora );
            $this->datagrid->addColumn( $column_status );
            $this->datagrid->addColumn( $column_data );
            $this->datagrid->addColumn( $column_prazo );
            $this->datagrid->addColumn( $column_usuario );

            $column_data->setTransformer(
                function( $value, $object, $row ) {
                    $date = new DateTime( $value );

                    return $date->format( 'd/m/Y H:i:s' );
                }
            );

            $column_prazo->setTransformer(
                function( $value, $object, $row ) {
                    return  "<p style='color:red;font-weight: 900;'>".$value."</p>";
                }
            );
            $column_usuario->setTransformer(
                function( $value, $object, $row ) {
                    if (strlen($value) > 2) {
                        return "<p style='color:blue;font-weight: 900;'>".$value."</p>";
                    }else{
                        return "<p style='color:blue;font-weight: 900;'>".$value."</p>";
                    }
                }
            );

            $this->datagrid->createModel();

            $this->pageNavigation = new TPageNavigation();
            $this->pageNavigation->setAction( new TAction( array( $this, 'onReload' ) ) );
            $this->pageNavigation->setWidth( $this->datagrid->getWidth() );
    
            $panel                                = new TPanelGroup();
            $panel->add( $this->datagrid )->style = 'overflow-x:auto';
            $panel->addFooter( $this->pageNavigation );
    
            $container        = new TVBox();
            $container->style = 'width: 100%';
            $container->add( $this->form );
            $container->add( $panel );
            parent::add( $container );
        }

        public function onExportCollection($param)
        {
            try {
                TTransaction::open($this->database);

                $repository = new TRepository($this->activeRecord);

                $criteria = new TCriteria;

                if ($this->order) {
                    $criteria->setProperty('order', $this->order);
                    $criteria->setProperty('direction', $this->direction);
                }

                $objects = $repository->load($criteria, false);

                if ($objects) {
                    require_once('app/lib/PHPExcel/PHPExcel/IOFactory.php');
                    set_include_path(get_include_path().PATH_SEPARATOR.'../../../Classes/');

                    $Arquivo = "tmp/processos_".rand().".xlsx";

                    $objPHPExcel = new PHPExcel();
                    $objPHPExcel->setActiveSheetIndex(0);
                    $objPHPExcel->getActiveSheet()->setTitle('Processos');

                    $objPHPExcel->setActiveSheetIndex(0);

                    $sharedStyle1 = new PHPExcel_Style();
                    $sharedStyle2 = new PHPExcel_Style();
                    $sharedStyle3 = new PHPExcel_Style();
                    $sharedStyle4 = new PHPExcel_Style();
                    $sharedStyle5 = new PHPExcel_Style();

                    $sharedStyle1->applyFromArray(
                        array(
                            'fill'      => array(
                                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('argb' => 'FFCCFFCC'),
                            ),
                            'borders'   => array(
                                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                'top'    => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                'right'  => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                'left'   => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                            ),
                            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            ),
                        )
                    );
                    $sharedStyle2->applyFromArray(
                        array(
                            'fill'      => array(
                                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                                'color' => array('argb' => 'FFFFFF00'),
                            ),
                            'borders'   => array(
                                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                'top'    => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                'right'  => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                'left'   => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                            ),
                            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            ),
                        )
                    );
                    $sharedStyle3->applyFromArray(
                        array(
                            'borders'   => array(
                                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                'top'    => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                'right'  => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                'left'   => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                            ),
                            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            ),
                        )
                    );
                    $sharedStyle4->applyFromArray(
                        array(
                            'borders'   => array(
                                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                'top'    => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                'right'  => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                'left'   => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                            ),
                            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                            ),
                        )
                    );
                    $sharedStyle5->applyFromArray(
                        array(
                            'borders'   => array(
                                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                'top'    => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                'right'  => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                'left'   => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                            ),
                            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                            ),
                        )
                    );

                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A1:L1");

                    $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'LISTAGEM DE PENDÊNCIAS');
                    $objPHPExcel->getActiveSheet()->mergeCells('A1:L1');

                    $objPHPExcel->getActiveSheet()->SetCellValue('A2', 'PROCESSO');
                    $objPHPExcel->getActiveSheet()->SetCellValue('B2', 'PLACA');
                    $objPHPExcel->getActiveSheet()->SetCellValue('C2', 'CHASSI');
                    $objPHPExcel->getActiveSheet()->SetCellValue('D2', 'MOTOR');
                    $objPHPExcel->getActiveSheet()->SetCellValue('E2', 'MARCA/MODELO');
                    $objPHPExcel->getActiveSheet()->SetCellValue('F2', 'COR');
                    $objPHPExcel->getActiveSheet()->SetCellValue('G2', 'SINISTRO');
                    $objPHPExcel->getActiveSheet()->SetCellValue('H2', 'SEGURADORA');
                    $objPHPExcel->getActiveSheet()->SetCellValue('I2', 'STATUS ATUAL');
                    $objPHPExcel->getActiveSheet()->SetCellValue('J2', 'DATA STATUS');
                    $objPHPExcel->getActiveSheet()->SetCellValue('K2', 'DIAS STATUS');
                    $objPHPExcel->getActiveSheet()->SetCellValue('L2', 'USUÁRIO');
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "A2:L2");

                    $linha = 2;
                    date_default_timezone_set('America/Sao_Paulo');

                    foreach ($objects as $object) {
                        set_time_limit(0);

                        $linha++;

                        $date    = new DateTime($object->data_cadastro);

                        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$linha, $object->id, PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$linha, utf8_encode($object->placa), PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$linha, utf8_encode($object->chassi), PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$linha, utf8_encode($object->motor), PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$linha, utf8_encode($object->marca_modelo), PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$linha, utf8_encode($object->cor), PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$linha, utf8_encode($object->sinistro), PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$linha, utf8_encode($object->nome), PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$linha, $object->statu, PHPExcel_Cell_DataType::TYPE_STRING);
                        $data_status = date('d/m/Y H:i:s', strtotime($object->data_cadastro));
                        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$linha, utf8_encode($data_status), PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$linha, utf8_encode($object->prazo), PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$linha, utf8_encode($object->representante), PHPExcel_Cell_DataType::TYPE_STRING);

                    }

                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle3, "A3:B".$linha);
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle4, "C3:E".$linha);
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle3, "F3:F".$linha);
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle4, "G3:I".$linha);
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle3, "J3:K".$linha);
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle4, "L3:L".$linha);

                    $objPHPExcel->getActiveSheet()->getStyle("J3:J".$linha)->getNumberFormat()->setFormatCode('dd/mm/yyyy hh:mm:ss');

                    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                        $objPHPExcel->setActiveSheetIndex($objPHPExcel->getIndex($worksheet));

                        $sheet        = $objPHPExcel->getActiveSheet();
                        $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
                        $cellIterator->setIterateOnlyExistingCells(true);
                        foreach ($cellIterator as $cell) {
                            $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
                        }
                    }

                    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
                    $objWriter->save($Arquivo, __FILE__);
                    parent::openFile($Arquivo);
                }

                TTransaction::close();
            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        }

    }
