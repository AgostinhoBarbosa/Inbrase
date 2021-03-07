<?php
    
    use Adianti\Base\TStandardForm;
    use Adianti\Widget\Base\TScript;
    use Adianti\Widget\Form\TCombo;
    use Adianti\Widget\Form\TDate;
    use Adianti\Widget\Form\TEntry;
    use Adianti\Widget\Form\TNumeric;
    
    class contacorrenteForm extends TStandardForm {

        function __construct() {

            parent::__construct();

            parent::setTargetContainer( 'adianti_right_panel' );

            $this->setDatabase( 'afincco' );
            $this->setActiveRecord( 'Contacorrente' );

            $this->form = new BootstrapFormBuilder( 'form_'.__CLASS__ );
            $this->form->setFormTitle( 'Manutenção de Conta Corrente' );

            $id              = new TEntry( 'id' );
            $banco_id        = new TDBCombo( 'banco_id', 'afincco', 'Banco', 'id', 'nome', 'nome' );
            $nome            = new TEntry( 'nome' );
            $agencia         = new TEntry( 'agencia' );
            $numero          = new TEntry( 'numero' );
            $tipo            = new TCombo( 'tipo' );
            $chave           = new TEntry( 'chave' );
            $debito          = new TNumeric( 'debito', 2, ',', '.', TRUE );
            $credito         = new TNumeric( 'credito', 2, ',', '.', TRUE );
            $ativo           = new TCombo( 'ativo' );
            $data_fechamento = new TDate( 'data_fechamento' );
            $saldo           = new TEntry( 'saldo' );

            $banco_id->enableSearch();

            $ativo->addItems( Utilidades::sim_nao() );
            $tipo->addItems( Utilidades::tipo_conta() );

            $id->setEditable( FALSE );
            $debito->setEditable( FALSE );
            $credito->setEditable( FALSE );
            $saldo->setEditable( FALSE );

            $data_fechamento->setMask( 'dd/mm/yyyy' );
            $data_fechamento->setDatabaseMask( 'yyyy-mm-dd' );

            $id->style              = ( 'text-align:center;color:#ff0000;' );
            $debito->style          = ( 'color: #FF0000;text-align:right;' );
            $credito->style         = ( 'color: #00008B;text-align:right;' );
            $saldo->style           = ( 'text-align:right;' );
            $data_fechamento->style = ( 'color: #ff0000;text-align:center;' );

            $nome->addValidation( 'Descrição da Conta', new TRequiredValidator() );

            $campo_id         = [ new TLabel( 'Código' ), $id ];
            $campo_banco      = [ new TLabel( 'Banco' ), $banco_id ];
            $campo_nome       = [ new TLabel( 'Descrição' ), $nome ];
            $campo_agencia    = [ new TLabel( 'Agência' ), $agencia ];
            $campo_numero     = [ new TLabel( 'Numero' ), $numero ];
            $campo_tipo       = [ new TLabel( 'Tipo' ), $tipo ];
            $campo_chaveofx   = [ new TLabel( 'Chave OFX' ), $chave ];
            $campo_ativa      = [ new TLabel( 'Ativa' ), $ativo ];
            $campo_credito    = [ new TLabel( 'Total dos Créditos' ), $credito ];
            $campo_debito     = [ new TLabel( 'Total dos Débitos' ), $debito ];
            $campo_saldo      = [ new TLabel( 'Saldo' ), $saldo ];
            $campo_fechamento = [ new TLabel( 'Data Fechamento' ), $data_fechamento ];

            $row         = $this->form->addFields( $campo_id, $campo_banco, $campo_nome, $campo_ativa );
            $row->layout = [ 'col-md-2', 'col-md-4', 'col-md-4', 'col-md-2' ];

            $row         = $this->form->addFields( $campo_tipo, $campo_agencia, $campo_numero, $campo_chaveofx, $campo_fechamento );
            $row->layout = [ 'col-md-2', 'col-md-3', 'col-md-3', 'col-md-2', 'col-md-2' ];

            $row         = $this->form->addFields( $campo_credito, $campo_debito, $campo_saldo );
            $row->layout = [ 'col-md-4', 'col-md-4', 'col-md-4' ];

            /* BOTÕES */
            $this->form->addAction( _t( 'Save' ), new TAction( [ $this, 'onSave' ] ), 'far:save blue' );
            $this->form->addActionLink( _t( 'New' ), new TAction( [ $this, 'onClear' ] ), 'fa:eraser red' );
            $this->form->addActionLink( 'Retornar', new TAction( [ $this,
                                                                   'onFechaRightPanel' ] ), 'fa: fa-times green' );
            /* BOTÕES */

            $pos_action = new TAction( [ 'contacorrenteList', 'onReload' ] );
            self::setAfterSaveAction( $pos_action );

            $container        = new TVBox();
            $container->style = 'width: 100%';
            $container->add( $this->form );

            parent::add( $container );
        }

        public function  onEdit( $param ) {
            $data = parent::onEdit($param);
            if ($data->saldo > 0){
                TScript::create("$('input[name=\"saldo\"]').css('color', 'blue');");
            }else{
                TScript::create("$('input[name=\"saldo\"]').css('color', 'red');");
            }
        }

    }
