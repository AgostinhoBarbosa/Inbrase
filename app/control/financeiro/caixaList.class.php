<?php
    
    use Adianti\Base\TStandardList;
    use Adianti\Core\AdiantiCoreApplication;
    use Adianti\Database\TTransaction;
    use Adianti\Widget\Container\TTableRow;
    use Adianti\Widget\Datagrid\TDataGrid;
    use Adianti\Widget\Form\TDate;
    use Adianti\Widget\Form\TEntry;
    use Adianti\Widget\Form\TNumeric;
    use Adianti\Widget\Wrapper\TDBCombo;
    use Adianti\Wrapper\BootstrapDatagridWrapper;
    
    class caixaList extends TStandardList
    {

        public function __construct() {
            parent::__construct();

            parent::setDatabase( 'afincco' );
            parent::setActiveRecord( 'Caixa' );
            parent::setDefaultOrder( 'id', 'asc' );

            $this->form = new BootstrapFormBuilder( 'form_'.__CLASS__ );
            $this->form->setFormTitle( 'Movimento de Caixa' );

            $criteria = new TCriteria();
            $filter   = new TFilter( 'ativo', '=', '1' );
            $criteria->add( $filter );

            $contacorrente_id  = new TDBCombo( 'contacorrente_id', 'afincco', 'Contacorrente', 'id', 'nome', 'nome', $criteria );
            $data_ini          = new TDate( 'data_ini' );
            $data_fim          = new TDate( 'data_fim' );
            $valor             = new TNumeric( 'valor', 2, ',', '.' );
            $numeroOS          = new TEntry( 'numeroOS' );
            $tipolancamento_id = new TDBCombo( 'tipolancamento_id', 'afincco', 'TipoLancamento', 'id', 'nome', 'nome' );

            $contacorrente_id->style = ( 'background-color: #FFFEEB;' );
            $data_ini->style         = ( 'background-color: #FFFEEB;' );
            $data_fim->style         = ( 'background-color: #FFFEEB;' );
            $valor->style            = ( 'background-color: #FFFEEB;' );
            $numeroOS->style         = ( 'background-color: #FFFEEB; text-align:center;' );

            $data_ini->setMask( 'dd/mm/yyyy' );
            $data_fim->setMask( 'dd/mm/yyyy' );
            $numeroOS->setMask( '999999' );

            $data_ini->setDatabaseMask( 'yyyy-mm-dd' );
            $data_fim->setDatabaseMask( 'yyyy-mm-dd' );

            $campo_conta_corrente     = [ new TLabel( 'Conta Corrente ' ), $contacorrente_id ];
            $campo_data_movimento_ini = [ new TLabel( 'Data Inicial ' ), $data_ini ];
            $campo_data_movimento_fim = [ new TLabel( 'Data Final ' ), $data_fim ];
            $campo_valor              = [ new TLabel( 'Valor ' ), $valor ];
            $campo_os                 = [ new TLabel( 'Nº Processo ' ), $numeroOS ];
            $campo_tipolancamento     = [ new TLabel( 'Tipo Lançamento' ), $tipolancamento_id ];

            $row         = $this->form->addFields( $campo_conta_corrente, $campo_data_movimento_ini, $campo_data_movimento_fim, $campo_valor, $campo_os, $campo_tipolancamento );
            $row->layout = [ 'col-md-3', 'col-md-2', 'col-md-2', 'col-md-1', 'col-md-1', 'col-md-3' ];

            $this->form->setData( TSession::getValue( 'Caixa_filter_data' ) );

            $this->form->addAction( _t( 'Find' ), new TAction( [ $this, 'onSearch' ] ), 'fa:search blue' );
            $this->form->addActionlink( _t( 'New' ), new TAction( [ 'caixaForm',
                                                                    'onClear' ], [ 'register_state' => 'false' ] ), 'fa:eraser red' );
            $this->form->addAction( 'Importar OFX', new TAction( [ $this, 'onImportarOFX' ] ), 'fas:exchange-alt red' );

            $this->datagrid        = new BootstrapDatagridWrapper( new TDataGrid() );

            $column_id             = new TDataGridColumn( 'id', 'Código', 'center', '5%' );
            $column_data_movimento = new TDataGridColumn( 'data_movimento', 'Data Movimento', 'center', '10%' );
            $column_historico      = new TDataGridColumn( 'historico', 'Histórico', 'left', '40%' );
            $column_operacao_id    = new TDataGridColumn( 'operacao_id', 'Processo', 'center', '10%' );
            $column_dc             = new TDataGridColumn( 'dc', 'D/C', 'center', '5%' );
            $column_valor          = new TDataGridColumn( 'valor', 'Valor', 'right', '15%' );
            $column_saldo          = new TDataGridColumn( 'saldo_atual', 'Saldo', 'right', '15%' );

            $column_data_movimento->setTransformer( function( $value, $object, $row ) {
                return "<b>".TDate::date2br( $value )."</b>";
            } );

            $column_valor->setTransformer( function( $value, $object, $row ) {
                if ( $object->dc == "D" ) {
                    return "<p style='color:#ff0000'><b>".number_format( $value, 2, ',', '.' )."</b></p>";
                }

                return "<p style='color:#00008B'><b>".number_format( $value, 2, ',', '.' )."</b></p>";
            } );

            $column_saldo->setTransformer( function( $value, $object, $row ) {
                if ( $value < 0.00 ) {
                    return "<p style='color:#ff0000'><b>".number_format( $value, 2, ',', '.' )."</b></p>";
                }

                return "<p style='color:#00008B'><b>".number_format( $value, 2, ',', '.' )."</b></p>";
            } );

            $column_dc->setTransformer( function( $value, $object, $row ) {
                if ( $value == "D" ) {
                    $row->style = "background-color:#F0E68C;";

                    return "<b style='color:#ff0000'>".$value."</b>";
                } else {
                    $row->style = "background-color:#FFFFFF;";

                    return "<b style='color:#00008B'>".$value."</b>";
                }
            } );

            $this->datagrid->addColumn( $column_id );
            $this->datagrid->addColumn( $column_data_movimento );
            $this->datagrid->addColumn( $column_historico );
            $this->datagrid->addColumn( $column_operacao_id );
            $this->datagrid->addColumn( $column_dc );
            $this->datagrid->addColumn( $column_valor );
            $this->datagrid->addColumn( $column_saldo );

            $action_edit = new TDataGridAction( [ 'caixaForm', 'onEdit' ], [ 'key'            => '{id}',
                                                                             'register_state' => 'false' ] );
            $action_del  = new TDataGridAction( [ $this, 'onDelete' ], [ 'key'            => '{id}',
                                                                         'register_state' => 'false' ] );
            $action_vin  = new TDataGridAction( [ 'titulosPagar', 'onCaixaVincula' ], [ 'key'            => '{id}',
                                                                                        'register_state' => 'false' ] );
            $action_mov  = new TDataGridAction( [ 'movimentoTituloForm', 'onClear' ], [ 'key'            => '{id}',
                                                                                        'register_state' => 'false' ] );
            $action_det  = new TDataGridAction( [ $this, 'onShowDetail' ], [ 'key'            => '{id}',
                                                                             'register_state' => 'false' ] );

            $action_vin->setDisplayCondition( [ $this, 'mostraVincular' ] );
            $action_mov->setDisplayCondition( [ $this, 'mostraVincular' ] );
            $action_det->setDisplayCondition( [ $this, 'mostraMovimento' ] );
            $action_del->setDisplayCondition( [ $this, 'mostraDeletar' ] );

            $this->datagrid->addAction( $action_edit, _t( 'Edit' ), 'far:edit blue fa-lg' );
            $this->datagrid->addAction( $action_del, _t( 'Delete' ), 'far:trash-alt red fa-lg' );
            $this->datagrid->addAction( $action_vin, 'Vincular Títulos', 'fas:paperclip brown fa-lg' );
            $this->datagrid->addAction( $action_mov, 'Inserir Movimento', 'far:money-bill-alt brown fa-lg' );
            $this->datagrid->addAction( $action_det, 'Detalhes Movimento', 'far:plus-square green fa-lg' );

            $this->datagrid->createModel();

            $this->pageNavigation = new TPageNavigation();
            $this->pageNavigation->setAction( new TAction( [ $this, 'onReload' ] ) );
            $this->pageNavigation->setWidth( $this->datagrid->getWidth() );

            $dropdown = new TDropDown( _t( 'Export' ), 'fa:list' );
            $dropdown->setPullSide( 'right' );
            $dropdown->setButtonClass( 'btn btn-default waves-effect dropdown-toggle' );
            $dropdown->addAction( _t( 'Save as CSV' ), new TAction( [ $this,
                                                                      'onExportCSV' ], [ 'register_state' => 'false',
                                                                                         'static'         => '1' ] ), 'fa:table fa-fw blue' );
            $dropdown->addAction( _t( 'Save as PDF' ), new TAction( [ $this,
                                                                      'onExportPDF' ], [ 'register_state' => 'false',
                                                                                         'static'         => '1' ] ), 'far:file-pdf fa-fw red' );
            $dropdown->addAction( _t( 'Save as XML' ), new TAction( [ $this,
                                                                      'onExportXML' ], [ 'register_state' => 'false',
                                                                                         'static'         => '1' ] ), 'fa:code fa-fw green' );

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

        public static function onImportarOFX( $param ) {
            $form        = new TQuickForm( 'input_form' );
            $form->style = 'padding:20px';

            $arquivo = new TFile( 'arquivo' );

            $form->addQuickField( 'Arquivo', $arquivo );

            $form->addQuickAction( 'Salvar', new TAction( [ 'caixaList', 'onProcessaOFX' ] ), 'ico_save.png' );

            new TInputDialog( 'Importa Arquivo OFX', $form );
        }

        public static function onProcessaOFX( $param ) {

            $file = "tmp/".$param[ 'arquivo' ];
            $file = caixaList::closeTags( $file );

            // 1. Leia no arquivo
            $cont = file_get_contents( $file );
            // 2. Separe e remova o cabeçho
            $bline = strpos( $cont, "<OFX>" );
            $head  = substr( $cont, 0, $bline - 2 );
            $ofx   = substr( $cont, $bline - 1 );
            // 3. Examine tags que possam estar terminadas de forma impróa
            $ofxx = $ofx;
            $tot  = 0;
            $post = 0;

            while ( $pos = strpos( $ofxx, '<' ) ) {
                $tot++;
                $pos2 = strpos( $ofxx, '>' );
                $ele  = substr( $ofxx, $pos + 1, $pos2 - $pos - 1 );
                if ( substr( $ele, 0, 1 ) == '/' ) {
                    $sla[] = substr( $ele, 1 );
                } else {
                    $als[] = $ele;
                }
                $ofxx = substr( $ofxx, $pos2 + 1 );
            }

            $adif = array_diff( $als, $sla );
            $adif = array_unique( $adif );
            $ofxy = $ofx;
            // 4. Termine aquelas que precisam de terminaç
            foreach ( $adif as $dif ) {
                $dpos = 0;
                while ( $dpos = strpos( $ofxy, $dif, $dpos + 1 ) ) {
                    $npos = strpos( $ofxy, '<', $dpos + 1 );
                    $ofxy = substr_replace( $ofxy, "</$dif>\n<", $npos, 1 );
                    $dpos = $npos + strlen( $ele ) + 3;
                }
            }
            // 5. Lide com caracteres especiais
            $ofxy = str_replace( '&', '&amp;', $ofxy );
            // 6. Grave a cadeia de caracteres resultante na tela
            $myFile = 'tmp/importa.xml';
            if ( file_exists( $myFile ) ) {
                unlink( $myFile );
            }

            file_put_contents( $myFile, utf8_encode( $ofxy ) );

            // testar ofx
            $xmlstr = file_get_contents( 'tmp/importa.xml' );
            $xml    = new SimpleXMLElement( $xmlstr );

            // Vamos obter o saldo primeiro
            $bal   = $xml->BANKMSGSRSV1->STMTTRNRS->STMTRS->LEDGERBAL->BALAMT;
            $dat   = $xml->BANKMSGSRSV1->STMTTRNRS->STMTRS->LEDGERBAL->DTASOF;
            $chave = trim( $xml->BANKMSGSRSV1->STMTTRNRS->STMTRS->BANKACCTFROM->ACCTID );

            try {
                TTransaction::open( 'afincco' );

                $conta = Contacorrente::where( 'chave', '=', $chave )->first();
                if ( !$conta ) {
                    TTransaction::close();
                    new TMessage( 'error', 'Chave Importação não encontrada!!' );

                    return;
                }

                $data = strtotime( substr( $dat, 0, 8 ) );
                $datb = date( 'Y-m-d', $data );
                $dath = date( 'Y-m-d', strtotime( '-1 day' ) );

                // Agora, aponte para a array de transaçs e mostre os detalhes de cada uma

                $trans = $xml->BANKMSGSRSV1->STMTTRNRS->STMTRS->BANKTRANLIST->STMTTRN;

                foreach ( $trans as $tran ) {
                    $trandate = trim( $tran->DTPOSTED );
                    $tdate    = date( "Y-m-d", strtotime( substr( $trandate, 0, 8 ) ) );

                    if ( $tdate > $dath ) {
                        continue;
                    }

                    $tranamt  = $tran->TRNAMT;
                    $tranamt  = str_replace( ',', '.', $tranamt );
                    $trancrdr = $tran->TRNTYPE;
                    $codigo0  = $tran->FITID;
                    $codigo1  = $tran->CHECKNUM;
                    $debcred  = 'C';
                    $tipolan  = 1;

                    if ( $tranamt < 0 ) {
                        $debcred = 'D';
                        $tranamt = str_replace( '-', '', $tranamt );
                    }

                    $controle = 0;

                    if ( $tdate > $dath ) {
                        $controle = 1;
                    } //Nao importa lançamentos futuros

                    if ( $controle == 0 ) {
                        $memo = trim( utf8_decode( $tran->MEMO ) );
                        $memo = str_replace( "'", "", $memo );

                        $val = substr_count( $memo, "Aplica" );
                        if ( $val > 0 ) {
                            $tipolan = 25;
                        }
                        $val = substr_count( $memo, "APLICACOES EM PAPEIS" );
                        if ( $val > 0 ) {
                            $tipolan = 25;
                        }
                        $val = substr_count( $memo, "TITULO DE CAPITALIZACAO" );
                        if ( $val > 0 ) {
                            $tipolan = 25;
                        }
                        $val = substr_count( $memo, "Resgate" );
                        if ( $val > 0 ) {
                            $tipolan = 24;
                        }
                        $val = substr_count( $memo, "RESGATE DE PAPEIS" );
                        if ( $val > 0 ) {
                            $tipolan = 24;
                        }
                        $val = substr_count( $memo, "bx Automatica" );
                        if ( $val > 0 ) {
                            $tipolan = 24;
                        }
                        $val = substr_count( $memo, "RESG AUTOM" );
                        if ( $val > 0 ) {
                            $tipolan = 24;
                        }
                        $val = substr_count( $memo, "REC CDB" );
                        if ( $val > 0 ) {
                            $tipolan = 24;
                        }
                        $val = substr_count( $memo, "TELEFONE" );
                        if ( $debcred == 'D' && $val > 0 ) {
                            $tipolan = 17;
                        }

                        $val = substr_count( $memo, "Transfer" );
                        if ( $debcred == 'D' && $val > 0 ) {
                            $tipolan = 50;
                        }
                        $val = substr_count( $memo, "Conta de Luz" );
                        if ( $debcred == 'D' && $val > 0 ) {
                            $tipolan = 46;
                        }
                        $val = substr_count( $memo, "LUZ/ENERGIA" );
                        if ( $debcred == 'D' && $val > 0 ) {
                            $tipolan = 45;
                        }
                        $val = substr_count( $memo, "AGUA" );
                        if ( $debcred == 'D' && $val > 0 ) {
                            $tipolan = 45;
                        }

                        $caixa = Caixa::where( 'data_movimento', '=', $tdate )->where( 'contacorrente_id', '=', $conta->id )->where( 'controle', '=', $codigo1 )->first();

                        if ( !$caixa ) {
                            $caixa                    = new Caixa();
                            $caixa->contacorrente_id  = $conta->id;
                            $caixa->tipolancamento_id = $tipolan;
                            $caixa->data_movimento    = $tdate;
                            $caixa->dc                = $debcred;
                            $caixa->valor             = $tranamt;
                            $caixa->saldo             = $tranamt;
                            $caixa->compensado        = 1;
                            $caixa->controle          = $codigo1;
                            $caixa->historico         = utf8_encode( $memo );
                            $caixa->usuario           = TSession::getValue( 'login' );
                            $caixa->pessoa_id         = NULL;
                            $caixa->store();
                        }
                    }
                }
            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();
            } finally {
                TTransaction::close();
                AdiantiCoreApplication::loadPage( __CLASS__, 'onReload' );
            }

            unlink( 'tmp/importa.xml' );
            unlink( $file );
        }

        public static function closeTags( $ofx = NULL ) {
            $buffer = '';
            $source = fopen( $ofx, 'r' ) or die( "Unable to open file!" );
            while ( !feof( $source ) ) {
                $line = trim( fgets( $source ) );
                if ( $line === '' ) {
                    continue;
                }

                if ( substr( $line, -1, 1 ) !== '>' ) {
                    [ $tag ] = explode( '>', $line, 2 );
                    $line .= '</'.substr( $tag, 1 ).'>';
                }
                $buffer .= $line."\n";
            }

            $name = realpath( dirname( $ofx ) ).'/'.date( 'Ymd' ).'.ofx';
            $file = fopen( $name, "w" ) or die( "Unable to open file!" );
            fwrite( $file, $buffer );
            fclose( $file );

            return $name;
        }

        public function mostraVincular( $object ) {
            if ( $object->saldo == '0.00' ) {
                return FALSE;
            }
            return TRUE;
        }
    
        public function mostraDeletar( $object  )
        {
            if (MovimentoTitulo::where( 'caixa_id', '=', $object->id )->count() > 0){
                return FALSE;
            }
            return TRUE;
        }

        public function mostraMovimento( $object  )
        {
            if (MovimentoTitulo::where( 'caixa_id', '=', $object->id )->count() > 0){
                return TRUE;
            }
            return FALSE;
        }

        public function onReload( $param = NULL ) {
            try {
                TTransaction::open( 'afincco' );

                $repository = new TRepository( 'Caixa' );
                $limit      = 10;
                $criteria   = new TCriteria();

                if ( empty( $param[ 'order' ] ) ) {
                    $param[ 'order' ]     = 'data_movimento, dc, id';
                    $param[ 'direction' ] = 'asc';
                }

                if ( count( $param ) == 0 ) {
                    $param = TSession::GetValue( 'Caixa_filter_data' );
                }

                $criteria->setProperties( $param );
                $criteria->setProperty( 'limit', $limit );

                $tem_filtro = FALSE;

                if ( TSession::getValue( 'caixaList_filter_contacorrente_id' ) ) {
                    $criteria->add( TSession::getValue( 'caixaList_filter_contacorrente_id' ) );
                    $tem_filtro = TRUE;
                }

                if ( TSession::getValue( 'caixaList_filter_data_movimento' ) ) {
                    $criteria->add( TSession::getValue( 'caixaList_filter_data_movimento' ) );
                    $tem_filtro = TRUE;
                }

                if ( TSession::getValue( 'caixaList_filter_valor' ) ) {
                    $criteria->add( TSession::getValue( 'caixaList_filter_valor' ) );
                    $tem_filtro = TRUE;
                }

                if ( TSession::getValue( 'caixaList_filter_numeroOS' ) ) {
                    $criteria->add( TSession::getValue( 'caixaList_filter_numeroOS' ) );
                    $tem_filtro = TRUE;
                }

                if ( TSession::getValue( 'caixaList_filter_tipolan' ) ) {
                    $criteria->add( TSession::getValue( 'caixaList_filter_tipolan' ) );
                    $tem_filtro = TRUE;
                }

                if ( $tem_filtro == FALSE ) {
                    $filter = new TFilter( 'id', '=', 0 );
                    $criteria->add( $filter );
                }

                $objects = $repository->load( $criteria, FALSE );

                if ( is_callable( $this->transformCallback ) ) {
                    call_user_func( $this->transformCallback, $objects, $param );
                }

                $this->datagrid->clear();

                if ( $objects ) {

                    $saldo_anterior = NULL;

                    foreach ( $objects as $object ) {
                        if ( $saldo_anterior == NULL ) {
                            $saldo_anterior = $object->get_saldo_anterior();
                        }
                        if ( $object->dc == "C" ) {
                            $saldo_anterior += $object->valor;
                        } else {
                            $saldo_anterior -= $object->valor;
                        }

                        $object->saldo_atual = $saldo_anterior;

                        $row          = $this->datagrid->addItem( $object );
                        $row->popover = 'true';
                        $row->popside = 'top';
                        if ( $object->saldo > 0 ) {
                            $row->popcontent = "<p style='color:blue;font-weight:900;text-align:center;'>".number_format( $object->saldo, 2, ',', '.' )."</p>";
                        } else {
                            $row->popcontent = "<p style='color:red;font-weight:900;text-align: center;'>".number_format( $object->saldo, 2, ',', '.' )."</p>";
                        }
                        $row->poptitle = "<p style='font-weight:900'>Saldo para Vinculo</p>";

                    }
                }

                $criteria->resetProperties();
                $count = $repository->count( $criteria );

                $this->pageNavigation->setCount( $count );
                $this->pageNavigation->setProperties( $param );
                $this->pageNavigation->setLimit( $limit );
                
                $this->loaded = TRUE;
            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();
            } finally {
                TTransaction::close();
            }
        }

        public function onSearch( $param = NULL ) {

            $data = $this->form->getData();

            TSession::delValue( 'caixaList_filter_contacorrente_id' );
            TSession::delValue( 'caixaList_filter_data_movimento' );
            TSession::delValue( 'caixaList_filter_valor' );
            TSession::delValue( 'caixaList_filter_numeroOS' );
            TSession::delValue( 'caixaList_filter_tipolan' );

            if ( isset( $data->contacorrente_id ) AND ( $data->contacorrente_id ) ) {
                $filter = new TFilter( 'contacorrente_id', '=', "$data->contacorrente_id" );
                TSession::setValue( 'caixaList_filter_contacorrente_id', $filter );
            }

            if ( isset( $data->data_ini ) AND ( $data->data_ini ) ) {
                if ( !$data->data_fim ) {
                    $data->data_fim = $data->data_ini;
                }
                $filter = new TFilter( 'data_movimento', 'BETWEEN', $data->data_ini, $data->data_fim );
                TSession::setValue( 'caixaList_filter_data_movimento', $filter );
            }

            if ( isset( $data->valor ) AND ( $data->valor ) ) {
                if ( $data->valor <> 0.00 ) {
                    $filter = new TFilter( 'valor', '=', "$data->valor" );
                    TSession::setValue( 'caixaList_filter_valor', $filter );
                }
            }

            if ( isset( $data->numeroOS ) AND ( $data->numeroOS ) ) {
                $filter = new TFilter( 'servico_id', '=', "$data->numeroOS" );
                TSession::setValue( 'caixaList_filter_numeroOS', $filter );
            }

            if ( isset( $data->tipolancamento_id ) AND ( $data->tipolancamento_id ) ) {
                $filter = new TFilter( 'tipolancamento_id', '=', "$data->tipolancamento_id" );
                TSession::setValue( 'caixaList_filter_tipolan', $filter );
            }

            $this->form->setData( $data );

            TSession::setValue( 'Caixa_filter_data', $data );

            $param                 = [];
            $param[ 'offset' ]     = 0;
            $param[ 'first_page' ] = 1;
            $this->onReload( $param );
        }

        public function onShowDetail( $param ) {
            $ultimo_detalhe = TSession::getValue( 'ultimo_detalhe' );

            if ( $ultimo_detalhe ) {
                if ( $ultimo_detalhe == $param[ 'key' ] ) {
                    TSession::delValue( 'ultimo_detalhe' );

                    return;
                }
            }
            TSession::setValue( 'ultimo_detalhe', $param[ 'key' ] );

            try {
                TTransaction::open( 'afincco' );

                $x         = 1;
                $tot_total = 0.00;

                $movimento = MovimentoTitulo::where( 'caixa_id', '=', $param[ 'key' ] )->load();

                if ( $movimento ) {
                    $pos = $this->datagrid->getRowIndex( 'id', $param[ 'key' ] );
    
                    $current_row = $this->datagrid->getRow( $pos );
    
                    $current_row->style = "background-color: #CDB38B; color:white; text-shadow:none";
    
                    $row              = new TTableRow();
                    $row->style       = "";
                    $cab_ini          = $row->addCell( 'Data Movimento' );
                    $cab_ini->style   = 'padding:10px;background-color: #ADD8E6;border: 1px solid;text-align:center;';
                    $cab_ini->colspan = 4;
                    $cab_1            = $row->addCell( 'Titulo' );
                    $cab_1->style     = 'padding:10px;background-color: #ADD8E6;border: 1px solid;text-align:center;';
                    $cab_2            = $row->addCell( 'Nº. Título' );
                    $cab_2->style     = 'padding:10px;background-color: #ADD8E6;border-top: 1px solid;border-bottom:1px solid;border-right: 1px solid;text-align:center;';
                    $cab_3            = $row->addCell( 'Cliente' );
                    $cab_3->style     = 'padding:10px;background-color: #ADD8E6;border-top: 1px solid;border-bottom:1px solid;border-right: 1px solid;text-align:center;';
                    $cab_4            = $row->addCell( 'Histórico' );
                    $cab_4->style     = 'padding:10px;background-color: #ADD8E6;border-top: 1px solid;border-bottom:1px solid;border-right: 1px solid;text-align:center;';
                    $cab_5            = $row->addCell( 'D/C' );
                    $cab_5->style     = 'padding:10px;background-color: #ADD8E6;border-top: 1px solid;border-bottom:1px solid;border-right: 1px solid;text-align:center;';
                    $cab_6            = $row->addCell( 'Valor' );
                    $cab_6->style     = 'padding:10px;background-color: #ADD8E6;border-top: 1px solid;border-bottom: 1px solid;border-right: 1px solid;text-align:center;';
                    $cab_7            = $row->addCell( 'Nº. Processo' );
                    $cab_7->style     = 'padding:10px;background-color: #ADD8E6;border-top: 1px solid;border-bottom:1px solid;border-right: 1px solid;text-align:center;';
                    $cab_8            = $row->addCell( 'Ação' );
                    $cab_8->style     = 'padding:10px;background-color: #ADD8E6;border-top: 1px solid;border-bottom: 1px solid;border-right: 1px solid;text-align:center;';
    
                    $this->datagrid->insert( $pos + 1, $row );
                    
                    foreach ( $movimento as $object ) {
                        $x++;
                        $linha            = new TTableRow();
                        $linha->style     = "";
                        $cab_ini          = $linha->addCell( TDate::date2br( $object->data_movimento ) );
                        $cab_ini->colspan = 4;
                        $cab_ini->style   = 'background-color: #FFFAFA;border-left: 1px solid;border-right:1px solid;border-bottom:1px solid;text-align:center;font-weight:900';
                        $cab_1            = $linha->addCell( $object->titulo_id );
                        $cab_1->style     = 'background-color: #FFFAFA;border-left: 1px solid;border-right:1px solid;border-bottom:1px solid;text-align:center;font-weight:900';
                        $cab_2            = $linha->addCell( "<b style='color:red;'>".$object->get_titulo()->numero."</b>" );
                        $cab_2->style     = 'background-color: #FFFAFA;border-bottom:1px solid;border-right: 1px solid;text-align:center;font-weight:700';
                        $cab_3            = $linha->addCell( "<b style='color:#00008B;font-weight:900'>".$object->get_titulo()->get_pessoa()->nome."</b>" );
                        $cab_3->style     = 'background-color: #FFFAFA;border-bottom:1px solid;border-right: 1px solid;text-align:left;';
                        $cab_4            = $linha->addCell( "<b style='color:#00008B;font-weight:900'>".$object->observacao."</b>" );
                        $cab_4->style     = 'width:90px !important;background-color: #FFFAFA;border-bottom:1px solid;border-right: 1px solid;text-align:left;';
                        $cab_5            = $linha->addCell( "<b style='color:#00008B;font-weight:bold;'>".$object->dc."</b>" );
                        $cab_5->style     = 'padding-left:5px;background-color: #FFFAFA;border-bottom:1px solid;text-align:center;';
                        $cab_6            = $linha->addCell( number_format( $object->valor, 2, ',', '.' ) );
                        $cab_6->style     = 'padding-right:5px;background-color: #FFFAFA;border-left: 1px solid;border-right: 1px solid;border-bottom: 1px solid;text-align:right;';
                        $cab_7            = $linha->addCell( "<b style='color:red;'>".$object->titulo->processo_id."</b>" );
                        $cab_7->style     = 'background-color: #FFFAFA;border-bottom:1px solid;border-right: 1px solid;text-align:center;font-weight:700';

                        $action_delmov = new TAction( [ $this, 'onExcluirMovimento' ] );
                        $action_delmov->setParameter( 'mov_id', $object->id );

                        $btn_delmov = new TButton( 'btnDelMov'.$object->id );
                        $btn_delmov->setAction( $action_delmov, 'Excluir' );
                        $btn_delmov->class = 'btn btn-danger btn-sm';
                        $btn_delmov->setImage( 'far:trash-alt white fa-lg' );

                        $action_updmov = new TAction( [ 'movimentoTituloForm', 'onEdit' ] );
                        $action_updmov->setParameter( 'key', $object->id );

                        $btn_updmov = new TButton( 'btnUpdMov'.$object->id );
                        $btn_updmov->setAction( $action_updmov, 'Editar' );
                        $btn_updmov->class = 'btn btn-info btn-sm';
                        $btn_updmov->setImage( 'fa:edit white fa-lg' );


                        $cab_8        = $linha->addMultiCell( $btn_updmov, $btn_delmov );
                        $cab_8->style = 'padding-right:5px;background-color: #FFFAFA;border-left: 1px solid;border-right: 1px solid;border-bottom: 1px solid;text-align:center;';
                        $this->form->addField( $btn_updmov );
                        $this->form->addField( $btn_delmov );
                        $tot_total += $object->valor;

                        $this->datagrid->insert( $pos + $x, $linha );
                    }
                    $x++;
                    $sumario          = new TTableRow();
                    $sum_tit          = $sumario->addCell( "<b style='color:#00008B;font-weight:bold'>Total das Baixas</b>" );
                    $sum_tit->colspan = 9;
                    $sum_tit->style   = 'background-color: #ADD8E6;border-left:1px solid;border-right: 1px solid;border-bottom: 1px solid;text-align:center;';
                    $sum_tot          = $sumario->addCell( "<b style='color:#00008B;font-weight:bold'>".number_format( $tot_total, 2, ',', '.' )."</b>" );
                    $sum_tot->style   = 'background-color: #ADD8E6;border-right: 1px solid;border-bottom: 1px solid;text-align:right;padding-right:5px;';
                    $sum_fim          = $sumario->addCell( "" );
                    $sum_fim->style   = 'background-color: #ADD8E6;border-left:1px solid;border-right: 1px solid;border-bottom: 1px solid;text-align:center;';
                    $sum_fim->colspan = 4;

                    $this->datagrid->insert( $pos + $x, $sumario );

                    $x++;
                    $rodape           = new TTableRow();
                    $cab_fim          = $rodape->addCell( "<hr>" );
                    $cab_fim->colspan = 12;
                    $this->datagrid->insert( $pos + $x, $rodape );

                } else {
                    $pos = $this->datagrid->getRowIndex( 'id', $param[ 'key' ] );

                    $current_row        = $this->datagrid->getRow( $pos );
                    $current_row->style = "background-color: #CDB38B; color:white; text-shadow:none";

                    $row              = new TTableRow();
                    $cab_ini          = $row->addCell( 'Sem Movimento a Listar' );
                    $cab_ini->colspan = 14;
                    $cab_ini->style   = "height:5px;background-color: #F5DEB3;border: 1px solid;border-color:#ff0000;text-align:center;color:#00008B;";

                    $this->datagrid->insert( $pos + 1, $row );

                }
            } catch ( Exception $e ) {
                new TMessage( 'error', '<b>Erro</b> '.$e->getMessage() );
                TTransaction::close();
            } finally {
                TTransaction::close();
            }
        }

        public function onExcluirMovimento( $param ) {
            try {
                TTransaction::open( 'afincco' );
                MovimentoTitulo::find( $param[ 'mov_id' ] )->delete();
                TTransaction::close();
            } catch ( Exception $e ) {
                new TMessage( 'error', '<b>Erro</b> '.$e->getMessage() );
                TTransaction::close();
            } finally {
                $this->onReload(NULL);
            }
        }

    }
