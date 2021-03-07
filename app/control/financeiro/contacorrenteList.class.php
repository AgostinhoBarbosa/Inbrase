<?php

    class contacorrenteList extends TStandardList {

        public function __construct() {
            parent::__construct();

            $this->setDatabase( 'afincco' );
            $this->setActiveRecord( 'Contacorrente' );
            $this->setDefaultOrder( 'id', 'asc' );

            $this->form = new BootstrapFormBuilder( 'form_'.__CLASS__ );
            $this->form->setFormTitle( 'Contas Correntes' );

            $this->form->addAction( _t( 'Find' ), new TAction( array($this, 'onSearch') ), 'fa:search blue' );
            $this->form->addActionlink( _t( 'New' ), new TAction( ['contacorrenteForm', 'onClear'], ['register_state' => 'false'] ), 'fa:eraser red' );

            $this->form->setData( TSession::getValue( 'Contacorrente_filter_data' ) );

            $this->datagrid        = new BootstrapDatagridWrapper( new TQuickGrid );

            $column_id              = new TDataGridColumn( 'id', 'Código', 'center', '5%' );
            $column_nome            = new TDataGridColumn( 'nome', 'Nome', 'left', '25%' );
            $column_agencia         = new TDataGridColumn( 'agencia', 'Agência', 'left',  '5%' );
            $column_numero          = new TDataGridColumn( 'numero', 'Numero', 'left',  '10%' );
            $column_tipo            = new TDataGridColumn( 'tipo_conta', 'Tipo', 'center',  '10%' );
            $column_debito          = new TDataGridColumn( 'debito', 'Debito', 'right', '10%' );
            $column_credito         = new TDataGridColumn( 'credito', 'Credito', 'right', '10%' );
            $column_saldo           = new TDataGridColumn( '= {credito} - {debito}', 'Saldo', 'right', '10%' );
            $column_ativo           = new TDataGridColumn( 'ativo', 'Ativo', 'center',  '5%' );
            $column_data_fechamento = new TDataGridColumn( 'data_fechamento', 'Data Fechamento', 'center',  '10%' );

            $column_debito->setTotalFunction(
                function( $values ) {
                    return array_sum( (array) $values );
                }
            );
            $column_credito->setTotalFunction(
                function( $values ) {
                    return array_sum( (array) $values );
                }
            );
            $column_saldo->setTotalFunction(
                function( $values ) {
                    return array_sum( (array) $values );
                }
            );

            $column_ativo->setTransformer( function ($value) {
                if ($value == '1')
                {
                    $div = new TElement('span');
                    $div->class="label label-success";
                    $div->style="text-shadow:none; font-size:12px";
                    $div->add('Sim');
                    return $div;
                }
                else
                {
                    $div = new TElement('span');
                    $div->class="label label-danger";
                    $div->style="text-shadow:none; font-size:12px";
                    $div->add('Não');
                    return $div;
                }
            });


            $column_data_fechamento->setTransformer(
                function( $value, $object, $row ) {
                    return "<p style='color:#FF0000'>".TDate::date2br( $value )."</p>";
                }
            );

            $column_debito->setTransformer(
                function( $value, $object, $row ) {
                    return "<p style='color:#FF0000'>".number_format( $value, 2, ',', '.' )."</p>";
                }
            );

            $column_credito->setTransformer(
                function( $value, $object, $row ) {
                    return "<p style='color:blue'>".number_format( $value, 2, ',', '.' )."</p>";
                }
            );

            $column_saldo->setTransformer(
                function( $value, $object, $row ) {
                    if ( $value >= 0 ) {
                        return "<p style='color:blue'>".number_format( $value, 2, ',', '.' )."</p>";
                    } else {
                        return "<p style='color:#FF0000'>".number_format( $value, 2, ',', '.' )."</p>";
                    }
                }
            );

            $this->datagrid->addColumn( $column_id );
            $this->datagrid->addColumn( $column_nome );
            $this->datagrid->addColumn( $column_agencia );
            $this->datagrid->addColumn( $column_numero );
            $this->datagrid->addColumn( $column_tipo );
            $this->datagrid->addColumn( $column_credito );
            $this->datagrid->addColumn( $column_debito );
            $this->datagrid->addColumn( $column_saldo );
            $this->datagrid->addColumn( $column_ativo );
            $this->datagrid->addColumn( $column_data_fechamento );

            $action_edit = new TDataGridAction( ['contacorrenteForm', 'onEdit'], ['key' => '{id}', 'register_state' => 'false'] );
            $action_del  = new TDataGridAction( [$this, 'onDelete'], ['key' => '{id}', 'register_state' => 'false'] );

            $this->datagrid->addAction( $action_edit, _t( 'Edit' ), 'far:edit blue fa-lg' );
            $this->datagrid->addAction( $action_del, _t( 'Delete' ), 'far:trash-alt red fa-lg' );

            $this->datagrid->createModel();

            $this->pageNavigation = new TPageNavigation();
            $this->pageNavigation->setAction( new TAction( [ $this, 'onReload' ] ) );
            $this->pageNavigation->setWidth( $this->datagrid->getWidth() );

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
