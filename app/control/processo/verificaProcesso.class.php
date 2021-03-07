<?php
    
    use Adianti\Base\TStandardList;
    use Adianti\Database\TTransaction;
    use Adianti\Registry\TSession;
    use Adianti\Widget\Datagrid\TDataGrid;
    use Adianti\Widget\Datagrid\TDataGridColumn;
    use Adianti\Widget\Dialog\TMessage;
    
    class verificaProcesso extends TStandardList
    {
        protected $form;
        protected $datagrid;
        protected $pageNavigation;

        public function __construct()
        {
            parent::__construct();

            parent::setDatabase( 'afincco' );
            parent::setActiveRecord( 'Processo' );
            parent::setDefaultOrder( 'liberador', 'asc' );

            $this->form = new BootstrapFormBuilder('form_'.__CLASS__);
            $this->form->setFormTitle('Verifica Processos');

            $arquivo = new TFile('arquivo');
            $arquivo->setAllowedExtensions(['xlx', 'xlsx']);

            $row         = $this->form->addFields([new TLabel('Arquivo'), $arquivo]);
            $row->layout = ['col-sm-4'];

            $this->form->setData(TSession::getValue('Processoa_filter_data'));

            $this->form->addAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search blue');

            $this->datagrid        = new BootstrapDatagridWrapper(new TDataGrid());

            $column_id         = new TDataGridColumn('id', 'Processo', 'center', 20);
            $column_placa      = new TDataGridColumn('placa', 'Placa', 'center', 50);
            $column_sinistro   = new TDataGridColumn('sinistro', 'Sinistro', 'left', 50);
            $column_chassi     = new TDataGridColumn('chassi', 'Chassi', 'left', 200);
            $column_motor      = new TDataGridColumn('motor', 'Motor', 'left', 200);
            $column_marca      = new TDataGridColumn('marca_modelo', 'Marca', 'left', 400);
            $column_ano        = new TDataGridColumn('ano', 'Ano', 'center', 50);
            $column_cor        = new TDataGridColumn('cor', 'Cor', 'center', 50);
            $column_seguradora = new TDataGridColumn('seguradoras->nome', 'Seguradora', 'left');

            $this->datagrid->addColumn($column_id);
            $this->datagrid->addColumn($column_placa);
            $this->datagrid->addColumn($column_sinistro);
            $this->datagrid->addColumn($column_chassi);
            $this->datagrid->addColumn($column_motor);
            $this->datagrid->addColumn($column_marca);
            $this->datagrid->addColumn($column_ano);
            $this->datagrid->addColumn($column_cor);
            $this->datagrid->addColumn($column_seguradora);

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

            parent::add($container);
        }

        public function onSearch( $parama = NULL)
        {
            ini_set('max_input_time', 0);
            ini_set('memory_limit', '4000M');
            ini_set('max_execution_time', 0);

            $data = $this->form->getData();
            TSession::setValue('valida_filter_data', $data);
            $this->form->setData($data);

            TSession::delValue('lista_verifica');
            KeepNavigation::clear(get_class($this));

            require_once('app/lib/PHPExcel/PHPExcel/IOFactory.php');
            set_include_path(get_include_path().PATH_SEPARATOR.'../../../Classes/');

            try {
                $lista = [];
                TTransaction::open('afincco');
                TTransaction::setLogger(new TLoggerSTD());

                $source_file   = 'tmp/'.$data->arquivo;
                if (file_exists($source_file)) {
                    $inputFileType = PHPExcel_IOFactory::identify( $source_file );
                    $objReader     = PHPExcel_IOFactory::createReader( $inputFileType );
                    $objPHPExcel   = $objReader->load( $source_file );
    
                    PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
                    $highestRow = $objPHPExcel->getActiveSheet()->getHighestRow();
    
                    $sheetData = $objPHPExcel->getActiveSheet()->toArray( NULL, TRUE, TRUE, TRUE );
                    $con       = TTransaction::get();
                    foreach ( $sheetData as $linha ) {
                        $placa  = trim( $linha[ 'A' ] );
                        $chassi = trim( $linha[ 'B' ] );
                        $query  = "select id from processoa where placa = ?";
        
                        $comando = $con->prepare( $query );
                        $comando->execute( [ $placa ] );
                        $processos = $comando->fetchAll();
                        if ( $processos ) {
                            foreach ( $processos as $proc ) {
                                $lista[] = $proc[ 'id' ];
                            }
                        } else {
                            if ( strlen( $chassi ) > 0 ) {
                                $query = "select id from processoa where chassi = ?";
                
                                $comando = $con->prepare( $query );
                                $comando->execute( [ $chassi ] );
                                $processos = $comando->fetchAll();
                                if ( $processos ) {
                                    foreach ( $processos as $proc ) {
                                        $lista[] = $proc[ 'id' ];
                                    }
                                }
                            }
                        }
                    }
                    unlink( $source_file );
                    $filtro = new TFilter( 'id', 'IN', $lista );
                    TSession::setValue( 'lista_verifica', $filtro );
                }
            } catch
            (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            } finally {
                TTransaction::close();
            }

            if (count($lista) > 0) {
                $param                 = array();
                $param[ 'offset' ]     = 0;
                $param[ 'first_page' ] = 1;
                $this->onReload($param);
            } else {
                new TMessage('info', 'Não foi localizado no sistema nenhum processo contendo as placas/chassi que estão na planilha.');
            }
        }

        public function onReload($param = null)
        {
            if (!TSession::getValue('lista_verifica')) {
                return false;
            }
    
            try {
                TTransaction::open('afincco');

                $repository = new TRepository('Processo');

                $limit      =  10;
                $criteria   =  new TCriteria;
                
                $param = KeepNavigation::update($param, get_class($this));
                
                if ($this->order) {
                    $criteria->setProperty('order', $this->order);
                    $criteria->setProperty('direction', $this->direction);
                }

                $criteria->setProperties($param);
                $criteria->setProperty('limit', $limit);

                if (TSession::getValue('lista_verifica')) {
                    $criteria->add(TSession::getValue('lista_verifica'));
                }

                $objects = $repository->load($criteria, false);

                if (is_callable($this->transformCallback)) {
                    call_user_func($this->transformCallback, $objects, $param);
                }

                $this->datagrid->clear();
                if ($objects) {
                    foreach ($objects as $object) {
                        $this->datagrid->addItem($object);
                    }
                }

                $criteria->resetProperties();
                $count = $repository->count($criteria);

                if (isset($this->pageNavigation)) {
                    $this->pageNavigation->setCount($count);
                    $this->pageNavigation->setProperties($param);
                    $this->pageNavigation->setLimit($limit);
                }

                TTransaction::close();
                $this->loaded = true;
            } catch (Exception $e)
            {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        }

    }
