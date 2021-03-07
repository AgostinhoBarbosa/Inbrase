<?php

    /**
     * SystemSqlLogList
     *
     * @version    1.0
     * @package    control
     * @subpackage log
     * @author     Pablo Dall'Oglio
     * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
     * @license    http://www.adianti.com.br/framework-license
     */
    class SystemSqlLogList extends TStandardList
    {
        public function __construct()
        {
            parent::__construct();

            parent::setDatabase( 'log' );
            parent::setActiveRecord( 'SystemSqlLog' );
            parent::setDefaultOrder( 'id', 'asc' );
            parent::addFilterField( 'login', 'like' );
            parent::addFilterField( 'database_name', 'like' );
            parent::addFilterField( 'sql_command', 'like' );
            parent::addFilterField( 'class_name', 'like' );
            parent::addFilterField( 'session_id', 'like' );
            parent::addFilterField( 'request_id', '=' );
            parent::setLimit( 20 );

            $this->form = new BootstrapFormBuilder( 'form_search_SystemSqlLog' );
            $this->form->setFieldSizes( '100%' );
            $this->form->setFormTitle( 'SQL Log' );

            $login      = new TEntry( 'login' );
            $database   = new TEntry( 'database_name' );
            $sql        = new TEntry( 'sql_command' );
            $class_name = new TEntry( 'class_name' );
            $session_id = new TEntry( 'session_id' );
            $request_id = new TEntry( 'request_id' );

            $campo_login    = [new TLabel( _t( 'Login' ) ), $login];
            $campo_programa = [new TLabel( _t( 'Program' ) ), $class_name];
            $campo_banco    = [new TLabel( _t( 'Database' ) ), $database];
            $campo_sessao   = [new TLabel( _t( 'Session' ) ), $session_id];
            $campo_sql      = [new TLabel( 'SQL' ), $sql];
            $campo_request  = [new TLabel( _t( 'Request' ) ), $request_id];

            $this->form->addFields( $campo_login, $campo_programa )->layout = ['col-sm-6', 'col-sm-6'];
            $this->form->addFields( $campo_banco, $campo_sessao )->layout   = ['col-sm-6', 'col-sm-6'];
            $this->form->addFields( $campo_sql, $campo_request )->layout    = ['col-sm-6', 'col-sm-6'];

            $this->form->setData( TSession::getValue( 'SystemSqlLog_filter_data' ) );

            $this->form->addAction( _t( 'Find' ), new TAction( [$this, 'onSearch'] ), 'fa:search blue' );

            $this->datagrid = new BootstrapDatagridWrapper( new TQuickGrid() );
            $this->datagrid->disableDefaultClick();
            $this->datagrid->style = 'width: 100%';
            $this->datagrid->setGroupColumn( 'transaction_id', 'Transaction: <b>{transaction_id}</b>' );
            $this->datagrid->enablePopover( _t( 'Execution trace' ), '{log_trace_formatted}' );

            $id         = $this->datagrid->addQuickColumn( 'Código', 'id', 'center', 50, new TAction( [$this,
                                                                                                       'onReload'] ), ['order',
                                                                                                                       'id'] );
            $logdate    = $this->datagrid->addQuickColumn( _t( 'Date' ), 'logdate', 'center', NULL, new TAction( [$this,
                                                                                                                  'onReload'] ), ['order',
                                                                                                                                  'logdate'] );
            $login      = $this->datagrid->addQuickColumn( _t( 'Login' ), 'login', 'center', NULL, new TAction( [$this,
                                                                                                                 'onReload'] ), ['order',
                                                                                                                                 'login'] );
            $database   = $this->datagrid->addQuickColumn( _t( 'Database' ), 'database_name', 'center', NULL, new TAction( [$this,
                                                                                                                            'onReload'] ), ['order',
                                                                                                                                            'database_name'] );
            $sql        = $this->datagrid->addQuickColumn( 'SQL', 'sql_command', 'left', NULL );
            $class_name = $this->datagrid->addQuickColumn( _t( 'Program' ), 'class_name', 'center' );
            $php_sapi   = $this->datagrid->addQuickColumn( 'SAPI', 'php_sapi', 'center' );
            $access_ip  = $this->datagrid->addQuickColumn( 'IP', 'access_ip', 'center' );

            $sql->setTransformer( function ( $sql_string )
            {
                $original_sql = $sql_string;
                $m            = [];
                preg_match_all( "/'([^']+)'/", $sql_string, $matches );

                if ( count( $matches[ 0 ] ) > 0 ) {
                    foreach ( $matches[ 0 ] as $found_string ) {
                        $sql_string = str_replace( $found_string, '<b class="orange">' . $found_string . '</b>', $sql_string );
                    }
                }

                $sql_string = str_replace( 'INSERT INTO ', '<b class="blue">INSERT INTO </b>', $sql_string );
                $sql_string = str_replace( 'DELETE FROM ', '<b class="blue">DELETE FROM </b>', $sql_string );
                $sql_string = str_replace( 'UPDATE ', '<b class="blue">UPDATE </b>', $sql_string );
                $sql_string = str_replace( ' FROM ', '<b class="blue"> FROM </b>', $sql_string );
                $sql_string = str_replace( ' WHERE ', '<b class="blue"> WHERE </b>', $sql_string );
                $sql_string = str_replace( ' SET ', '<b class="blue"> SET </b>', $sql_string );
                $sql_string = str_replace( ' VALUES ', '<b class="blue"> VALUES </b>', $sql_string );

                $div        = new TElement( 'span' );
                $div->style = "text-shadow:none; font-size:12px";
                if ( substr( $original_sql, 0, 11 ) == 'INSERT INTO' ) {
                    $div->class = "label label-success";
                    $div->add( 'INSERT' );
                } else {
                    if ( substr( $original_sql, 0, 11 ) == 'DELETE FROM' ) {
                        $div->class = "label label-danger";
                        $div->add( 'DELETE' );
                    }
                }
                if ( substr( $original_sql, 0, 6 ) == 'UPDATE' ) {
                    $div->class = "label label-info";
                    $div->add( 'UPDATE' );
                }

                return $div . $sql_string;
            } );

            $this->datagrid->createModel();

            $this->pageNavigation = new TPageNavigation();
            $this->pageNavigation->setAction( new TAction( [$this, 'onReload'] ) );
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

        /**
         *
         */
        public function filterRequest( $param )
        {
            parent::clearFilters();

            $data             = new stdClass();
            $data->request_id = $param[ 'request_id' ];
            $this->form->setData( $data );

            $criteria = new TCriteria();
            $criteria->add( new TFilter( 'request_id', '=', $param[ 'request_id' ] ) );
            parent::setCriteria( $criteria );

            $this->onReload( $param );
        }
    }
