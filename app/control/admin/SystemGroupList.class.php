<?php

    /**
     * SystemGroupList
     *
     * @version    1.0
     * @package    control
     * @subpackage admin
     * @author     Pablo Dall'Oglio
     * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
     * @license    http://www.adianti.com.br/framework-license
     */
    class SystemGroupList extends TStandardList
    {

        public function __construct()
        {
            parent::__construct();

            parent::setDatabase( 'permission' );
            parent::setActiveRecord( 'SystemGroup' );
            parent::setDefaultOrder( 'id', 'asc' );
            parent::addFilterField( 'id', '=', 'id' );
            parent::addFilterField( 'name', 'like', 'name' );

            $this->form = new BootstrapFormBuilder( 'form_search_SystemGroup' );
            $this->form->setFieldSizes( '100%' );
            $this->form->setFormTitle( _t( 'Groups' ) );

            $id   = new TEntry( 'id' );
            $name = new TEntry( 'name' );

            $campo_id   = [new TLabel( 'Código' ), $id];
            $campo_nome = [new TLabel( _t( 'Name' ) ), $name];

            $this->form->addFields( $campo_id, $campo_nome );

            $this->form->setData( TSession::getValue( 'SystemGroup_filter_data' ) );

            $this->form->addAction( _t( 'Find' ), new TAction( array($this, 'onSearch') ), 'fa:search blue' );
            $this->form->addActionlink( _t( 'New' ), new TAction( ['SystemGroupForm', 'onEdit'], ['register_state' => 'false'] ), 'fa:eraser red' );

            $this->datagrid        = new BootstrapDatagridWrapper( new TDataGrid() );
            $this->datagrid->style = 'width: 100%';
            $this->datagrid->setHeight( 320 );

            $column_id   = new TDataGridColumn( 'id', 'Código', 'center', 50 );
            $column_name = new TDataGridColumn( 'name', _t( 'Name' ), 'left' );

            $this->datagrid->addColumn( $column_id );
            $this->datagrid->addColumn( $column_name );

            $order_id = new TAction( [$this, 'onReload'] );
            $order_id->setParameter( 'order', 'id' );
            $column_id->setAction( $order_id );

            $order_name = new TAction( [$this, 'onReload'] );
            $order_name->setParameter( 'order', 'name' );
            $column_name->setAction( $order_name );

            $action_edit  = new TDataGridAction( ['SystemGroupForm', 'onEdit'], ['key' => '{id}', 'register_state' => 'false'] );
            $action_del   = new TDataGridAction( [$this, 'onDelete'], ['key' => '{id}', 'register_state' => 'false'] );
            $action_clone = new TDataGridAction( [$this, 'onClone'], ['key' => '{id}', 'register_state' => 'false'] );

            $this->datagrid->addAction( $action_edit, _t( 'Edit' ), 'far:edit blue fa-lg' );
            $this->datagrid->addAction( $action_del, _t( 'Delete' ), 'far:trash-alt red fa-lg' );
            $this->datagrid->addAction( $action_clone, _t( 'Clone' ), 'far:clone green fa-lg' );

            $this->datagrid->createModel();

            $this->pageNavigation = new TPageNavigation();
            $this->pageNavigation->setAction( new TAction( [$this, 'onReload'] ) );
            $this->pageNavigation->setWidth( $this->datagrid->getWidth() );

            $panel = new TPanelGroup();
            $panel->add( $this->datagrid );
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
         * Clone group
         */
        public function onClone( $param )
        {
            try {
                TTransaction::open( 'permission' );
                $group = new SystemGroup( $param[ 'id' ] );
                $group->cloneGroup();
                TTransaction::close();

                $this->onReload();
            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();
            }
        }
    }
