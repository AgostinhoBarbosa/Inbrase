<?php

    /**
     * SystemRequestLogList
     *
     * @version    1.0
     * @package    control
     * @subpackage log
     * @author     Pablo Dall'Oglio
     * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
     * @license    http://www.adianti.com.br/framework-license
     */
    class SystemRequestLogList extends TStandardList
    {

        public function __construct()
        {
            parent::__construct();

            parent::setDatabase( 'log' );
            parent::setActiveRecord( 'SystemRequestLog' );
            parent::setDefaultOrder( 'id', 'desc' );
            parent::addFilterField( 'login', 'like' );
            parent::addFilterField( 'class_name', 'like' );
            parent::addFilterField( 'session_id', 'like' );
            parent::addFilterField( 'endpoint', '=' );
            parent::setLimit( 20 );

            $this->form = new BootstrapFormBuilder( 'form_search_SystemRequestLog' );
            $this->form->setFieldSizes( '100%' );
            $this->form->setFormTitle( _t( 'Request Log' ) );

            $login      = new TEntry( 'login' );
            $class_name = new TEntry( 'class_name' );
            $session_id = new TEntry( 'session_id' );
            $endpoint   = new TCombo( 'endpoint' );

            $endpoint->addItems( ['cli' => 'CLI', 'rest' => 'REST', 'web' => 'WEB'] );

            $campo_login    = [new TLabel( _t( 'Login' ) ), $login];
            $campo_programa = [new TLabel( _t( 'Program' ) ), $class_name];
            $campo_endpoint = [new TLabel( 'Endpoint' ), $endpoint];
            $campo_sessao   = [new TLabel( _t( 'Session' ) ), $session_id];

            $this->form->addFields( $campo_login, $campo_programa )->layout  = ['col-sm-6', 'col-sm-6'];
            $this->form->addFields( $campo_sessao, $campo_endpoint )->layout = ['col-sm-6', 'col-sm-6'];

            $this->form->setData( TSession::getValue( 'SystemRequestLog_filter_data' ) );

            $this->form->addAction( _t( 'Find' ), new TAction( [$this, 'onSearch'] ), 'fa:search blue' );

            $this->datagrid            = new BootstrapDatagridWrapper( new TQuickGrid() );
            $this->datagrid->style     = 'width: 100%';
            $this->datagrid->datatable = 'true';
            $this->datagrid->setHeight( 320 );

            $id         = $this->datagrid->addQuickColumn( 'Código', 'id', 'center' );
            $logdate    = $this->datagrid->addQuickColumn( _t( 'Time' ), 'logdate', 'center' );
            $sessionid  = $this->datagrid->addQuickColumn( 'Sessão', 'session_id', 'left' );
            $login      = $this->datagrid->addQuickColumn( _t( 'Login' ), 'login', 'center' );
            $access_ip  = $this->datagrid->addQuickColumn( 'IP', 'access_ip', 'center' );
            $class_name = $this->datagrid->addQuickColumn( _t( 'Program' ), 'class_name', 'center' );
            $endpoint   = $this->datagrid->addQuickColumn( 'Endpoint', 'endpoint', 'center' );
            $req_method = $this->datagrid->addQuickColumn( _t( 'Method' ), 'request_method', 'center' );

            $action1 = new TDataGridAction( ['SystemRequestLogView', 'onLoad'], ['id' => '{id}', 'register_state' => 'false'] );
            $action2 = new TDataGridAction( ['SystemSqlLogList', 'filterRequest'], ['request_id' => '{id}', 'register_state' => 'false'] );

            $this->datagrid->addAction( $action1, 'View', 'fa:search blue fa-lg' );
            $this->datagrid->addAction( $action2, 'SQL', 'fa:database orange fa-lg' );

            $endpoint->setTransformer( function ( $value )
            {
                return strtoupper( $value );
            } );

            $order_id = new TAction( [$this, 'onReload'] );
            $order_id->setParameter( 'order', 'id' );
            $id->setAction( $order_id );

            $this->datagrid->createModel();

            $this->pageNavigation = new TPageNavigation();
            $this->pageNavigation->setAction( new TAction( [$this, 'onReload'] ) );
            $this->pageNavigation->setWidth( $this->datagrid->getWidth() );

            $panel = new TPanelGroup();
            $panel->add( $this->datagrid );
            $panel->addFooter( $this->pageNavigation );

            $container        = new TVBox();
            $container->style = 'width: 100%';
            $container->add( $this->form );
            $container->add( $panel );

            parent::add( $container );
        }

        /**
         *
         */
        public function filterSession( $param )
        {
            parent::clearFilters();

            $data             = new stdClass();
            $data->session_id = $param[ 'session_id' ];
            $this->form->setData( $data );

            $criteria = new TCriteria();
            $criteria->add( new TFilter( 'session_id', 'like', $param[ 'session_id' ] ) );
            parent::setCriteria( $criteria );

            $this->onReload( $param );
        }
    }
