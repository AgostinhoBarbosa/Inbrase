<?php
    
    use Adianti\Base\TStandardForm;
    use Adianti\Control\TAction;
    use Adianti\Core\AdiantiCoreApplication;
    use Adianti\Database\TTransaction;
    use Adianti\Registry\TSession;
    use Adianti\Validator\TMinValueValidator;
    use Adianti\Widget\Datagrid\TDataGrid;
    use Adianti\Widget\Dialog\TMessage;
    use Adianti\Widget\Form\TButton;
    use Adianti\Widget\Form\TCombo;
    use Adianti\Widget\Form\TDate;
    use Adianti\Widget\Form\TEntry;
    use Adianti\Widget\Form\THidden;
    use Adianti\Widget\Form\TNumeric;
    use Adianti\Widget\Wrapper\TDBCombo;
    use Adianti\Wrapper\BootstrapDatagridWrapper;
    use Adianti\Wrapper\BootstrapFormBuilder;
    
    class recibosForm extends TStandardForm
    {
        protected $datagrid;

        function __construct()
        {
            parent::__construct();

            parent::setTargetContainer( 'adianti_right_panel' );

            $this->form = new BootstrapFormBuilder('form_'.__CLASS__);
            $this->form->setFormTitle('Recibo Prestador - Manutenção');

            $id           = new TEntry('id');
            $pessoa_id    = new TDBCombo('pessoa_id', 'afincco', 'Pessoa', 'id', 'nome', 'nome');
            $processo_id  = new TEntry('processo_id');
            $data_emissao = new TDate('data_emissao');
            $valor_recibo = new TNumeric('valor_recibo', 2, ',', '.', TRUE);
            $status       = new TCombo('status');
            $id_detalhe   = new THidden('id_detalhe');
            $nome         = new TEntry('nome');
            $valor        = new TNumeric('valor', 2, ',', '.', TRUE);

            $valor->addValidation('Valor', new TMinValueValidator(), 10.00);
            $valor_recibo->addValidation('Valor Recibo', new TMinValueValidator(), 10.00);

            $data_emissao->setMask('dd/mm/yyyy');
            $data_emissao->setDatabaseMask('yyyy-mm-dd');

            $id->setEditable(FALSE);
            $valor_recibo->setEditable(FALSE);
            $status->addItems(Utilidades::status_recibo());
            
            $processo_id->style  .= ';text-align:center;font-weight:bold;color:red';
            $valor_recibo->style .= ';font-weight:bold;color:red;font-size:20px !important;';

            $validaProcesso = new TAction(array($this, 'onValidaProcesso'));
            $processo_id->setExitAction($validaProcesso);

            $campo_codigo       = [new TLabel('Código'), $id];
            $campo_prestador    = [new TLabel('Prestador'), $pessoa_id];
            $campo_processo     = [new TLabel('Processo'), $processo_id];
            $campo_data_emissao = [new TLabel('Data Emissão'), $data_emissao];
            $campo_valor        = [new TLabel('Valor Recibo'), $valor_recibo];
            $campo_status       = [new TLabel('Status'), $status];
            $campo_det_valor    = [new TLabel('Valor'), $valor, $id_detalhe];
            $campo_det_nome     = [new TLabel('Descrição'), $nome];

            $label1        = new TLabel('Dados do Recibo', '#7D78B6', 12, 'bi');
            $label1->style = 'text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
            $this->form->addContent([$label1]);

            $row         = $this->form->addFields($campo_codigo, $campo_data_emissao, $campo_processo, $campo_prestador);
            $row->layout = ['col-md-2', 'col-md-2', 'col-md-2', 'col-md-6'];

            $row         = $this->form->addFields($campo_status, $campo_valor);
            $row->layout = ['col-md-2', 'col-md-4'];

            $label2        = new TLabel('Itens do Recibo', '#7D78B6', 12, 'bi');
            $label2->style = 'text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
            $this->form->addContent([$label2]);

            $add_recibo        = TButton::create('add_recibo', [$this, 'onReciboAdd'], 'Registrar', 'fa:save red');
            $add_recibo->style = 'margin-top:25px;margin-left: -16px;';

            $this->form->addFields($campo_det_nome, $campo_det_valor, [$add_recibo])->layout = ['col-md-8', 'col-md-2', 'col-md-2'];

            $this->datagrid            = new BootstrapDatagridWrapper(new TDataGrid);

            $column_id    = new TDataGridColumn('id', 'Código', 'center', '5%');
            $column_nome  = new TDataGridColumn('nome', 'Descrição', 'left');
            $column_valor = new TDataGridColumn('valor', 'Valor', 'right', '20%');

            $this->datagrid->addColumn($column_id);
            $this->datagrid->addColumn($column_nome);
            $this->datagrid->addColumn($column_valor);

            $column_valor->setTransformer(
                function( $value, $object, $row ) {
                    if ( is_numeric($value) ) {
                        return 'R$ '.number_format($value, 2, ',', '.');
                    }

                    return $value;
                }
            );

            $column_valor->setTotalFunction(
                function( $values ) {
                    return array_sum((array) $values);
                }
            );

            $action_edit = new TDataGridAction( [$this, 'onEditRecibo'], ['key' => '{id}', 'register_state' => 'false'] );
            $action_del  = new TDataGridAction( [$this, 'onDeleteRecibo'], ['key' => '{id}', 'register_state' => 'false'] );

            $this->datagrid->addAction( $action_edit, _t( 'Edit' ), 'far:edit blue fa-lg' );
            $this->datagrid->addAction( $action_del, _t( 'Delete' ), 'far:trash-alt red fa-lg' );

            $this->datagrid->createModel();

            /* BOTÕES */
            $this->form->addAction( _t( 'Save' ), new TAction( [ $this, 'onSave' ] ), 'far:save blue' );
            $this->form->addActionLink( _t( 'New' ), new TAction( [ $this, 'onClear' ] ), 'fa:eraser red' );
            $this->form->addAction('Imprimir', new TAction(array($this, 'onImprimir')), 'fas:print blue');
            $this->form->addActionLink( 'Retornar', new TAction( [ $this,
                                                                   'onFechaRightPanel' ] ), 'fa: fa-times green' );
            /* BOTÕES */

            if (TSession::getValue('chamador_recibos')) {
                $form_chamador = TSession::getValue('chamador_recibos');
                $data          = TSession::getValue('Processoa_data');
                $param         = [];
                $param['key']  = $id;
                $param['id']  = $id;
                $pos_action = new TAction( [ $form_chamador, 'onEdit' ], ['key'  => $id, 'id' => $id]);
            }else{
                $form_chamador = 'recibosList';
                $pos_action = new TAction( [ $form_chamador, 'onReload' ] );
            }

            self::setAfterSaveAction( $pos_action );

            $this->form->addContent([$this->datagrid]);

            $container        = new TVBox();
            $container->style = 'width: 100%';
            $container->add( $this->form );

            parent::add( $container );
        }

        public static function onImprimir( $param )
        {
            try {
                TTransaction::open('afincco');
                $object = Recibos::find($param[ 'id' ]);
                if ( $object ) {
                    $relatorio = new imprimirRecibo($object);
                    $relatorio->gerarRecibos();
                    if ( $relatorio ) {
                        parent::openFile($relatorio->get_arquivo());
                    }
                }
            } catch ( Exception $e ) {
                new TMessage('Erro', '<b>Error:</b> '.$e->getMessage());
                TTransaction::rollback();
            } finally {
                TTransaction::close();
            }
        }

        public static function onValidaProcesso( $param )
        {
            try {
                TTransaction::open('afincco');
                if ( isset($param[ 'processo_id' ]) and $param[ 'processo_id' ] ) {
                    $objeto = Processo::find( $param[ 'processo_id' ]);
                    if ( isset($objeto->liberador) ) {
                        $obj            = new stdClass();
                        $obj->pessoa_id = $objeto->liberador;
                        TForm::sendData('form_'.__CLASS__, $obj);
                        unset($obj);
                    } else {
                        $obj            = new stdClass();
                        $obj->pessoa_id = NULL;
                        TForm::sendData('form_'.__CLASS__, $obj);
                        unset($obj);
                        new TMessage('error', 'Erro ao buscar liberador no processo, <br> ou processo não existe.');
                    }
                }
            } catch ( Exception $e ) {
                new TMessage('error', '<b>Error:</b> '.$e->getMessage());
                TTransaction::rollback();
            } finally {
                TTransaction::close();
            }
        }

        public static function onEditRecibo( $param )
        {
            $recibos = TSession::getValue('recibo_valores');

            $detalhe = $recibos[ (int) $param[ 'id' ] ];

            $retorno             = new StdClass();
            $retorno->id_detalhe = $detalhe[ 'id' ];
            $retorno->nome       = $detalhe[ 'nome' ];
            $retorno->valor      = number_format($detalhe[ 'valor' ], 2, ',', '.');
            TForm::sendData('form_'.__CLASS__, $retorno);
        }

        public static function onDeleteRecibo( $param )
        {
            try {
                TTransaction::open('afincco');
                $data    = new StdClass();
                $recibos = TSession::getValue('recibo_valores');

                $key = (int) $param[ 'key' ];
                if ( !empty($key) ) {
                    $detalhe        = Recibodetalhe::find($key);
                    $param[ 'key' ] = $detalhe->recibo_id;
                    $detalhe->delete();
                    unset($recibos[ $key ]);
                    TSession::setValue('recibo_valores', $recibos);
                }
                unset($param[ 'static' ]);
                AdiantiCoreApplication::loadPage('recibosForm', 'onEdit', $param);
            } catch ( Exception $e ) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            } finally {
                TTransaction::close();
            }
        }

        public function onSave( )
        {
            try {
                TTransaction::open('afincco');

                $this->form->validate();

                $data = $this->form->getData('Recibos');
                $data->store();

                $this->form->setData($data);

                $param[ 'key' ] = $data->id;
                $this->onEmail(( $param ));

                new TMessage( 'info', TAdiantiCoreTranslator::translate( 'Record saved' ),$this->afterSaveAction );
                if ( !empty( $this->afterSaveAction ) ) {
                    $this->onFechaRightPanel();
                }

            } catch ( Exception $e )
            {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            } finally {
                TTransaction::close();
            }

        }

        function onEdit( $param )
        {
            try {
                TSession::delValue('recibos_valores');
                TTransaction::open('afincco');

                $recibos_valores = [];

                if ( isset($param[ 'key' ]) ) {
                    $key = $param[ 'key' ];

                    $object = new Recibos($key);

                    $items                = $object->get_recibodetalhe();
                    $object->valor_recibo = 0;

                    if ( $items ) {
                        foreach ( $items as $item ) {
                            $recibos_valores[ $item->id ] = $item->toArray();
                            $object->valor_recibo        += $item->valor;

                        }

                        TSession::setValue('recibos_valores', $recibos_valores);
                    }
                    TEntry::disableField('form_'.__CLASS__, 'processo_id');
                    TDBCombo::disableField('form_'.__CLASS__, 'pessoa_id');
                    if ($object->onVerificaTitulo()) {
                        TButton::disableField('form_'.__CLASS__,'btn_salvar');
                    }
                } else {
                    $this->onClear($param);
                    $object               = new Recibos();
                    $object->processo_id  = $param[ 'processo_id' ];
                    $object->pessoa_id    = $object->get_processo()->liberador;
                    $object->data_emissao = date('d/m/Y');
                    $object->status       =  'Ativo';
                    TEntry::disableField('form_recibos', 'processo_id');
                    TDBCombo::disableField('form_recibos', 'pessoa_id');
                }
                $this->form->setData($object);

                $this->onMontaGrid($param);

            } catch ( Exception $e )
            {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            } finally {
                TTransaction::close();
            }
        }

        public function onClear( $param )
        {
            $this->form->clear(TRUE);
            TSession::delValue('recibos_valores');
        }

        public function onMontaGrid( $param )
        {
            $recibos = TSession::getValue('recibos_valores');
            $this->datagrid->clear();
            if ( $recibos ) {
                foreach ( $recibos as $cod_recibo => $recibo ) {
                    $item            = new StdClass;
                    $item->id        = $recibo[ 'id' ];
                    $item->recibo_id = $recibo[ 'recibo_id' ];
                    $item->nome      = $recibo[ 'nome' ];
                    $item->valor     = $recibo[ 'valor' ];

                    $row              = $this->datagrid->addItem($item);
                    $row->onmouseover = '';
                    $row->onmouseout  = '';
                }
            }
        }

        public function onReciboAdd( $param )
        {
            try {
                TTransaction::open('afincco');
                $object = $this->form->getData('Recibos');
                $data   = $this->form->getData();
                if ( empty($object->id) ) {
                    $object->valor_recibo = 0;
                    $object->store();
                    $data->id = $object->id;
                }
                $recibos = TSession::getValue('recibos_valores');

                $key = (int) $data->id_detalhe;
                if ( empty($key) ) {
                    $detalhe            = new Recibodetalhe();
                    $detalhe->recibo_id = $object->id;
                    $detalhe->nome      = $data->nome;
                    $detalhe->valor     = $data->valor;
                    $detalhe->store();
                    $key                = $detalhe->id;
                }else {
                    $detalhe = Recibodetalhe::find($key);
                    $detalhe->nome      = $data->nome;
                    $detalhe->valor     = $data->valor;
                    $detalhe->store();
                }


                $recibos[ $key ] = [
                    'id'        => $key,
                    'recibo_id' => $data->id,
                    'nome'      => $data->nome,
                    'valor'     => $data->valor
                ];

                TSession::setValue('recibos_valores', $recibos);

                $data->valor_recibo = 0;
                $recibos = (object) $recibos;
                foreach ( $recibos as $rec ) {
                    $data->valor_recibo += $rec['valor'];
                }


                $object->valor_recibo = $data->valor_recibo;
                $object->store();

                $data->id_detalhe = '';
                $data->nome       = '';
                $data->valor      = '';
                $this->form->setData($data);

                $this->onMontaGrid($param);
            } catch ( Exception $e ) {
                $this->form->setData($data);
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            } finally {
                TTransaction::close();
            }
        }

        public function onEmail( $param ) {
            try {
                $data = $this->form->getData();
                $this->form->setData( $data );
                $dados_email = NULL;
                $key               = $param[ 'key' ];

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

                $comprovante = Recibos::find( $key );

                $this->assunto = "RECIBO PRESTADOR - SINISTRO ".$comprovante->processo->sinistro;
                $this->assunto .= " - PLACA ".$comprovante->processo->placa;
                $this->assunto .= " - CHASSI ".$comprovante->processo->chassi;
                $this->assunto .= " - ".$comprovante->processo->marca_modelo;
                $this->assunto .= " - SEGURADORA ".$comprovante->processo->seguradoras->nome;

                $mail_template = $texto->texto;
    
                $mail_template = str_replace( '[PROCESSO]', $comprovante->processo->id, $mail_template );
                $mail_template = str_replace( '[TIPO_SERVICO_DEC]', $comprovante->processo->tipo_servico->nome, $mail_template );
                $mail_template = str_replace( '[LOCAL_ENTREGA_DEV]', $comprovante->processo->local_entrega_dev, $mail_template );
                $mail_template = str_replace( '[SEGURADORA]', $comprovante->processo->seguradoras->nome, $mail_template );
                $mail_template = str_replace( '[MARCA_MODELO]', $comprovante->processo->marca_modelo, $mail_template );
                $mail_template = str_replace( '[SINISTRO]', $comprovante->processo->sinistro, $mail_template );
                $mail_template = str_replace( '[PLACA]', $comprovante->processo->placa, $mail_template );
                $mail_template = str_replace( '[AO_FAB]', $comprovante->processo->ano, $mail_template );
                $mail_template = str_replace( '[CHASSI]', $comprovante->processo->chassi, $mail_template );
                $mail_template = str_replace( '[ANO_VEICULO]', $comprovante->processo->ano, $mail_template );
                $cond_chassi   = Utilidades::condicao_chassi_motor()[ $comprovante->processo->condChassi ];
                $mail_template = str_replace( '[CONDCHASSI]', $cond_chassi, $mail_template );
                $cond_motor    = Utilidades::condicao_chassi_motor()[ $comprovante->processo->condMotor ];
                $mail_template = str_replace( '[CONDMOTOR]', $cond_motor, $mail_template );
                $mail_template = str_replace( '[MOTOR]', $comprovante->processo->motor, $mail_template );
                $mail_template = str_replace( '[ESTADO_VEICULO]', Utilidades::estado_bem()[ $comprovante->processo->restricao02 ], $mail_template );
                $mail_template = str_replace( '[DP_DEV]', $comprovante->processo->dp_dev, $mail_template );
                $cidade_dev    = $comprovante->processo->cidade_dev;
                if (strlen($comprovante->processo->uf_dev) > 0) {
                    $cidade_dev .= '('.$comprovante->processo->uf_dev.')';
                }
                $mail_template = str_replace( '[CIDADE_DEV]', $cidade_dev, $mail_template );
                
                $mail_template = str_replace( '[TELEFONE_DEV]', $comprovante->processo->telefone_dev, $mail_template );
                $mail_template = str_replace( '[RESPONSAVEL]', $comprovante->processo->responsavel_dev, $mail_template );
                $local_entrega = $comprovante->processo->cidade_dev."(".$comprovante->processo->uf_dev.")";
                $mail_template = str_replace( '[PATIO]', $local_entrega, $mail_template );
                $data_rec      = TDate::date2br( $comprovante->processo->data_rec );
                $data_lib      = TDate::date2br( $comprovante->processo->data_entrega_dev );
                if ( $data_lib === '00/00/0000' ) {
                    $data_lib = '';
                }
                $mail_template = str_replace( '[DATA_REC]', $data_rec, $mail_template );
                $mail_template = str_replace( '[DATA_ENTREGA_DEV]', $data_lib, $mail_template );

                $despesas = "<table class='table table-bordered' style='width:100%;border-collapse:collapse;'>";
                $despesas .= "<tbody>";
                $despesas .= "    <tr>";
                $despesas .= "        <td></td>";
                $despesas .= "    </tr>";
                $despesas .= "    <tr>";
                $despesas .= "        <td><b>Prestador: </b>".$comprovante->pessoa->nome."</td>";
                $despesas .= "    </tr>";

                $despesas .= "    <tr>";
                $despesas .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:600px !important;'>DESCRIMINAÇÃO</th>";
                $despesas .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:200px !important;'>VALOR</th>";
                $despesas .= "    </tr>";

                $detalhes = $comprovante->recibodetalhe;
                foreach ($detalhes as $dados){
                    $despesas .= "<tr>";
                    $despesas .= "   <td style='width:200px !important;border:1px solid;font-wight:bold;'>".Utilidades::converte_string($dados->nome)."</td>";
                    $despesas .= "   <td style='width:100px !important;border:1px solid;text-align:right;'>".number_format(floatval($dados->valor),2,',','.')."</td>";
                    $despesas .= "</tr>";

                }
                $despesas .= "    <tr>";
                $despesas .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:600px !important;'>TOTAL DAS DESPESAS</th>";
                $despesas .= "        <th style='text-align: right;background-color: rgb(255, 231, 206);border:1px solid;width:200px !important;'>".number_format(
                        $comprovante->valor_recibo, 2, ',', '.'
                    )."</th>";
                $despesas .= "    </tr>";

                $despesas      .= "</tbody>";
                $despesas      .= "</table>";
                $mail_template = str_replace( '[DESPESAS]', $despesas, $mail_template );
                $mail_template = str_replace( '[TOTAL_DESPESAS]', number_format( $comprovante->valor_recibo, 2, ',', '.' ), $mail_template );

                $mail = new TMail;
                $mail->setDebug( FALSE );
                $mail->setFrom( $dados_email->mail_from, $dados_email->mail_from );
                $mail->setSubject( $this->assunto );
                $mail->setHtmlBody( $mail_template );

                $mail->addAddress( 'indenizados@afincco.com.br' );
                $mail->addAddress( 'financeiro@afincco.com.br' );
                $mail->addAddress( 'administrativo@afincco.com.br' );

                $mail->SetUseSmtp( TRUE );
                $mail->SetSmtpHost( $dados_email->smtp_host, $dados_email->smtp_port );
                $mail->SetSmtpUser( $dados_email->smtp_user, $dados_email->smtp_pass );

                $mail->send();

                $param          = array();
                $param[ 'key' ] = $key;
                $this->onEdit( $param );
            } catch ( Exception $e )
            {
                new TMessage( 'error', '<b>Erro Leitura: </b> '.$e->getMessage() );

                TTransaction::rollback();
            } finally {
                TTransaction::close();
            }

        }

    }
