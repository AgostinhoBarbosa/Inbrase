<?php
    
    use Adianti\Base\TStandardFormList;
    use Adianti\Widget\Datagrid\TDataGrid;
    use Adianti\Wrapper\BootstrapDatagridWrapper;
    
    class tipolancamentoFormList extends TStandardFormList
{
    public function __construct()
    {
        parent::__construct();

        $this->setDatabase('afincco');
        $this->setActiveRecord('TipoLancamento');
        $this->setDefaultOrder('nome', 'asc');

        $this->form = new BootstrapFormBuilder( 'form_'.__CLASS__ );
        $this->form->setFormTitle( 'Tipos de Lançamento' );

        $id   = new TEntry('id');
        $nome = new TEntry('nome');

        $id->setEditable(FALSE);

        $id->style     = ('text-align:center;color:#ff0000;font-weight:bold;');

        $nome->addValidation('Descrição',new TRequiredValidator);

        // add the fields
        $campo_id   = array(new TLabel('Código:'),    $id);
        $campo_nome = array($lblNome = new TLabel("Descrição:"), $nome);

        $row = $this->form->addFields($campo_id, $campo_nome);
        $row->layout = ['col-sm-1', 'col-sm-3'];

        $this->form->setData( TSession::getValue( 'TipoLancamento_filter_data' ) );

        $this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'far:save green fa-lg');
        $this->form->addAction(_t('New'),  new TAction(array($this, 'onEdit')), 'fa:eraser blue fa-lg');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid());

        $column_id   = new TDataGridColumn('id', 'Código', 'center', '5%');
        $column_nome = new TDataGridColumn('nome', 'Descrição', 'left', '95%');

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_nome);

        $action_edit = new TDataGridAction( [$this, 'onEdit'], ['key' => '{id}', 'register_state' => 'false'] );
        $action_del  = new TDataGridAction( [$this, 'onDelete'], ['key' => '{id}', 'register_state' => 'false'] );

        $this->datagrid->addAction( $action_edit, _t( 'Edit' ), 'far:edit blue fa-lg' );
        $this->datagrid->addAction( $action_del, _t( 'Delete' ), 'far:trash-alt red fa-lg' );

        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation();
        $this->pageNavigation->setAction( new TAction( [ $this, 'onReload' ] ) );
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
}
