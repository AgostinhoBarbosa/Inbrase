<?php
    
    use Adianti\Base\TStandardForm;
    use Adianti\Core\AdiantiCoreApplication;
    use Adianti\Registry\TSession;
    use Adianti\Widget\Base\TScript;
    use Adianti\Widget\Form\TCombo;
    use Adianti\Widget\Form\TDateTime;
    use Adianti\Widget\Form\TEntry;
    use Adianti\Widget\Form\THidden;
    use Adianti\Wrapper\BootstrapFormBuilder;
    
    class movimentoTituloForm extends TStandardForm {

        function __construct() {
            parent::__construct();

            parent::setTargetContainer( 'adianti_right_panel' );

            $this->setDatabase( 'afincco' );
            $this->setActiveRecord( 'MovimentoTitulo' );

            $this->form = new BootstrapFormBuilder( 'form_'.__CLASS__ );
            $this->form->setFormTitle('Movimento de Caixa');

            $id                = new TEntry( 'id' );
            $data_movimento    = new TDateTime( 'data_movimento' );
            $titulo_id         = new THidden( 'titulo_id' );
            $caixa_id          = new THidden( 'caixa_id' );
            $cheque_id         = new THidden( 'cheque_id' );
            $tipolancamento_id = new TDBCombo( 'tipolancamento_id', 'afincco', 'TipoLancamento', 'id', 'nome', 'nome' );
            $dc                = new TCombo( 'dc' );
            $valor             = new TNumeric( 'valor', 2, '.', ',', TRUE );
            $processo_id       = new TEntry( 'processo_id' );
            $usuario           = new TEntry( 'usuario' );
            $observacao        = new THtmlEditor( 'observacao' );

            $tipolancamento_id->enableSearch();

            $dc->addItems( Utilidades::debito_credito() );

            $id->setEditable( FALSE );
            $usuario->setEditable( FALSE );
            $data_movimento->setEditable( FALSE );

            $observacao->setSize( '100%', 200 );

            $data_movimento->setMask( 'dd/mm/yyyy' );
            $data_movimento->setDatabaseMask( 'yyyy-mm-dd' );

            $id->style                = ( 'text-align:center;color:#ff0000;background-color:#F7F2E0;' );
            $data_movimento->style    = ( 'background-color: #FFFEEB;text-align:center' );
            $usuario->style           = ( 'background-color: #EEEED1;' );
            $tipolancamento_id->style = ( 'background-color: #FFFEEB;text-align:center;color:#ff0000;' );

            $campo_codigo   = array( new TLabel( 'Código' ), $id );
            $campo_data     = array( new TLabel( 'Data Movimento' ), $data_movimento, $titulo_id, $caixa_id, $cheque_id );
            $campo_tipolan  = array( new TLabel( 'Tipo de Lançamento' ), $tipolancamento_id );
            $campo_dc       = array( new TLabel( 'D/C' ), $dc );
            $campo_valor    = array( new TLabel( 'Valor' ), $valor );
            $campo_processo = array( new TLabel( 'Nº. Processo' ), $processo_id );
            $campo_usuario  = array( new TLabel( 'Usuário' ), $usuario );
            $campo_obs      = array( new TLabel( 'Observação' ), $observacao );

            $this->form->addFields( $campo_codigo, $campo_data, $campo_usuario, $campo_processo )->layout = [ 'col-md-2', 'col-md-2', 'col-md-3', 'col-md-2' ];
            $this->form->addFields( $campo_tipolan, $campo_dc, $campo_valor )->layout                     = [ 'col-md-5', 'col-md-2', 'col-md-2' ];
            $this->form->addFields( $campo_obs )->layout                                                  = [ 'col-md-12' ];

            /* BOTÕES */
            $this->form->addAction( _t( 'Save' ), new TAction( [ $this, 'onSave' ] ), 'far:save blue' );
            $this->form->addActionLink( _t( 'New' ), new TAction( [ $this, 'onClear' ] ), 'fa:eraser red' );
            $this->form->addActionLink( 'Retornar', new TAction( [ $this,
                                                                   'onFechaRightPanel' ] ), 'fa: fa-times green' );
            /* BOTÕES */

            $container        = new TVBox();
            $container->style = 'width: 100%';
            $container->add( $this->form );

            parent::add( $container );
        }

        public function onFechaRightPanel()
        {
            TScript::create( 'Template.closeRightPanel()' );
            if (TSession::getValue('origem_movimento')){
                TSession::delValue('origem_movimento');
                AdiantiCoreApplication::loadPage('tituloList', 'onReload');
            }else {
                AdiantiCoreApplication::loadPage( 'caixaList', 'onReload' );
            }
        }

        public function onClear( $param ) {
            $this->form->clear( TRUE );
            $objeto                 = new StdClass();
            $objeto->dc             = 'D';
            $objeto->usuario        = TSession::getValue( 'login' );
            $objeto->data_movimento = date( 'Y-m-d H:i:s' );
            if ($param['origem'] === 'titulo') {
                $objeto->titulo_id = $param[ 'key' ];
                TSession::setValue('origem_movimento', 'titulo');
            }else {
                $objeto->caixa_id = $param[ 'key' ];
            }
            $this->form->setData( $objeto );
        }

    }
