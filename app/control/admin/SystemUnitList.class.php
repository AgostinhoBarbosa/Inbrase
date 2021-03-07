<?php

    /**
     * SystemUnitList
     *
     * @version    1.0
     * @package    control
     * @subpackage admin
     * @author     Pablo Dall'Oglio
     * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
     * @license    http://www.adianti.com.br/framework-license
     */
    class SystemUnitList extends TStandardList
    {
        public function __construct()
        {
            parent::__construct();

            parent::setDatabase( 'permission' );
            parent::setActiveRecord( 'SystemUnit' );
            parent::setDefaultOrder( 'id', 'asc' );
            parent::addFilterField( 'id', '=', 'id' );
            parent::addFilterField( 'name', 'like', 'name' );

            $this->form = new BootstrapFormBuilder( 'form_search_SystemUnit' );
            $this->form->setFieldSizes( '100%' );
            $this->form->setFormTitle( _t( 'Units' ) );

            $id   = new TEntry( 'id' );
            $name = new TEntry( 'name' );

            $campo_codigo = [new TLabel( 'Código' ), $id];
            $campo_nome   = [new TLabel( _t( 'Name' ) ), $name];

            $this->form->addFields( $campo_codigo, $campo_nome );

            $this->form->setData( TSession::getValue( 'SystemUnit_filter_data' ) );

            $this->form->addAction( _t( 'Find' ), new TAction( array($this, 'onSearch') ), 'fa:search blue' );
            $this->form->addActionlink( _t( 'New' ), new TAction( ['SystemUnitForm', 'onEdit'], ['register_state' => 'false'] ), 'fa:eraser red' );

            $this->datagrid            = new BootstrapDatagridWrapper( new TDataGrid() );
            $this->datagrid->datatable = 'true';
            $this->datagrid->style     = 'width: 100%';
            $this->datagrid->setHeight( 320 );

            $column_id   = new TDataGridColumn( 'id', 'Código', 'center', '5%' );
            $column_name = new TDataGridColumn( 'name', _t( 'Name' ), 'left', '95%' );

            $this->datagrid->addColumn( $column_id );
            $this->datagrid->addColumn( $column_name );

            $order_id = new TAction( [$this, 'onReload'] );
            $order_id->setParameter( 'order', 'id' );
            $column_id->setAction( $order_id );

            $order_name = new TAction( [$this, 'onReload'] );
            $order_name->setParameter( 'order', 'name' );
            $column_name->setAction( $order_name );

            $action_edit = new TDataGridAction( ['SystemUnitForm', 'onEdit'], ['key' => '{id}', 'register_state' => 'false'] );
            $action_del  = new TDataGridAction( [$this, 'onDelete'], ['key' => '{id}', 'register_state' => 'false'] );

            $this->datagrid->addAction( $action_edit, _t( 'Edit' ), 'far:edit blue fa-lg' );
            $this->datagrid->addAction( $action_del, _t( 'Delete' ), 'far:trash-alt red fa-lg' );

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
    }
