<?php
    
    use Adianti\Base\TStandardList;
    use Adianti\Wrapper\BootstrapFormBuilder;
    
    class textosList extends TStandardList
    {
        public function __construct()
        {
            parent::__construct();
            parent::setDatabase( 'afincco' );
            parent::setActiveRecord( 'Textos' );
            parent::setDefaultOrder( 'id', 'asc' );
            parent::setFilterField( 'nome', 'like', 'nome' );
            
            $this->form = new BootstrapFormBuilder( 'form_'.__CLASS__ );
            $this->form->setFormTitle( 'Textos' );
            
            $nome = new TEntry( 'nome' );
            
            $this->form->addFields( [ 'Nome', $nome ] )->layout = [ 'col-sm-6' ];
            
            $this->form->setData( TSession::getValue( 'textos_filter_data' ) );
            
            $this->form->addAction( _t( 'Find' ), new TAction( [ $this, 'onSearch' ] ), 'fa:search blue' );
            $this->form->addActionlink( _t( 'New' ), new TAction( [ 'textosForm', 'onClear' ], [ 'register_state' => 'false' ] ), 'fa:eraser red' );
            
            $this->datagrid        = new BootstrapDatagridWrapper( new TQuickGrid() );
            $this->datagrid->style = 'width: 100%';
            
            $column_id   = new TDataGridColumn( 'id', 'Código', 'center', '10%' );
            $column_nome = new TDataGridColumn( 'nome', 'Nome', 'left' );
            
            $this->datagrid->addColumn( $column_id );
            $this->datagrid->addColumn( $column_nome );
            
            $order_id = new TAction( [ $this, 'onReload' ] );
            $order_id->setParameter( 'order', 'id' );
            $column_id->setAction( $order_id );
            
            $order_nome = new TAction( [ $this, 'onReload' ] );
            $order_nome->setParameter( 'order', 'nome' );
            $column_nome->setAction( $order_nome );
            
            $action_edit = new TDataGridAction( [ 'textosForm', 'onEdit' ], [ 'key' => '{id}', 'register_state' => 'false' ] );
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
