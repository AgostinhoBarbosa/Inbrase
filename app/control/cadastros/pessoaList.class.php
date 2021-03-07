<?php
    
    use Adianti\Base\TStandardList;
    use Adianti\Wrapper\BootstrapFormBuilder;
    
    class pessoaList extends TStandardList
    {
        
        public function __construct()
        {
            parent::__construct();
            parent::setDatabase( 'afincco' );
            parent::setActiveRecord( 'Pessoa' );
            parent::setDefaultOrder( 'id', 'asc' );
            
            parent::setFilterField( 'nome' );
            parent::setFilterField( 'documento' );
            parent::setFilterField( 'apelido' );
            
            $this->form = new BootstrapFormBuilder( 'form_'.__CLASS__ );
            $this->form->setFormTitle( 'Clientes/Fornecedores' );
            
            $documento = new TEntry( 'documento' );
            $nome      = new TEntry( 'nome' );
            $apelido   = new TEntry( 'apelido' );
            
            $campo_documento = [ new TLabel( 'Documento' ), $documento ];
            $campo_nome      = [ new TLabel( 'Nome' ), $nome ];
            $campo_apelido   = [ new TLabel( 'Nome Fantasia' ), $apelido ];
            
            $this->form->addFields( $campo_documento, $campo_nome, $campo_apelido )->layout = [ 'col-sm-2', 'col-sm-4',
                                                                                                'col-sm-4' ];
            
            $this->form->setData( TSession::getValue( 'Pessoa_filter_data' ) );
            
            $this->form->addAction( _t( 'Find' ), new TAction( [ $this, 'onSearch' ] ), 'fa:search blue' );
            $this->form->addActionlink( _t( 'New' ), new TAction( [ 'pessoaForm', 'onClear' ], [ 'register_state' => 'false' ] ), 'fa:eraser red' );
            
            $this->datagrid        = new BootstrapDatagridWrapper( new TQuickGrid() );
            $this->datagrid->style = 'width: 100%';
            
            $column_id        = $this->datagrid->addQuickColumn( 'Código', 'id', 'center', '5%' );
            $column_documento = $this->datagrid->addQuickColumn( 'Documento', 'documento', 'center', '15%' );
            $column_nome      = $this->datagrid->addQuickColumn( 'Nome', 'nome', 'left', '50%' );
            $column_apelido   = $this->datagrid->addQuickColumn( 'Apelido', 'apelido', 'left', '20%' );
            $column_fone      = $this->datagrid->addQuickColumn( 'telefone', 'telefone', 'left', '10%' );
            
            $column_id->setTransformer( function( $value, $object, $row ) {
                return "<b style='color:#ff0000'>".$value."</b>";
            } );
            
            $column_fone->setTransformer( function( $value, $object, $row ) {
                $retorno = "";
                foreach ( $object->telefones as $telefone ) {
                    $retorno = $telefone->fone_numero;
                }
                
                return $retorno;
            } );
            
            $action_edit = new TDataGridAction( [ 'pessoaForm', 'onEdit' ], [ 'key' => '{id}', 'register_state' => 'false' ] );
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
