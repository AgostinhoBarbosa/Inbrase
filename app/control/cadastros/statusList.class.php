<?php
    
    use Adianti\Base\TStandardList;
    use Adianti\Widget\Form\TLabel;
    
    class statusList extends TStandardList
    {
        protected $form;
        protected $datagrid;
        protected $pageNavigation;
        
        public function __construct()
        {
            parent::__construct();
            
            parent::setDatabase( 'afincco' );
            parent::setActiveRecord( 'Status' );
            parent::setDefaultOrder( 'id', 'asc' );
            
            parent::setFilterField( 'statu' );
            
            $this->form = new BootstrapFormBuilder( 'form_'.__CLASS__ );
            $this->form->setFieldSizes( '100%' );
            $this->form->setFormTitle( 'Status dos Processos' );
            
            $id    = new TEntry( 'id' );
            $statu = new TEntry( 'statu' );
            
            $campo_id   = [ new TLabel( 'Código' ), $id ];
            $campo_nome = [ new TLabel( 'Status' ), $statu ];
            
            $row         = $this->form->addFields( $campo_id, $campo_nome );
            $row->layout = [ 'col-sm-1', 'col-sm-3' ];
            
            $this->form->setData( TSession::getValue( 'status_filter_data' ) );
            
            $this->form->addAction( _t( 'Find' ), new TAction( [ $this, 'onSearch' ] ), 'fa:search blue' );
            $this->form->addActionlink( _t( 'New' ), new TAction( [ 'statusForm', 'onClear' ], [ 'register_state' => 'false' ] ), 'fa:eraser red' );
            
            $this->datagrid        = new BootstrapDatagridWrapper( new TQuickGrid );
            $this->datagrid->style = 'width: 100%';
            
            $column_id    = new TDataGridColumn( 'id', 'Código', 'center', '5%' );
            $column_statu = new TDataGridColumn( 'statu', 'Status', 'left', '75%' );
            $column_prazo = new TDataGridColumn( 'prazo', 'Prazo em Dias', 'center', '10%' );
            $column_final = new TDataGridColumn( 'status_final', 'Status Final', 'center', '10%' );
    
            $column_final->setTransformer( function( $value, $object, $row ) {
                if ( $value == "0" ) {
                    return "<b style='color:#ff0000;margin: 0 0 0 0;'>Não</b>";
                } else {
                    return "<b style='color:#00008B;margin: 0 0 0 0;'>Sim</b>";
                }
        
            } );
            
            $this->datagrid->addColumn( $column_id );
            $this->datagrid->addColumn( $column_statu );
            $this->datagrid->addColumn( $column_prazo );
            $this->datagrid->addColumn( $column_final );
            
            $order_id = new TAction( [ $this, 'onReload' ] );
            $order_id->setParameter( 'order', 'id' );
            $column_id->setAction( $order_id );
            
            $order_statu = new TAction( [ $this, 'onReload' ] );
            $order_statu->setParameter( 'order', 'statu' );
            $column_statu->setAction( $order_statu );
            
            $action_edit = new TDataGridAction( [ 'statusForm', 'onEdit' ], [ 'key' => '{id}', 'register_state' => 'false' ] );
            $action_del  = new TDataGridAction( [ $this, 'onDelete' ], [ 'key' => '{id}', 'register_state' => 'false' ] );
            
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
        
    }
