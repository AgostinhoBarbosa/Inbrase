<?php
    
    use Adianti\Base\TStandardForm;
    use Adianti\Control\TAction;
    use Adianti\Database\TCriteria;
    use Adianti\Database\TTransaction;
    use Adianti\Registry\TSession;
    use Adianti\Widget\Dialog\TMessage;
    use Adianti\Widget\Form\TButton;
    use Adianti\Widget\Form\TFieldList;
    
    class comprovanteForm extends TStandardForm
    {
        
        protected $detail_row;
        protected $assunto;
        protected $lista_despesa;
        
        public function __construct( $param ) {
            parent::__construct();
            
            parent::setTargetContainer( 'adianti_right_panel' );
            
            $criteria_seguradora = new TCriteria();
            $filter              = new TFilter( 'seguradora', '=', '1' );
            $criteria_seguradora->add( $filter );
            
            
            $this->form = new BootstrapFormBuilder( 'form_' . __CLASS__ );
            $this->form->setFormTitle( 'Manutenção de Recibos' );
            
            $id             = new TEntry( 'id' );
            $id_processo    = new TEntry( 'id_processo' );
            $id_seg         = new TDBCombo( 'id_seg', 'afincco', 'Pessoa', 'id', 'nome', 'nome', $criteria_seguradora );
            $PlacaVeiculo   = new TEntry( 'PlacaVeiculo' );
            $ValorTotal     = new TNumeric( 'ValorTotal', 2, ',', '.', TRUE );
            $Status         = new TCombo( 'Status' );
            $Data_processo  = new TDateTime( 'Data_processo' );
            $Data_Atualizao = new TDateTime( 'Data_Atualizao' );
            $email          = new TEntry( 'email' );
            
            $id_despesa = new TEntry( 'id_despesa[]' );
            $descricao  = new TEntry( 'descricao[]' );
            $valor      = new TNumeric( 'valor[]', 2, ',', '.' );
            $this->form->addField( $id_despesa );
            $this->form->addField( $descricao );
            $this->form->addField( $valor );
            
            $this->lista_despesa = new TFieldList();
            $this->lista_despesa->addField( '<b>Código</b>', $id_despesa, [ 'width' => '10%' ] );
            $this->lista_despesa->addField( '<b>Descrição</b>', $descricao, [ 'width' => '100%' ] );
            $this->lista_despesa->addField( '<b>Valor</b>', $valor, [ 'width' => '20%', 'sum' => TRUE ] );
            $this->lista_despesa->width = '100%';
            $this->lista_despesa->enableSorting();
            
            $Status->addItems( Utilidades::status_recibo() );
            $id->setEditable( FALSE );
            $ValorTotal->setEditable( FALSE );
            $Data_processo->setEditable( FALSE );
            $Data_Atualizao->setEditable( FALSE );
            
            $Data_processo->setMask( 'dd/mm/yyyy hh:ii' );
            $Data_Atualizao->setMask( 'dd/mm/yyyy hh:ii' );
            $Data_processo->setDatabaseMask( 'yyyy-mm-dd hh:ii' );
            $Data_Atualizao->setDatabaseMask( 'yyyy-mm-dd hh:ii' );
            
            $id->style           .= ';text-align:center;font-weight:bold;';
            $id_processo->style  .= ';text-align:center;color:#ff0000;font-weight:bold;';
            $PlacaVeiculo->style .= ';text-align:center;color:#000080;font-weight:bold;';
            $ValorTotal->style   .= ';color:#FF0000;font-weight:bold;';
            $descricao->style    .= ';width:100%; color:#FF0000;font-weight:bold;';
            
            $id_processo->addValidation( 'Nº Processo', new TRequiredValidator );
            $id_seg->addValidation( 'Seguradora', new TRequiredValidator );
            $PlacaVeiculo->addValidation( 'Placa do Veículo', new TRequiredValidator );
            $Status->addValidation( 'Status do Recibo', new TRequiredValidator );
            
            $campo_comprovante     = [ new TLabel( 'Comprovante' ), $id ];
            $campo_processo        = [ new TLabel( 'Processo' ), $id_processo ];
            $campo_seguradora      = [ new TLabel( 'Seguradora' ), $id_seg ];
            $campo_placa           = [ new TLabel( 'Placa' ), $PlacaVeiculo ];
            $campo_valortotal      = [ new TLabel( 'Valor Total' ), $ValorTotal ];
            $campo_status          = [ new TLabel( 'Status' ), $Status ];
            $campo_dataprocesso    = [ new TLabel( 'Data Processo' ), $Data_processo ];
            $campo_dataatualizacao = [ new TLabel( 'Data Atualização' ), $Data_Atualizao ];
            $campo_email           = [ new TLabel( 'Dados E-Mail para envio' ), $email ];
            
            $row         = $this->form->addFields( $campo_comprovante, $campo_processo, $campo_seguradora, $campo_placa );
            $row->layout = [ 'col-md-2', 'col-md-2', 'col-md-6', 'col-md-2' ];
            
            $row         = $this->form->addFields( $campo_valortotal, $campo_status, $campo_dataprocesso, $campo_dataatualizacao );
            $row->layout = [ 'col-md-2', 'col-md-2', 'col-md-2', 'col-md-2' ];
            
            $this->form->addFields( $campo_email );
            
            $this->form->addFields( [ new TFormSeparator( 'Despesas' ) ] );
            $this->form->addFields( [ $this->lista_despesa ] );
            
            /* BOTÕES */
            $this->form->addAction( _t( 'Save' ), new TAction( [ $this, 'onSave' ] ), 'far:save blue' );
            $this->form->addAction( _t( 'New' ), new TAction( [ $this, 'onClear' ] ), 'fa:eraser red' );
            $this->form->addAction( 'Enviar E-Mail', new TAction( [ $this, 'onEmail' ] ), 'far:envelope green' );
            $this->form->addAction( 'Imprimir', new TAction( [ $this, 'onImprimir' ] ), 'fas:print blue' );
            $this->form->addActionLink( 'Retornar', new TAction( [ $this, 'onFechaRightPanel' ] ), 'fa: fa-times green' );
            /* BOTÕES */
            
            if ( TSession::getValue( 'chamador_comprovante' ) ) {
                $form_chamador  = TSession::getValue( 'chamador_comprovante' );
                $data           = TSession::getValue( 'Processoa_data' );
                $param          = [];
                $param[ 'key' ] = $data->id;
                $param[ 'id' ]  = $data->id;
                $pos_action     = new TAction( [ $form_chamador, 'onEdit' ], [ 'key' => $data->id, 'id' => $data->id ] );
            } else {
                $form_chamador = 'comprovanteList';
                $pos_action    = new TAction( [ $form_chamador, 'onReload' ] );
            }
            
            self::setAfterSaveAction( $pos_action );
            
            $container        = new TVBox;
            $container->style = 'width: 100%';
            $container->add( $this->form );
            
            parent::add( $container );
        }
        
        public static function onUpdateTotal( $param ) {
            $object             = new StdClass();
            $object->ValorTotal = 0;
            foreach ( $param[ 'despesa_valor' ] as $obj ) {
                $valor = Utilidades::Valor( $obj );
                if ( is_numeric( $valor ) ) {
                    $object->ValorTotal += $valor;
                }
            }
            $object->ValorTotal = number_format( $object->ValorTotal, 2, ',', '.' );
            TForm::sendData( 'form_' . __CLASS__, $object );
        }
        
        public static function onImprimir( $param ) {
            try {
                TTransaction::open( 'afincco' );
                $object = Comprovante::find( $param[ 'id' ] );
                if ( $object ) {
                    $relatorio = new imprimirComprovante( $object );
                    $relatorio->gerarComprovante();
                    if ( $relatorio ) {
                        parent::openFile( $relatorio->get_arquivo() );
                    }
                }
            } catch ( Exception $e ) {
                new TMessage( 'Erro', '<b>Error:</b> ' . $e->getMessage() );
            } finally {
                TTransaction::close();
            }
        }
        
        public function onEmail( $param ) {
            try {
                $data = $this->form->getData();
                $this->form->setData( $data );
                $dados_email = NULL;
                $key         = $param[ 'id' ];
                $email       = $param[ 'email' ];
                if ( empty( $email ) ) {
                    $email = 'afincco@afincco.com.br';
                }
                
                $email = 'indenizados@afincco.com.br';
                
                TTransaction::open( 'permission' );
                
                $preferences = SystemPreference::getAllPreferences();
                if ( $preferences ) {
                    $dados_email = ( (object) $preferences );
                } else {
                    new TMessage( 'error', 'Erro ao ler os dados para envio de email' );
                    exit;
                }
                
                TTransaction::close();
                
                TTransaction::open( 'afincco' );
                
                $texto = Textos::find( 1 );
                
                $comprovante = Comprovante::find( $key );
                
                $this->assunto = "SOLICITAÇÃO DE DESPESAS - SINISTRO " . $comprovante->processo->sinistro;
                $this->assunto .= " - PLACA " . $comprovante->processo->placa;
                $this->assunto .= " - CHASSI " . $comprovante->processo->chassi;
                $this->assunto .= " - " . $comprovante->processo->marca_modelo;
                $this->assunto .= " - SEGURADORA " . $comprovante->seguradora->nome;
                
                $mail_template = $texto->texto;
                
                $mail_template = str_replace( '[PROCESSO]', $comprovante->processo->id, $mail_template );
                $mail_template = str_replace( '[SEGURADORA]', $comprovante->seguradora->nome, $mail_template );
                $mail_template = str_replace( '[MARCA_MODELO]', $comprovante->processo->marca_modelo, $mail_template );
                $mail_template = str_replace( '[SINISTRO]', $comprovante->processo->sinistro, $mail_template );
                $mail_template = str_replace( '[PLACA]', $comprovante->PlacaVeiculo, $mail_template );
                $mail_template = str_replace( '[CHASSI]', $comprovante->processo->chassi, $mail_template );
                $mail_template = str_replace( '[MOTOR]', $comprovante->processo->motor, $mail_template );
                $mail_template = str_replace( '[TIPO_SERVICO_DEC]', $comprovante->processo->tipo_servico->nome, $mail_template );
                $mail_template = str_replace( '[ANO_FAB]', $comprovante->processo->ano, $mail_template );
                $cond_chassi   = Utilidades::condicao_chassi_motor()[ $comprovante->processo->condChassi ];
                $mail_template = str_replace( '[CONDCHASSI]', $cond_chassi, $mail_template );
                $cond_motor    = Utilidades::condicao_chassi_motor()[ $comprovante->processo->condMotor ];
                $mail_template = str_replace( '[CONDMOTOR]', $cond_motor, $mail_template );
                $mail_template = str_replace( '[ESTADO_VEICULO]', Utilidades::estado_bem()[ $comprovante->processo->restricao02 ], $mail_template );
                $mail_template = str_replace( '[DELEGACIA]', $comprovante->processo->dp_dev, $mail_template );
                $mail_template = str_replace( '[DELEGACIA_TELEFONE]', $comprovante->processo->telefone_dev, $mail_template );
                $mail_template = str_replace( '[RESPONSAVEL]', $comprovante->processo->responsavel_dev, $mail_template );
                $local_entrega = $comprovante->processo->cidade_dev . "(" . $comprovante->processo->uf_dev . ")";
                $mail_template = str_replace( '[PATIO]', $local_entrega, $mail_template );
                $data_rec      = TDate::date2br( $comprovante->processo->data_rec );
                $data_lib      = TDate::date2br( $comprovante->processo->data_entrega_dev );
                if ( $data_lib === '00/00/0000' ) {
                    $data_lib = '';
                }
                $mail_template = str_replace( '[DATA_REC]', $data_rec, $mail_template );
                $mail_template = str_replace( '[DATA_ENTREGA_DEV]', $data_lib, $mail_template );
                $mail_template = str_replace( '[LOCAL_ENTREGA_DEV]', $comprovante->processo->local_entrega_dev, $mail_template );
                $cidade_dev    = $comprovante->processo->cidade_dev;
                if ( strlen( $comprovante->processo->uf_dev ) > 0 ) {
                    $cidade_dev .= '(' . $comprovante->processo->uf_dev . ')';
                }
                $mail_template = str_replace( '[CIDADE_DEV]', $cidade_dev, $mail_template );
                $mail_template = str_replace( '[TELEFONE_DEV]', $comprovante->processo->telefone_dev, $mail_template );
                $mail_template = str_replace( '[DP_DEV]', $comprovante->processo->dp_dev, $mail_template );
                
                $despesas = "<table class='table table-bordered' style='width:100%;border-collapse:collapse;'>";
                $despesas .= "<tbody>";
                $despesas .= "    <tr>";
                $despesas .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:600px !important;'>DESCRIMINAÇÃO</th>";
                $despesas .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:200px !important;'>VALOR</th>";
                $despesas .= "    </tr>";
                
                if ( $comprovante->despesa ) {
                    $despesas .= $comprovante->despesa->observacao;
                }
                $despesas .= "    <tr>";
                $despesas .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:600px !important;'>TOTAL DAS DESPESAS</th>";
                $despesas .= "        <th style='text-align: right;background-color: rgb(255, 231, 206);border:1px solid;width:200px !important;'>" . number_format(
                        $comprovante->ValorTotal, 2, ',', '.'
                    ) . "</th>";
                $despesas .= "    </tr>";
                
                $despesas      .= "</tbody>";
                $despesas      .= "</table>";
                $mail_template = str_replace( '[DESPESAS]', $despesas, $mail_template );
                $mail_template = str_replace( '[TOTAL_DESPESAS]', number_format( $comprovante->ValorTotal, 2, ',', '.' ), $mail_template );
                
                $mail = new TMail;
                $mail->setDebug( FALSE );
                $mail->setFrom( $dados_email->mail_from, $dados_email->mail_from );
                $mail->addBCC( TSession::getValue( 'usermail' ), TSession::getValue( 'login' ) );
                $mail->setSubject( $this->assunto );
                $mail->setHtmlBody( $mail_template );
                
                $mail->addAddress( $email );
                $mail->addAddress( 'indenizados@afincco.com.br' );
                
                $mail->SetUseSmtp( TRUE );
                $mail->SetSmtpHost( $dados_email->smtp_host, $dados_email->smtp_port );
                $mail->SetSmtpUser( $dados_email->smtp_user, $dados_email->smtp_pass );
                
                $mail->send();
                new TMessage( 'info', 'E-Mail enviado com sucesso!!!' );
                
                TTransaction::close();
                $param          = [];
                $param[ 'key' ] = $key;
                $this->onEdit( $param );
            } catch ( Exception $e ) {
                new TMessage( 'error', '<b>Erro Leitura: </b> ' . $e->getMessage() );
                TTransaction::rollback();
            }
        }
        
        public function onClear( $param ) {
            $key_processo  = 0;
            $data_processo = "";
            $placa         = "";
            if ( isset( $param[ 'id_processo' ] ) ) {
                $key_processo  = $param[ 'id_processo' ];
                $data_processo = $param[ 'Data_processo' ];
                $placa         = $param[ 'PlacaVeiculo' ];
            } else {
                if ( isset( $param[ 'id' ] ) ) {
                    $key_processo  = $param[ 'id' ];
                    $data_processo = $param[ 'data_cadastro' ];
                    $placa         = $param[ 'placa' ];
                }
            }
            if ( $key_processo > 0 ) {
                $objeto                = new StdClass();
                $objeto->id_processo   = $key_processo;
                $objeto->id_seg        = $param[ 'id_seg' ];
                $objeto->PlacaVeiculo  = $placa;
                $objeto->Status        = 'Ativo';
                $objeto->Data_processo = TDateTime::convertToMask( $data_processo, 'dd/mm/yyyy hh:ii', 'yyyy-mm-dd hh:ii' );
                $this->form->setData( $objeto );
            }
            $this->lista_despesa->addHeader();
            $this->lista_despesa->addDetail( new stdClass );
            $this->lista_despesa->addCloneAction();
        }
        
        public function onEdit( $param ) {
            try {
                if ( isset( $param[ 'key' ] ) ) {
                    $key = $param[ 'key' ];
                    TTransaction::open( 'afincco' );
                    
                    $object = new Comprovante( $key );
                    
                    if ( $object->onVerificaTitulo() ) {
                        new TMessage( 'error', 'Recibo já integrado ao financeiro<br> ele não pode ser mais alterado... ' );
                        TButton::disableField( __CLASS__, 'btn_salvar' );
                    }
                    
                    if ( $object->Data_processo === NULL || $object->Data_processo === '0000-00-00 00:00:00' ) {
                        $object->Data_processo = $object->get_processo()->data_cadastro;
                        $object->store();
                    }
                    
                    $object->email = $object->get_seguradora()->email;
                    
                    $this->form->setData( $object );
                    
                    $despesa = $object->get_despesa();
                    
                    $this->lista_despesa->addHeader();
                    if ( $despesa ) {
                        foreach ( $despesa as $item ) {
                            $item->id_despesa = $item->id;
                            $this->lista_despesa->addDetail( $item );
                        }
                    }
                    if ( $object->onVerificaTitulo() ) {
                        TButton::disableField( 'form_' . __CLASS__, 'btn_salvar' );
                    }
                    
                    TTransaction::close();
                } else {
                    $this->onClear( $param );
                }
            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();
            }
        }
        
        public function onSave() {
            try {
                TTransaction::open( 'afincco' );
                
                $this->form->validate();
                
                $object = new Comprovante;
                $data   = $this->form->getData();
                
                $object->fromArray( (array) $data );
                $object->ValorTotal = 0;
                $object->store();
                $param              = $_POST;
                
                //$sale_items = SaleItem::where( 'sale_id', '=', $sale->id )->delete();
                
                if ( !empty( $param[ 'id_despesa' ] ) and is_array( $param[ 'id_despesa' ] ) ) {
                    foreach ( $param[ 'id_despesa' ] as $row => $id_despesa ) {
                        if ( $id_despesa ) {
                            $item                 = new Despesa();
                            $item->id             = $id_despesa;
                            $item->descricao      = $param[ 'descricao' ][ $row ];
                            $item->valor          = (float) str_replace( [ '.', ',' ], [ '', '.' ], $param[ 'valor' ][ $row ] );
                            $total                += $item->valor;
                            $item->id_comprovante = $object->id;
                            $item->store();
                        }
                    }
                }
                $object->store();
                
                if ( empty( $data->id ) ) {
                    $status = Hstatus::where( 'id_processo', '=', $object->id_processo )
                                     ->where( 'id_status', '=', '37' )->count();
                    if ( $status == 0 ) {
                        $status                = new Hstatus();
                        $status->id_status     = 12;
                        $status->id_processo   = $object->id_processo;
                        $status->representante = TSession::getValue( 'login' );
                        $status->data_cadastro = date( 'Y-m-d H:i:s' );
                        $status->store();
                    }
                }
                
                TTransaction::close();
                
                new TMessage( 'info', TAdiantiCoreTranslator::translate( 'Record saved' ), $this->afterSaveAction );
                if ( empty( $this->afterSaveAction ) ) {
                    $param = [];
                    $param['key'] = $object->id;
                    $this->onEdit($param);
                }
            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );
                $this->form->setData( $data );
                TTransaction::rollback();
            }
        }
        
        public function addDetailRow( $item ) {
            $uniqid = mt_rand( 1000000, 9999999 );
            
            $despesa_nome               = new TEntry( 'despesa_nome[]' );
            $despesa_nome->{'data-row'} = $this->detail_row;
            $despesa_nome->setId( 'despesa_nome_' . $uniqid );
            $despesa_nome->setSize( '100%' );
            
            if ( !empty( $item->despesa_nome ) ) {
                $despesa_nome->setValue( $item->despesa_nome );
            }
            
            $despesa_valor               = new TEntry( 'despesa_valor[]' );
            $despesa_valor->{'data-row'} = $this->detail_row;
            $despesa_valor->setId( 'despesa_valor_' . $uniqid );
            $despesa_valor->setNumericMask( 2, ',', '.', TRUE );
            $despesa_valor->setSize( '100%' );
            $despesa_valor->setExitAction( new TAction( [ $this, 'onUpdateTotal' ] ) );
            if ( !empty( $item->despesa_valor ) ) {
                $despesa_valor->setValue( $item->despesa_valor );
            }
            
            $del                            = new TImage( 'far:trash-alt red fa-lg' );
            $del->onclick                   = 'ttable_remove_row(this)';
            $del->style                     = 'text-align:center;';
            $row                            = $this->table_items->addRowSet( $despesa_nome, $despesa_valor, $del );
            $row->{'data-row'}              = $this->detail_row;
            $row->getChildren()[ 0 ]->style = 'width: 800px';
            $row->getChildren()[ 2 ]->style = 'text-align:center;';
            
            $this->form->addField( $despesa_nome );
            $this->form->addField( $despesa_valor );
            
            $this->detail_row++;
        }
    }
