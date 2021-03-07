<?php
    
    use Adianti\Database\TTransaction;
    use Adianti\Widget\Datagrid\TDataGrid;
    use Adianti\Widget\Datagrid\TPageNavigation;
    use Adianti\Widget\Wrapper\TDBCombo;
    use Adianti\Widget\Wrapper\TDBUniqueSearch;
    
    class titulosPagar extends TWindow
    {
        private $form;
        private $datagrid;
        private $pageNavigation;
        private $loaded;
        
        public function __construct()
        {
            parent::__construct();
            parent::setTitle( 'Lista de titulos em aberto' );
            parent::setSize( 0.8, NULL );
            
            $this->form = new BootstrapFormBuilder( 'form_'.__CLASS__ );
            
            $id                = new TEntry( 'id' );
            $pessoa_id         = new TDBUniqueSearch( 'pessoa_id', 'afincco', 'Pessoa', 'id', 'nome', 'nome' );
            $tipolancamento_id = new TDBCombo( 'tipolancamento_id', 'afincco', 'TipoLancamento', 'id', 'nome', 'nome' );
            $data_vencimento   = new TDate( 'data_vencimento' );
            $pagar_receber     = new TCombo( 'pagar_receber' );
            $numero            = new TEntry( 'numero' );
            
            $pagar_receber->addItems( Utilidades::pagar_receber() );
            
            $tipolancamento_id->enableSearch();
            
            $id->style              .= 'text-align:center;';
            $pessoa_id->style       .= 'text-align:center;';
            $data_vencimento->style .= 'text-align:center;';
            
            $campo_id             = [ new TLabel( 'Código' ), $id ];
            $campo_pessoa         = [ new TLabel( 'Cliente' ), $pessoa_id ];
            $campo_tipolancamento = [ new TLabel( 'Tipo Lançamento' ), $tipolancamento_id ];
            $campo_datavencimento = [ new TLabel( 'Data Vencimento' ), $data_vencimento ];
            $campo_numero         = [ new TLabel( 'Numero' ), $numero ];
            
            $row         = $this->form->addFields( $campo_id, $campo_pessoa, $campo_tipolancamento, $campo_datavencimento, $campo_numero );
            $row->layout = [ 'col-sm-1', 'col-sm-3', 'col-sm-3', 'col-sm-2', 'col-sm-2', 'col-sm-1' ];
            
            $this->form->setData( TSession::getValue( 'titulo_filter_data' ) );
            
            $this->form->addAction( _t( 'Find' ), new TAction( [ $this, 'onSearch' ] ), 'fa:search' );
            $this->form->addAction( 'Vincular', new TAction( [ $this, 'onVincular' ] ), 'far:check-circle green' );
            $this->form->addAction( 'Voltar', new TAction( [ $this, 'onRetornar' ] ), 'fa:table blue' );
            
            $this->datagrid        = new BootstrapDatagridWrapper( new TDataGrid() );
            
            $column_id                = new TDataGridColumn( 'id', 'Código', 'center' );
            $column_pessoa_id         = new TDataGridColumn( 'pessoa->nome', 'Cliente', 'left' );
            $column_tipolancamento_id = new TDataGridColumn( 'tipolancamento->nome', 'Tipo de Lancamento', 'left' );
            $column_data_vencimento   = new TDataGridColumn( 'data_vencimento', 'Data Vencimento', 'center' );
            $column_valor             = new TDataGridColumn( 'valor', 'Valor', 'right' );
            $column_saldo             = new TDataGridColumn( 'saldo', 'Saldo', 'right' );
            $column_numero            = new TDataGridColumn( 'numero', 'Numero', 'center' );
            $column_dc                = new TDataGridColumn( 'dc', 'D/C', 'center' );
            
            $this->datagrid->addColumn( $column_id );
            $this->datagrid->addColumn( $column_pessoa_id );
            $this->datagrid->addColumn( $column_tipolancamento_id );
            $this->datagrid->addColumn( $column_data_vencimento );
            $this->datagrid->addColumn( $column_valor );
            $this->datagrid->addColumn( $column_saldo );
            $this->datagrid->addColumn( $column_numero );
            $this->datagrid->addColumn( $column_dc );
            
            $column_id->setTransformer( [ $this, 'formatRow' ] );
            
            $order_id = new TAction( [ $this, 'onReload' ] );
            $order_id->setParameter( 'order', 'id' );
            $column_id->setAction( $order_id );
            
            $order_pessoa_id = new TAction( [ $this, 'onReload' ] );
            $order_pessoa_id->setParameter( 'order', 'pessoa_id' );
            $column_pessoa_id->setAction( $order_pessoa_id );
            
            $order_tipolancamento_id = new TAction( [ $this, 'onReload' ] );
            $order_tipolancamento_id->setParameter( 'order', 'tipolancamento_id' );
            $column_tipolancamento_id->setAction( $order_tipolancamento_id );
            
            $order_data_vencimento = new TAction( [ $this, 'onReload' ] );
            $order_data_vencimento->setParameter( 'order', 'data_vencimento' );
            $column_data_vencimento->setAction( $order_data_vencimento );
            
            $order_valor = new TAction( [ $this, 'onReload' ] );
            $order_valor->setParameter( 'order', 'valor' );
            $column_valor->setAction( $order_valor );
            
            $order_saldo = new TAction( [ $this, 'onReload' ] );
            $order_saldo->setParameter( 'order', 'saldo' );
            $column_saldo->setAction( $order_saldo );
            
            $order_dc = new TAction( [ $this, 'onReload' ] );
            $order_dc->setParameter( 'order', 'dc' );
            $column_dc->setAction( $order_dc );
            
            $column_data_vencimento->setTransformer(
                function( $value, $object, $row ) {
                    $date = new DateTime( $value );
                    
                    return $date->format( 'd/m/Y' );
                }
            );
            
            $column_valor->setTransformer(
                function( $value, $object, $row ) {
                    return 'R$ '.number_format( $value, 2, ',', '.' );
                }
            );
            
            $column_saldo->setTransformer(
                function( $value, $object, $row ) {
                    return 'R$ '.number_format( $value, 2, ',', '.' );
                }
            );
            
            $column_dc->setTransformer(
                function( $value, $object, $row ) {
                    return strtoupper( $value );
                }
            );
            
            $action_select = new TDataGridAction( [ $this, 'onSelect' ], [ 'key' => '{id}', 'register_state' => 'false' ] );
            
            $action_select->setUseButton( TRUE );
            
            $this->datagrid->addAction( $action_select, 'Selecionar', 'far:check-circle green fa-lg' );
            
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
        
        public function onRetornar( $param )
        {
            parent::closeWindow();
        }
        
        public function onCaixaVincula( $param )
        {
            TSession::setValue( 'caixa', NULL );
            TSession::setValue( 'saldo', NULL );
            TSession::setValue( 'vincular', NULL );
            TSession::setValue( 'dc', NULL );
            try {
                TTransaction::open( 'afincco' );
                $objeto = new Caixa( $param[ 'id' ] );
                if ( $objeto ) {
                    TSession::setValue( 'caixa', $objeto->id );
                    TSession::setValue( 'saldo', $objeto->saldo );
                    TSession::setValue( 'dc', $objeto->dc );
                }
                TTransaction::close();
            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();
            }
        }
        
        public function onSearch()
        {
            $data = $this->form->getData();
            
            TSession::delValue( 'titulosPagar_filter_id' );
            TSession::delValue( 'titulosPagar_filter_pessoa_id' );
            TSession::delValue( 'titulosPagar_filter_tipolancamento_id' );
            TSession::delValue( 'titulosPagar_filter_data_vencimento' );
            TSession::delValue( 'titulosPagar_filter_numero' );
            
            if ( isset( $data->id ) and ( $data->id ) ) {
                $filter = new TFilter( 'id', '=', "$data->id" );
                TSession::setValue( 'titulosPagar_filter_id', $filter );
            }
            
            if ( isset( $data->pessoa_id ) and ( $data->pessoa_id ) ) {
                $filter = new TFilter( 'pessoa_id', '=', "$data->pessoa_id" );
                TSession::setValue( 'titulosPagar_filter_pessoa_id', $filter );
            }
            
            if ( isset( $data->tipolancamento_id ) and ( $data->tipolancamento_id ) ) {
                $filter = new TFilter( 'tipolancamento_id', '=', "$data->tipolancamento_id" );
                TSession::setValue( 'titulosPagar_filter_tipolancamento_id', $filter );
            }
            
            if ( isset( $data->data_vencimento ) and ( $data->data_vencimento ) ) {
                $filter = new TFilter( 'data_vencimento', '=', "$data->data_vencimento" );
                TSession::setValue( 'titulosPagar_filter_data_vencimento', $filter );
            }
            
            if ( isset( $data->numero ) and ( $data->numero ) ) {
                $filter = new TFilter( 'numero', '=', "$data->numero" );
                TSession::setValue( 'titulosPagar_filter_numero', $filter );
            }
            
            $this->form->setData( $data );
            
            TSession::setValue( 'titulo_filter_data', $data );
            
            $param                 = [];
            $param[ 'offset' ]     = 0;
            $param[ 'first_page' ] = 1;
            $this->onReload( $param );
        }
        
        public function onReload( $param = NULL )
        {
            try {
                TTransaction::open( 'afincco' );
                
                $repository = new TRepository( 'Titulo' );
                $limit      = 10;
                $criteria   = new TCriteria;
                
                if ( empty( $param[ 'order' ] ) ) {
                    $param[ 'order' ]     = 'id';
                    $param[ 'direction' ] = 'asc';
                }
                
                $tem_filtro = FALSE;
                $criteria->setProperties( $param );
                $criteria->setProperty( 'limit', $limit );
                
                if ( TSession::getValue( 'titulosPagar_filter_id' ) ) {
                    $criteria->add( TSession::getValue( 'titulosPagar_filter_id' ) );
                    $tem_filtro = TRUE;
                }
                
                if ( TSession::getValue( 'titulosPagar_filter_pessoa_id' ) ) {
                    $criteria->add( TSession::getValue( 'titulosPagar_filter_pessoa_id' ) );
                    $tem_filtro = TRUE;
                }
                
                if ( TSession::getValue( 'titulosPagar_filter_tipolancamento_id' ) ) {
                    $criteria->add( TSession::getValue( 'titulosPagar_filter_tipolancamento_id' ) );
                    $tem_filtro = TRUE;
                }
                
                if ( TSession::getValue( 'titulosPagar_filter_data_vencimento' ) ) {
                    $criteria->add( TSession::getValue( 'titulosPagar_filter_data_vencimento' ) );
                    $tem_filtro = TRUE;
                }
                
                if ( TSession::getValue( 'titulosPagar_filter_numero' ) ) {
                    $criteria->add( TSession::getValue( 'titulosPagar_filter_numero' ) );
                    $tem_filtro = TRUE;
                }
                
                if ( !$tem_filtro ) {
                    $filter = new TFilter( 'data_vencimento', '<=', date( 'Y-m-d' ) );
                    $criteria->add( $filter );
                }
                
                $filter = new TFilter( 'pagar_receber', '=', TSession::getValue( 'dc' ) == 'D' ? 'P' : 'R' );
                $criteria->add( $filter );
                $filter = new TFilter( 'saldo', '>', '0' );
                $criteria->add( $filter );
                
                $objects = $repository->load( $criteria, FALSE );
                
                if ( is_callable( $this->transformCallback ) ) {
                    call_user_func( $this->transformCallback, $objects, $param );
                }
                
                $this->datagrid->clear();
                if ( $objects ) {
                    foreach ( $objects as $object ) {
                        $this->datagrid->addItem( $object );
                    }
                }
                
                $criteria->resetProperties();
                $count = $repository->count( $criteria );
                
                $this->pageNavigation->setCount( $count );
                $this->pageNavigation->setProperties( $param );
                $this->pageNavigation->setLimit( $limit );
                
                TTransaction::close();
                $this->loaded = TRUE;
            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();
            }
        }
        
        public function onSelect( $param )
        {
            $selected_objects = TSession::getValue( 'vincular' );
            $valor_id         = $param[ 'id' ];
            if ( isset( $selected_objects[ $valor_id ] ) ) {
                unset( $selected_objects[ $valor_id ] );
            } else {
                $selected_objects[ $valor_id ] = $valor_id;
            }
            TSession::setValue( 'vincular', $selected_objects );
            
            $this->onReload( func_get_arg( 0 ) );
        }
        
        public function formatRow( $value, $object, $row )
        {
            $selected_objects = TSession::getValue( 'vincular' );
            if ( $selected_objects ) {
                if ( in_array( (int)$value, array_keys( $selected_objects ) ) ) {
                    $row->style = "background: #FFD965";
                }
            }
            
            return $value;
        }
        
        public function onVincular()
        {
            try {
                TTransaction::open( 'afincco' );
                $selected_objects = TSession::getValue( 'vincular' );
                ksort( $selected_objects );
                if ( $selected_objects ) {
                    $caixa = Caixa::find( TSession::getValue( 'caixa' ) );
                    if ( $caixa ) {
                        foreach ( $selected_objects as $selected_object ) {
                            if ( $caixa->saldo > 0 ) {
                                $titulo                       = Titulo::find( $selected_object );
                                $movimento                    = new MovimentoTitulo();
                                $movimento->data_movimento    = $caixa->data_movimento;
                                $movimento->titulo_id         = $selected_object;
                                $movimento->caixa_id          = $caixa->id;
                                $movimento->cheque_id         = NULL;
                                $movimento->tipolancamento_id = $caixa->tipolancamento_id;
                                if ( $titulo->pagar_receber === 'R' ) {
                                    $movimento->dc = $caixa->dc == "C" ? "D" : "C";
                                } else {
                                    $movimento->dc = $caixa->dc;
                                }
                                $movimento->observacao = "";
                                
                                if ( $titulo->saldo >= $caixa->saldo ) {
                                    $movimento->valor = $caixa->saldo;
                                    $caixa->saldo     = 0;
                                } else {
                                    if ( $titulo->saldo < $caixa->saldo ) {
                                        $movimento->valor = $titulo->saldo;
                                        $caixa->saldo     -= $titulo->saldo;
                                    }
                                }
                                
                                $caixa->store();
                                $movimento->store();
                            }
                        }
                    }
                }
                TSession::delValue( 'vincular' );
                TTransaction::close();
            } catch ( Exception $e ) {
                TSession::delValue( 'vincular' );
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();
            }
            $this->onReload();
        }
        
        public function show()
        {
            if ( !$this->loaded and ( !isset( $_GET[ 'method' ] ) or !( in_array( $_GET[ 'method' ], [ 'onReload', 'onSearch' ] ) ) ) ) {
                if ( func_num_args() > 0 ) {
                    $this->onReload( func_get_arg( 0 ) );
                } else {
                    $this->onReload();
                }
            }
            parent::show();
        }
    }
