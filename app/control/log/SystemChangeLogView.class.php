<?php
    
    use Adianti\Widget\Datagrid\TDataGridColumn;
    
    /**
     * SystemChangeLogView
     *
     * @version    1.0
     * @package    control
     * @subpackage log
     * @author     Pablo Dall'Oglio
     * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
     * @license    http://www.adianti.com.br/framework-license
     */
    class SystemChangeLogView extends TStandardList
    {
        public function __construct()
        {
            parent::__construct();

            parent::setDatabase( 'log' );
            parent::setActiveRecord( 'SystemChangeLog' );
            parent::setDefaultOrder( 'id', 'asc' );
            parent::addFilterField( 'tablename' );
            parent::addFilterField( 'login' );
            parent::addFilterField( 'class_name', 'like' );
            parent::addFilterField( 'session_id', 'like' );
            parent::setLimit( 20 );

            $this->form = new BootstrapFormBuilder( 'form_table_logger' );
            $this->form->setFieldSizes( '100%' );
            $this->form->setFormTitle( 'Table change log' );

            $tablename  = new TEntry( 'tablename' );
            $login      = new TEntry( 'login' );
            $class_name = new TEntry( 'class_name' );
            $session_id = new TEntry( 'session_id' );

            $campo_login    = [new TLabel( _t( 'Login' ) ), $login];
            $campo_programa = [new TLabel( _t( 'Program' ) ), $class_name];
            $campo_tabela   = [new TLabel( _t( 'Table' ) ), $tablename];
            $campo_sessao   = [new TLabel( _t( 'Session' ) ), $session_id];

            $this->form->addFields( $campo_tabela, $campo_programa )->layout = ['col-sm-6', 'col-sm-6'];
            $this->form->addFields( $campo_login, $campo_sessao )->layout    = ['col-sm-6', 'col-sm-6'];

            $this->form->setData( TSession::getValue( 'SystemChangeLogView_filter_data' ) );

            $this->form->addAction( _t( 'Search' ), new TAction( [$this, 'onSearch'] ), 'fa:search blue' );

            $this->formgrid = new TForm();

            $this->datagrid        = new BootstrapDatagridWrapper( new TDataGrid() );
            $this->datagrid->style = 'width: 100%';
            $this->datagrid->setGroupColumn( 'transaction_id', 'Transaction: <b>{transaction_id}</b>' );
            $this->datagrid->enablePopover( _t( 'Execution trace' ), '{log_trace_formatted}' );

            parent::setTransformer( [$this, 'onBeforeLoad'] );

            $this->formgrid->add( $this->datagrid );

            $id         = new TDataGridColumn( 'pkvalue', 'PK', 'center' );
            $date       = new TDataGridColumn( 'logdate', _t( 'Date' ), 'center' );
            $login      = new TDataGridColumn( 'login', 'Login', 'center' );
            $name       = new TDataGridColumn( 'tablename', _t( 'Table' ), 'center' );
            $column     = new TDataGridColumn( 'columnname', _t( 'Column' ), 'center' );
            $operation  = new TDataGridColumn( 'operation', _t( 'Operation' ), 'center' );
            $oldvalue   = new TDataGridColumn( 'oldvalue', _t( 'Old value' ), 'left' );
            $newvalue   = new TDataGridColumn( 'newvalue', _t( 'New value' ), 'left' );
            $class_name = new TDataGridColumn( 'class_name', _t( 'Program' ), 'center' );
            $php_sapi   = new TDataGridColumn( 'php_sapi', 'SAPI', 'center' );
            $access_ip  = new TDataGridColumn( 'access_ip', 'IP', 'center' );

            $operation->setTransformer( function ( $value, $object, $row )
            {
                $div        = new TElement( 'span' );
                $div->style = "text-shadow:none; font-size:12px";
                if ( $value == 'created' ) {
                    $div->class = "label label-success";
                } else {
                    if ( $value == 'deleted' ) {
                        $div->class = "label label-danger";
                    } else {
                        if ( $value == 'changed' ) {
                            $div->class = "label label-info";
                        }
                    }
                }
                $div->add( $value );
                return $div;
            } );

            $order1 = new TAction( [$this, 'onReload'] );
            $order2 = new TAction( [$this, 'onReload'] );
            $order3 = new TAction( [$this, 'onReload'] );
            $order4 = new TAction( [$this, 'onReload'] );
            $order5 = new TAction( [$this, 'onReload'] );

            $order1->setParameter( 'order', 'pkvalue' );
            $order2->setParameter( 'order', 'logdate' );
            $order3->setParameter( 'order', 'login' );
            $order4->setParameter( 'order', 'tablename' );
            $order5->setParameter( 'order', 'columnname' );

            $id->setAction( $order1 );
            $date->setAction( $order2 );
            $login->setAction( $order3 );
            $name->setAction( $order4 );
            $column->setAction( $order5 );

            // adiciona as colunas à DataGrid
            $this->datagrid->addColumn( $date );
            $this->datagrid->addColumn( $login );
            $this->datagrid->addColumn( $name );
            $this->datagrid->addColumn( $id );
            $this->datagrid->addColumn( $column );
            $this->datagrid->addColumn( $operation );
            $this->datagrid->addColumn( $oldvalue );
            $this->datagrid->addColumn( $newvalue );
            $this->datagrid->addColumn( $class_name );
            $this->datagrid->addColumn( $php_sapi );
            $this->datagrid->addColumn( $access_ip );

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
    }
