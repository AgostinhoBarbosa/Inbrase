<?php
    
    use Adianti\Base\TStandardForm;
    use Adianti\Widget\Form\TCombo;
    use Adianti\Widget\Form\TDate;
    use Adianti\Widget\Form\TEntry;
    use Adianti\Widget\Form\THtmlEditor;
    use Adianti\Widget\Form\TNumeric;
    
    class tituloForm extends TStandardForm
    {
        protected $form;
        
        public function __construct( $param )
        {
            parent::__construct();
            
            parent::setTargetContainer( 'adianti_right_panel' );
            
            $this->setDatabase( 'afincco' );
            $this->setActiveRecord( 'Titulo' );
            
            $this->form = new BootstrapFormBuilder( 'form_'.__CLASS__ );
            $this->form->setFormTitle( 'Manutenção de Títulos Financeiros' );
            
            $id                = new TEntry( 'id' );
            $pessoa_id         = new TDBCombo( 'pessoa_id', 'afincco', 'Pessoa', 'id', 'nome', 'nome' );
            $tipolancamento_id = new TDBCombo( 'tipolancamento_id', 'afincco', 'TipoLancamento', 'id', 'nome', 'nome' );
            $data_entrada      = new TDate( 'data_entrada' );
            $data_vencimento   = new TDate( 'data_vencimento' );
            $data_emissao      = new TDate( 'data_emissao' );
            $valor             = new TNumeric( 'valor', 2, ',', '.', TRUE );
            $saldo             = new TNumeric( 'saldo', 2, ',', '.', TRUE );
            $numero            = new TEntry( 'numero' );
            $parcela           = new TEntry( 'parcela' );
            $pagar_receber     = new TCombo( 'pagar_receber' );
            $dc                = new TCombo( 'dc' );
            $processo_id       = new TEntry( 'processo_id' );
            $observacao        = new THtmlEditor( 'observacao' );
            
            $id->setEditable( FALSE );
            $saldo->setEditable( FALSE );
            $tipolancamento_id->enableSearch();
            $pessoa_id->enableSearch();
            
            $dc->addItems( Utilidades::debito_credito() );
            $pagar_receber->addItems( Utilidades::pagar_receber() );
            
            $dc->setValue( 'C' );
            $pagar_receber->setValue( 'P' );
            
            $id->style                = ( 'text-align:center;background-color: #F7F2E0;' );
            $pessoa_id->style         = ( 'text-align:center;background-color: #F7F2E0;' );
            $tipolancamento_id->style = ( 'text-align: center;background-color: #F7F2E0;' );
            $data_entrada->style      = ( 'text-align:center !important;' );
            $data_emissao->style      = ( 'text-align:center !important;' );
            $data_vencimento->style   = ( 'text-align:center !important;' );
            $numero->style            = ( 'text-align:center !important;' );
            $parcela->style           = ( 'text-align:center !important;' );
            $dc->style                = ( 'text-align:center !important;' );
            $processo_id->style       = ( 'text-align:center !important;' );
            $pagar_receber->style     = ( 'text-align:center !important;' );
            $observacao->style        = ( 'margin-right:10px !important;' );
            $saldo->style             = ( 'background-color: #FFF8DC;' );
            
            $data_entrada->setMask( 'dd/mm/yyyy' );
            $data_emissao->setMask( 'dd/mm/yyyy' );
            $data_vencimento->setMask( 'dd/mm/yyyy' );
            
            $data_entrada->setDatabaseMask( 'yyyy-mm-dd' );
            $data_emissao->setDatabaseMask( 'yyyy-mm-dd' );
            $data_vencimento->setDatabaseMask( 'yyyy-mm-dd' );
            
            $observacao->setSize( '100%', 200 );
            
            $change_valor = new TAction( [ $this, 'onChangeValor' ] );
            $valor->setExitAction( $change_valor );
            
            $campo_codigo          = [ new TLabel( 'Código' ), $id ];
            $campo_tipoconta       = [ new TLabel( 'Tipo de Conta' ), $pagar_receber ];
            $campo_cliente         = [ new TLabel( 'Cliente/Fornecedor' ), $pessoa_id ];
            $campo_tipo_lancamento = [ new TLabel( 'Tipo de Lançamento' ), $tipolancamento_id ];
            $campo_dc              = [ new TLabel( 'Débito/Crédito' ), $dc ];
            $campo_processo        = [ new TLabel( 'Nº Processo' ), $processo_id ];
            $campo_dataentrada     = [ new TLabel( 'Data Entrada' ), $data_entrada ];
            $campo_dataemissao     = [ new TLabel( 'Data Emissão' ), $data_emissao ];
            $campo_datavencimento  = [ new TLabel( 'Data Vencimento' ), $data_vencimento ];
            $campo_numerotitulo    = [ new TLabel( 'Nº Título' ), $numero ];
            $campo_parcela         = [ new TLabel( 'Parcela' ), $parcela ];
            $campo_valor           = [ new TLabel( 'Valor' ), $valor ];
            $campo_saldo           = [ new TLabel( 'Saldo' ), $saldo ];
            $campo_observacao      = [ new TLabel( 'Observação' ), $observacao ];
            
            $row         = $this->form->addFields( $campo_codigo, $campo_tipoconta, $campo_cliente, $campo_tipo_lancamento );
            $row->layout = [ 'col-md-2', 'col-md-2', 'col-md-5', 'col-md-3' ];
            
            $row         = $this->form->addFields( $campo_processo, $campo_dataentrada, $campo_dataemissao, $campo_datavencimento );
            $row->layout = [ 'col-md-2', 'col-md-2', 'col-md-2', 'col-md-2' ];
            
            $row         = $this->form->addFields( $campo_numerotitulo, $campo_parcela, $campo_dc, $campo_valor, $campo_saldo );
            $row->layout = [ 'col-md-2', 'col-md-2', 'col-md-2', 'col-md-2', 'col-md-2' ];
            
            $row         = $this->form->addFields( $campo_observacao );
            $row->layout = [ 'col-md-12' ];
            
            /* BOTÕES */
            $this->form->addAction( _t( 'Save' ), new TAction( [ $this, 'onSave' ] ), 'far:save blue' );
            $this->form->addActionLink( _t( 'New' ), new TAction( [ $this, 'onClear' ] ), 'fa:eraser red' );
            $this->form->addActionLink( 'Retornar', new TAction( [ $this,
                                                                   'onFechaRightPanel' ] ), 'fa: fa-times green' );
            /* BOTÕES */
            
            $pos_action = new TAction( [ 'tituloList', 'onReload' ] );
            self::setAfterSaveAction( $pos_action );
            
            $container        = new TVBox();
            $container->style = 'width: 100%';
            $container->add( $this->form );
            
            parent::add( $container );
        }
        
        public static function onChangeValor( $param )
        {
            $saldo = 0.00;
            try {
                TTransaction::open( 'afincco' );
                $movimento = new movimentotitulo();
                $saldo     = $movimento->get_saldo_titulo( $param[ 'id' ] );
                TTransaction::close();
            } catch ( Exception $e ) {
                TTransaction::close();
            }
            
            $obj   = new StdClass;
            $valor = str_replace( ".", "", $param[ 'valor' ] );
            $valor = str_replace( ",", ".", $valor );
            
            $obj->saldo = ( $valor + $saldo );
            $obj->saldo = number_format( $obj->saldo, 2, ',', '.' );
            TForm::sendData( 'form_'.__CLASS__, $obj );
        }
    }
