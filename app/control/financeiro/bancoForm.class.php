<?php
    
    use Adianti\Base\TStandardForm;
    
    class bancoForm extends TStandardForm {

        public function __construct( $param ) {
            parent::__construct();

            parent::setTargetContainer( 'adianti_right_panel' );

            $this->setDatabase( 'afincco' );
            $this->setActiveRecord( 'Banco' );

            $this->form = new BootstrapFormBuilder( 'form_'.__CLASS__ );
            $this->form->setFormTitle( 'Manutenção de Bancos' );

            $id     = new TEntry( 'id' );
            $codigo = new TEntry( 'codigo' );
            $nome   = new TEntry( 'nome' );

            $id->setEditable( FALSE );

            $id->style     .= 'text-align:center';
            $codigo->style .= 'text-align:center';

            $codigo->setMask( "999" );

            $campo_id          = array( new TLabel( 'Código' ), $id );
            $campo_compensacao = array( new TLabel( 'Código Compensação' ), $codigo );
            $campo_nome        = array( new TLabel( 'Nome' ), $nome );

            $this->form->addFields( $campo_id, $campo_compensacao, $campo_nome )->layout = [ 'col-md-2', 'col-md-4', 'col-md-6' ];

            /* BOTÕES */
            $this->form->addAction( _t( 'Save' ), new TAction( [ $this, 'onSave' ] ), 'far:save blue' );
            $this->form->addActionLink( _t( 'New' ), new TAction( [ $this, 'onClear' ] ), 'fa:eraser red' );
            $this->form->addActionLink( 'Retornar', new TAction( [ $this,
                                                                   'onFechaRightPanel' ] ), 'fa: fa-times green' );
            /* BOTÕES */

            $pos_action = new TAction( [ 'bancoList', 'onReload' ] );
            self::setAfterSaveAction( $pos_action );

            $container        = new TVBox();
            $container->style = 'width: 100%';
            $container->add( $this->form );

            parent::add( $container );
        }
    }
