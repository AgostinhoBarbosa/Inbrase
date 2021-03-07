<?php
    
    use Adianti\Base\TStandardList;
    use Adianti\Core\AdiantiCoreTranslator;
    use Adianti\Database\TCriteria;
    use Adianti\Database\TFilter;
    use Adianti\Database\TTransaction;
    use Adianti\Registry\TSession;
    use Adianti\Widget\Dialog\TMessage;
    use Adianti\Widget\Form\TDate;
    use Adianti\Widget\Wrapper\TDBCombo;
    use Adianti\Wrapper\BootstrapFormBuilder;
    
    class recibosList extends TStandardList
    {
        
        public function __construct()
        {
            parent::__construct();
            
            $this->setDatabase( 'afincco' );
            $this->setActiveRecord( 'Recibos' );
            $this->setDefaultOrder( 'id', 'desc' );
            
            $this->addFilterField( 'id', '=', 'id' );
            $this->addFilterField( 'pessoa_id', '=', 'pessoa_id' );
            $this->addFilterField( 'processo_id', '=', 'processo_id' );
            $this->addFilterField( 'data_emissao', '=', 'data_emissao' );
            
            $this->form = new BootstrapFormBuilder( 'form_'.__CLASS__ );
            $this->form->setFormTitle( 'Recibo Prestador' );
            
            $criteria_prestador = new TCriteria();
            $filter             = new TFilter( 'liberador', '=', '2' );
            $criteria_prestador->add( $filter );
            
            $id           = new TEntry( 'id' );
            $pessoa_id    = new TDBCombo( 'pessoa_id', 'afincco', 'Pessoa', 'id', 'nome', 'nome', $criteria_prestador );
            $processo_id  = new TEntry( 'processo_id' );
            $data_emissao = new TDate( 'data_emissao' );
            
            $data_emissao->setMask( 'dd/mm/yyyy' );
            $data_emissao->setDatabaseMask( 'yyyy-mm-dd' );
            
            $campo_codigo       = [ new TLabel( 'Código' ), $id ];
            $campo_prestador    = [ new TLabel( 'Prestador' ), $pessoa_id ];
            $campo_processo     = [ new TLabel( 'Processo' ), $processo_id ];
            $campo_data_emissao = [ new TLabel( 'Data Emissão' ), $data_emissao ];
            
            $row         = $this->form->addFields( $campo_codigo, $campo_data_emissao, $campo_processo, $campo_prestador );
            $row->layout = [ 'col-sm-1', 'col-sm-2', 'col-sm-1', 'col-sm-6' ];
            
            $this->form->setData( TSession::getValue( 'Recibos_filter_data' ) );
            
            $this->form->addAction( _t( 'Find' ), new TAction( [ $this, 'onSearch' ] ), 'fa:search blue' );
            $this->form->addActionlink( _t( 'New' ), new TAction( [ 'recibosForm', 'onClear' ], [ 'register_state' => 'false' ] ), 'fa:eraser red' );
            
            $this->datagrid = new BootstrapDatagridWrapper( new TDataGrid );
            
            $column_id           = new TDataGridColumn( 'id', 'Código', 'center', '5%' );
            $column_pessoa_id    = new TDataGridColumn( 'pessoa->nome', 'Prestador', 'left' );
            $column_processo_id  = new TDataGridColumn( 'processo_id', 'Processo', 'center', '10%' );
            $column_data_emissao = new TDataGridColumn( 'data_emissao', 'Data Emissão', 'center', '10%' );
            $column_valor        = new TDataGridColumn( 'valor_recibo', 'Valor', 'right', '15%' );
            $column_status       = new TDataGridColumn( 'status', 'Status', 'center', '10%' );
            
            $this->datagrid->addColumn( $column_id );
            $this->datagrid->addColumn( $column_pessoa_id );
            $this->datagrid->addColumn( $column_processo_id );
            $this->datagrid->addColumn( $column_data_emissao );
            $this->datagrid->addColumn( $column_valor );
            $this->datagrid->addColumn( $column_status );
            
            $column_data_emissao->setTransformer( function( $value, $object, $row ) {
                if ( $value ) {
                    return TDate::date2br( $value );
                }
                
                return $value;
            } );
            
            $column_valor->setTransformer( function( $value, $object, $row ) {
                if ( is_numeric( $value ) ) {
                    return 'R$ '.number_format( $value, 2, ',', '.' );
                }
                
                return $value;
            } );
            
            $column_status->setTransformer( function( $value, $object, $row ) {
                if ( file_exists( $value ) ) {
                    return new TImage( $value );
                }
            } );
            
            $action_edit = new TDataGridAction( [ 'recibosForm', 'onEdit' ], [ 'key' => '{id}', 'register_state' => 'false' ] );
            $action_del  = new TDataGridAction( [ $this, 'onDelete' ], [ 'key' => '{id}', 'register_state' => 'false' ] );
            
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
            parent::add( $container );
        }
        
        public function onReler( $param )
        {
            $this->onReload();
        }
        
        public function onReload( $param = NULL )
        {
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
                
                $criteria = isset( $this->criteria ) ? clone $this->criteria : new TCriteria;
                
                if ( $this->order ) {
                    $criteria->setProperty( 'order', $this->order );
                    $criteria->setProperty( 'direction', $this->direction );
                }
                
                $criteria->setProperties( $param );
                $criteria->setProperty( 'limit', $limit );
                
                if ( $this->formFilters ) {
                    foreach ( $this->formFilters as $filterKey => $filterField ) {
                        if ( TSession::getValue( $this->activeRecord.'_filter_'.$filterField ) ) {
                            $criteria->add( TSession::getValue( $this->activeRecord.'_filter_'.$filterField ) );
                        }
                    }
                }
                
                $query  = "(select numero from titulo where numero = recibos.id and tipolancamento_id = 91)";
                $filter = new TFilter( 'id', 'NOT IN', ( $query ) );
                $criteria->add( $filter );
                
                if ( in_array( '5', TSession::getValue( 'usergroupids' ) ) ) {
                    $filter = new TFilter( 'pessoa_id', '=', TSession::getValue( 'LIBERADOR' ) );
                    $criteria->add( $filter );
                }
                
                $objects = $repository->load( $criteria, FALSE );
                
                if ( is_callable( $this->transformCallback ) ) {
                    call_user_func( $this->transformCallback, $objects, $param );
                }
                
                $this->datagrid->clear();
                if ( $objects ) {
                    foreach ( $objects as $object ) {
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
                
                TTransaction::close();
                $this->loaded = TRUE;
            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();
            }
        }
        
        public function displayColumn( $object )
        {
            if ( $object->onVerificaTitulo() ) {
                return FALSE;
            }
            return TRUE;
        }
        
        
    }
