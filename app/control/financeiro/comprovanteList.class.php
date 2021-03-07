<?php
    
    use Adianti\Database\TCriteria;
    
    class comprovanteList extends TStandardList
    {
        public function __construct() {
            parent::__construct();

            parent::setDatabase( 'afincco' );
            parent::setActiveRecord( 'Comprovante' );
            parent::setDefaultOrder( 'id', 'desc' );

            parent::addFilterField( 'id_processo', '=', 'id_processo' );
            parent::addFilterField( 'id_seg', '=', 'id_seg' );
            parent::addFilterField( 'PlacaVeiculo' );
            
            $criteria_seguradora = new TCriteria();
            $filter = new TFilter('seguradora', '=', '1');
            $criteria_seguradora->add($filter);

            $this->form = new BootstrapFormBuilder( 'form_'.__CLASS__ );
            $this->form->setFormTitle( 'Recibos' );

            $id_processo  = new TEntry( 'id_processo' );
            $id_seg       = new TDBCombo( 'id_seg', 'afincco', 'Pessoa', 'id', 'nome', 'nome', $criteria_seguradora );
            $placaVeiculo = new TEntry( 'PlacaVeiculo' );

            $id_seg->enableSearch();

            $id_processo->style  .= 'text-align:center; color:$ff0000; font-wight:bold;';
            $placaVeiculo->style .= 'text-align:center; color:$ff0000; font-wight:bold;';
            $id_seg->style       .= 'color:$ff0000; font-wight:bold;';

            $campo_processo   = [ new TLabel( 'Processo' ), $id_processo ];
            $campo_placa      = [ new TLabel( 'Placa' ), $placaVeiculo ];
            $campo_seguradora = [ new TLabel( 'Seguradora' ), $id_seg ];

            $this->form->addFields( $campo_processo, $campo_placa, $campo_seguradora )->layout = [ 'col-md-1', 'col-md-1', 'col-md-4' ];

            $this->form->setData( TSession::getValue( 'Comprovante_filter_data' ) );

            $this->form->addAction( _t( 'Find' ), new TAction( array($this, 'onSearch') ), 'fa:search blue' );
            $this->form->addActionlink( _t( 'New' ), new TAction( ['comprovanteForm', 'onClear'], ['register_state' => 'false'] ), 'fa:eraser red' );

            $this->datagrid        = new BootstrapDatagridWrapper( new TDataGrid() );

            $column_IdComprovante  = new TDataGridColumn( 'id', 'Comprovante', 'center', '8%' );
            $column_id_processo    = new TDataGridColumn( 'id_processo', 'Processo', 'center', '6%' );
            $column_id_seg         = new TDataGridColumn( 'pessoa->nome', 'Seguradora', 'left');
            $column_PlacaVeiculo   = new TDataGridColumn( 'PlacaVeiculo', 'Placa', 'center', '8%' );
            $column_ValorTotal     = new TDataGridColumn( 'ValorTotal', 'Valor Total', 'right', '10%' );
            $column_Status         = new TDataGridColumn( 'Status', 'Status', 'center', '5%' );
            $column_Data_processo  = new TDataGridColumn( 'Data_processo', 'Data Processo', 'center', '12%' );
            $column_Data_Atualizao = new TDataGridColumn( 'Data_Atualizao', 'Data Atualização', 'center', '12%' );

            $column_ValorTotal->setTransformer( function( $value, $object, $row ) {
                if (is_numeric($value)) {
                    return number_format( $value, 2, ',', '.' );
                }
                return $value;
            } );

            $column_Status->setTransformer( function ($value) {
                if ($value == 'Ativo')
                {
                    $div = new TElement('span');
                    $div->class="label label-success";
                    $div->style="text-shadow:none; font-size:12px";
                    $div->add($value);
                    return $div;
                }
                else
                {
                    if (empty($value))
                    {
                        return $value;
                    }
                    $div = new TElement('span');
                    $div->class="label label-danger";
                    $div->style="text-shadow:none; font-size:12px";
                    $div->add($value);
                    return $div;
                }
            });


            $column_Data_processo->setTransformer( function( $value, $object, $row ) {
                $data_processo = new DateTime( $value );
                return $data_processo->format( 'd/m/Y H:i:s' );
            } );

            $column_ValorTotal->setTotalFunction( function( $values ) {
                return array_sum( (array)$values );
            } );

            $this->datagrid->addColumn( $column_IdComprovante );
            $this->datagrid->addColumn( $column_id_processo );
            $this->datagrid->addColumn( $column_id_seg );
            $this->datagrid->addColumn( $column_PlacaVeiculo );
            $this->datagrid->addColumn( $column_ValorTotal );
            $this->datagrid->addColumn( $column_Status );
            $this->datagrid->addColumn( $column_Data_processo );
            $this->datagrid->addColumn( $column_Data_Atualizao );

            $action_edit = new TDataGridAction( ['comprovanteForm', 'onEdit'], ['key' => '{id}', 'register_state' => 'false'] );
            $action_del  = new TDataGridAction( [$this, 'onDelete'], ['key' => '{id}', 'register_state' => 'false'] );
    
            $action_del->setDisplayCondition( [ $this, 'mostraDeletar' ] );
            
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

        public function mostraDeletar( $object ) {
            if ( $object->onVerificaTitulo() ) {
                return FALSE;
            }
            return TRUE;
        }


        public function onReler( $param ) {
            $this->onReload();
        }
    }
