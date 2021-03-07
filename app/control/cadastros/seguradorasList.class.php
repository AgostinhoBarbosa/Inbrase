<?php
    
    class seguradorasList extends TStandardList
    {
        
        public function __construct()
        {
            parent::__construct();
            
            parent::setDatabase( 'afincco' );
            parent::setActiveRecord( 'Seguradoras' );
            parent::setDefaultOrder( 'id', 'asc' );
            
            parent::addFilterField( 'nome', 'like', 'nome' );
            
            $this->form = new BootstrapFormBuilder( 'form_'.__CLASS__ );
            $this->form->setFormTitle( 'Seguradoras' );
            
            $nome = new TEntry( 'nome' );
            
            $this->form->addFields( [ 'Nome', $nome ] )->layout = [ 'col-sm-6' ];
            
            $this->form->setData( TSession::getValue( 'Seguradoras_filter_data' ) );
            
            $this->form->addAction( _t( 'Find' ), new TAction( [ $this, 'onSearch' ] ), 'fa:search blue' );
            $this->form->addActionlink( _t( 'New' ), new TAction( [ 'seguradorasForm', 'onClear' ], [ 'register_state' => 'false' ] ), 'fa:eraser red' );
            
            $this->datagrid        = new BootstrapDatagridWrapper( new TQuickGrid );
            $this->datagrid->style = 'width: 100%';
            
            $column_id       = new TDataGridColumn( 'id', 'Código', 'center', '5%' );
            $column_nome     = new TDataGridColumn( 'nome', 'Nome', 'left', '40%' );
            $column_gerente  = new TDataGridColumn( 'gerente', 'Gerente', 'left', '15%' );
            $column_cidade   = new TDataGridColumn( 'cidade', 'Cidade', 'left', '15%' );
            $column_uf       = new TDataGridColumn( 'uf', 'UF', 'center', '5%' );
            $column_telefone = new TDataGridColumn( 'telefone', 'Telefone', 'center', '10%' );
            $column_email    = new TDataGridColumn( 'email', 'Email', 'left', '10%' );
            
            $this->datagrid->addColumn( $column_id );
            $this->datagrid->addColumn( $column_nome );
            $this->datagrid->addColumn( $column_gerente );
            $this->datagrid->addColumn( $column_cidade );
            $this->datagrid->addColumn( $column_uf );
            $this->datagrid->addColumn( $column_telefone );
            $this->datagrid->addColumn( $column_email );
            
            $action_edit = new TDataGridAction( [ 'seguradorasForm', 'onEdit' ], [ 'key' => '{id}', 'register_state' => 'false' ] );
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
