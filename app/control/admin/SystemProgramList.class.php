<?php
    
    use Adianti\Widget\Datagrid\TPageNavigation;
    use Adianti\Wrapper\BootstrapDatagridWrapper;
    use Adianti\Wrapper\BootstrapFormBuilder;
    
    /**
     * SystemProgramList
     *
     * @version    1.0
     * @package    control
     * @subpackage admin
     * @author     Pablo Dall'Oglio
     * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
     * @license    http://www.adianti.com.br/framework-license
     */
    class SystemProgramList extends TStandardList
    {
        public function __construct()
        {
            parent::__construct();

            parent::setDatabase( 'permission' );
            parent::setActiveRecord( 'SystemProgram' );
            parent::setDefaultOrder( 'id', 'asc' );

            parent::addFilterField( 'id', '=', 'id' );
            parent::addFilterField( 'name', 'like', 'name' );
            parent::addFilterField( 'controller', 'like', 'controller' );

            // creates the form
            $this->form = new BootstrapFormBuilder( 'form_SystemProgramList' );
            $this->form->setFormTitle( _t( 'Programs' ) );

            $name       = new TEntry( 'name' );
            $controller = new TEntry( 'controller' );

            $campo_nome       = [new TLabel( 'Nome' ), $name];
            $campo_controller = [new TLabel( 'Classe de Controle' ), $controller];

            $this->form->addFields( $campo_nome, $campo_controller )->layout = ['col-sm-3', 'col-sm-3'];

            $this->form->setData( TSession::getValue( 'SystemProgram_filter_data' ) );

            $this->form->addAction( _t( 'Find' ), new TAction( array($this, 'onSearch') ), 'fa:search blue' );
            $this->form->addActionlink( _t( 'New' ), new TAction( ['SystemProgramForm', 'onClear'], ['register_state' => 'false'] ), 'fa:eraser red' );

            $this->datagrid        = new BootstrapDatagridWrapper( new TDataGrid() );

            $column_id         = new TDataGridColumn( 'id', 'Código', 'center', '5%' );
            $column_controller = new TDataGridColumn( 'controller', _t( 'Controller' ), 'left', '30%' );
            $column_name       = new TDataGridColumn( 'name', _t( 'Name' ), 'left', '30%' );
            $column_menu       = new TDataGridColumn( 'controller', _t( 'Menu path' ), 'left', '35%' );

            //$column_name->enableAutoHide( 500 );
            //$column_menu->enableAutoHide( 500 );

            $column_menu->setTransformer( function ( $value, $object, $row )
            {
                $menuparser = new TMenuParser( 'menu.xml' );
                $paths      = $menuparser->getPath( $value );

                if ( $paths ) {
                    return implode( ' &raquo; ', $paths );
                }
            } );

            // add the columns to the DataGrid
            $this->datagrid->addColumn( $column_id );
            $this->datagrid->addColumn( $column_controller );
            $this->datagrid->addColumn( $column_name );
            $this->datagrid->addColumn( $column_menu );


            // creates the datagrid column actions
            $order_id = new TAction( array($this, 'onReload') );
            $order_id->setParameter( 'order', 'id' );
            $column_id->setAction( $order_id );

            $order_name = new TAction( array($this, 'onReload') );
            $order_name->setParameter( 'order', 'name' );
            $column_name->setAction( $order_name );

            $order_controller = new TAction( array($this, 'onReload') );
            $order_controller->setParameter( 'order', 'controller' );
            $column_controller->setAction( $order_controller );

            $action_edit = new TDataGridAction( ['SystemProgramForm', 'onEdit'], ['key' => '{id}', 'register_state' => 'false'] );
            $action_del  = new TDataGridAction( [$this, 'onDelete'], ['key' => '{id}', 'register_state' => 'false'] );
            $action_ope  = new TDataGridAction( [$this, 'onOpen'], ['key' => '{controller}', 'register_state' => 'false'] );

            $this->datagrid->addAction( $action_edit, _t( 'Edit' ), 'far:edit blue fa-lg' );
            $this->datagrid->addAction( $action_del, _t( 'Delete' ), 'far:trash-alt red fa-lg' );
            $this->datagrid->addAction( $action_ope, _t( 'Open' ), 'far:folder-open green fa-lg' );


            // create the datagrid model
            $this->datagrid->createModel();

            // create the page navigation
            $this->pageNavigation = new TPageNavigation();
            $this->pageNavigation->setAction( new TAction( array($this, 'onReload') ) );
            $this->pageNavigation->setWidth( $this->datagrid->getWidth() );

            $panel                                = new TPanelGroup();
            $panel->add( $this->datagrid )->style = 'overflow-x:auto';
            $panel->addFooter( $this->pageNavigation );

            // header actions
            $dropdown = new TDropDown( _t( 'Export' ), 'fa:list' );
            $dropdown->setPullSide( 'right' );
            $dropdown->setButtonClass( 'btn btn-default waves-effect dropdown-toggle' );
            $dropdown->addAction( _t( 'Save as CSV' ), new TAction( [$this,
                                                                     'onExportCSV'], ['register_state' => 'false',
                                                                                      'static'         => '1'] ), 'fa:table fa-fw blue' );
            $dropdown->addAction( _t( 'Save as PDF' ), new TAction( [$this,
                                                                     'onExportPDF'], ['register_state' => 'false',
                                                                                      'static'         => '1'] ), 'far:file-pdf fa-fw red' );
            $dropdown->addAction( _t( 'Save as XML' ), new TAction( [$this,
                                                                     'onExportXML'], ['register_state' => 'false',
                                                                                      'static'         => '1'] ), 'fa:code fa-fw green' );
            $panel->addHeaderWidget( $dropdown );

            $container        = new TVBox();
            $container->style = 'width: 100%';
            $container->add( $this->form );
            $container->add( $panel );

            parent::add( $container );
        }

        /**
         * Open controller
         */
        public function onOpen( $param )
        {
            AdiantiCoreApplication::loadPage( $param[ 'controller' ] );
        }

        /**
         * Display condition
         */
        public function displayBuilderActions( $object )
        {
            return ( ( strpos( $object->controller, 'System' ) === FALSE ) and ! in_array( $object->controller, ['CommonPage',
                                                                                                                 'WelcomeView'] ) );
        }
    }
