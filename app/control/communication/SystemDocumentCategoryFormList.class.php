<?php
    
    use Adianti\Base\TStandardFormList;
    use Adianti\Wrapper\BootstrapFormBuilder;
    
    /**
     * SystemDocumentCategoryFormList
     *
     * @version    1.0
     * @package    control
     * @subpackage communication
     * @author     Pablo Dall'Oglio
     * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
     * @license    http://www.adianti.com.br/framework-license
     */
    class SystemDocumentCategoryFormList extends TStandardFormList
    {
        public function __construct()
        {
            parent::__construct();

            $this->setDatabase( 'communication' );
            $this->setActiveRecord( 'SystemDocumentCategory' );
            $this->setDefaultOrder( 'id', 'asc' );

            $this->form = new BootstrapFormBuilder( 'form_SystemDocumentCategory' );
            $this->form->setFieldSizes( '100%' );
            $this->form->setFormTitle( _t( 'Categories' ) );

            // create the form fields
            $id   = new TEntry( 'id' );
            $name = new TEntry( 'name' );

            $id->setEditable( FALSE );

            $campo_id   = [new TLabel( 'Código' ), $id];
            $campo_nome = [new TLabel( 'Nome' ), $name];

            // add the fields
            $this->form->addFields( $campo_id, $campo_nome );

            $this->form->addAction( _t( 'Save' ), new TAction( [$this, 'onSave'] ), 'far:save blue' );
            $this->form->addActionLink( _t( 'Clear form' ), new TAction( [$this, 'onClear'] ), 'fa:eraser red' );

            $this->datagrid        = new BootstrapDatagridWrapper( new TDataGrid() );
            $this->datagrid->style = 'width: 100%';

            $column_id   = new TDataGridColumn( 'id', 'Código', 'center', '5%' );
            $column_name = new TDataGridColumn( 'name', 'Nome', 'left', '95%' );

            $this->datagrid->addColumn( $column_id );
            $this->datagrid->addColumn( $column_name );

            $action_edit = new TDataGridAction( [$this, 'onEdit'], ['key' => '{id}', 'register_state' => 'false'] );
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
