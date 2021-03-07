<?php
    
    use Adianti\Base\TStandardForm;
    use Adianti\Widget\Form\TDate;
    use Adianti\Widget\Form\TEntry;
    use Adianti\Widget\Form\THtmlEditor;
    use Adianti\Widget\Form\TNumeric;
    use Adianti\Widget\Wrapper\TDBCombo;
    
    class caixaForm extends TStandardForm {

        public function __construct( $param ) {
            parent::__construct();

            parent::setTargetContainer( 'adianti_right_panel' );

            $this->setDatabase( 'afincco' );
            $this->setActiveRecord( 'Caixa' );

            $this->form = new BootstrapFormBuilder( 'form_'.__CLASS__ );
            $this->form->setFormTitle( 'Manutenção de Movimento de Caixa' );

            $id                = new TEntry( 'id' );
            $contacorrente_id  = new TDBCombo( 'contacorrente_id', 'afincco', 'Contacorrente', 'id', 'nome', 'nome' );
            $pessoa_id         = new TDBCombo( 'pessoa_id', 'afincco', 'Pessoa', 'id', 'nome', 'nome' );
            $tipolancamento_id = new TDBCombo( 'tipolancamento_id', 'afincco', 'TipoLancamento', 'id', 'nome', 'nome' );
            $data_movimento    = new TDate( 'data_movimento' );
            $dc                = new TCombo( 'dc' );
            $valor             = new TNumeric( 'valor', 2, '.', ',', TRUE );
            $saldo             = new TNumeric( 'saldo', 2, '.', ',', TRUE );
            $compensado        = new TCombo( 'compensado' );
            $controle          = new TEntry( 'controle' );
            $historico         = new THtmlEditor( 'historico' );
            $usuario           = new TEntry( 'usuario' );
            $operacao_id       = new TEntry( 'operacao_id' );

            $id->setEditable( FALSE );
            $controle->setEditable( FALSE );
            $usuario->setEditable( FALSE );
            $saldo->setEditable( FALSE );

            $data_movimento->setMask( 'dd/mm/yyyy' );
            $data_movimento->setDatabaseMask( 'yyyy-mm-dd' );

            $contacorrente_id->addValidation( 'Conta Corrente', new TRequiredValidator );
            $tipolancamento_id->addValidation( 'Tipo de Lançamento', new TRequiredValidator );
            $data_movimento->addValidation( 'Data Movimento', new TRequiredValidator );
            $dc->addValidation( 'D/C', new TRequiredValidator );
            $valor->addValidation( 'Valor', new TRequiredValidator );
            $compensado->addValidation( 'Compensado', new TRequiredValidator );
            $historico->addValidation( 'Histórico', new TRequiredValidator );
            $usuario->addValidation( 'Usuário', new TRequiredValidator );

            $dc->addItems( Utilidades::debito_credito() );
            $compensado->addItems( Utilidades::sim_nao() );

            $historico->setSize( '100%', 200 );

            $id->style               = ( 'text-align:center;color:#ff0000;background-color:#F7F2E0;' );
            $contacorrente_id->style = ( 'background-color: #FFFEEB;' );
            $data_movimento->style   = ( 'background-color: #FFFEEB;text-align:center' );
            $controle->style         = ( 'background-color: #EEEED1;' );
            $usuario->style          = ( 'background-color: #EEEED1;' );
            $operacao_id->style      = ( 'background-color: #FFFEEB;text-align:center;color:#ff0000;' );

            $campo_id             = [ new TLabel( 'Código' ), $id ];
            $campo_contacorrente  = [ new TLabel( 'Conta Corrente' ), $contacorrente_id ];
            $campo_data_movimento = [ new TLabel( 'Data Movimento' ), $data_movimento ];
            $campo_usuario        = [ new TLabel( 'Usuário' ), $usuario ];
            $campo_tipolancamento = [ new TLabel( 'Tipo Lançamento' ), $tipolancamento_id ];
            $campo_dc             = [ new TLabel( 'D/C' ), $dc ];
            $campo_valor          = [ new TLabel( 'Valor' ), $valor ];
            $campo_compensado     = [ new TLabel( 'Compensado' ), $compensado ];
            $campo_cliente        = [ new TLabel( 'Cliente' ), $pessoa_id ];
            $campo_processo       = [ new TLabel( 'Nº Processo' ), $operacao_id ];
            $campo_historico      = [ new TLabel( 'Histórico' ), $historico ];
            $campo_saldo          = [ new TLabel( 'Saldo a Utilizar' ), $saldo ];
            $campo_controle       = [ new TLabel( 'Controle Banco' ), $controle ];

            $row         = $this->form->addFields( $campo_id, $campo_contacorrente, $campo_data_movimento, $campo_usuario );
            $row->layout = [ 'col-md-2', 'col-md-5', 'col-md-2', 'col-md-3' ];

            $row         = $this->form->addFields( $campo_tipolancamento, $campo_dc, $campo_valor, $campo_compensado, $campo_saldo );
            $row->layout = [ 'col-md-4', 'col-md-2', 'col-md-2', 'col-md-2', 'col-md-2' ];

            $row         = $this->form->addFields( $campo_cliente, $campo_processo, $campo_controle );
            $row->layout = [ 'col-md-7', 'col-md-2', 'col-md-3' ];

            $row         = $this->form->addFields( $campo_historico );
            $row->layout = [ 'col-md-12' ];

            /* BOTÕES */
            $this->form->addAction( _t( 'Save' ), new TAction( [ $this, 'onSave' ] ), 'far:save blue' );
            $this->form->addActionLink( _t( 'New' ), new TAction( [ $this, 'onClear' ] ), 'fa:eraser red' );
            $this->form->addActionLink( 'Retornar', new TAction( [ $this,
                                                                   'onFechaRightPanel' ] ), 'fa: fa-times green' );
            /* BOTÕES */

            $pos_action = new TAction( [ 'caixaList', 'onReload' ] );
            self::setAfterSaveAction( $pos_action );

            $container        = new TVBox();
            $container->style = 'width: 100%';
            $container->add( $this->form );

            parent::add( $container );
        }

        public function onClear( $param ) {
            $this->form->clear();
            $dados_caixa           = TSession::getValue( 'Caixa_filter_data' );
            $obj                   = new StdClass();
            $obj->dc               = "C";
            $obj->compensado       = 1;
            $obj->contacorrente_id = $dados_caixa['contacorrente_id'];
            $obj->data_movimento   = date( 'd/m/Y' );
            $obj->usuario          = TSession::getValue( 'login' );
            $this->form->setData( $obj );
        }

    }
