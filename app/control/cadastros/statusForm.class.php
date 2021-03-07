<?php
    
    use Adianti\Base\TStandardForm;
    use Adianti\Control\TAction;
    use Adianti\Widget\Form\TCombo;
    use Adianti\Widget\Form\TEntry;
    use Adianti\Widget\Form\TSpinner;
    use Adianti\Widget\Form\TText;
    
    class statusForm extends TStandardForm
    {
        
        function __construct()
        {
            parent::__construct();
            
            parent::setTargetContainer( 'adianti_right_panel' );
            
            $this->setDatabase( 'afincco' );
            $this->setActiveRecord( 'Status' );
            
            $this->form = new BootstrapFormBuilder( 'form_'.__CLASS__ );
            $this->form->setFormTitle( 'Manutenção de Status do Processo' );
            
            $id               = new TEntry( 'id' );
            $statu            = new TEntry( 'statu' );
            $prazo            = new TSpinner( 'prazo' );
            $email_cobranca   = new TText( 'email_cobranca' );
            $email_liberador  = new TCombo( 'email_liberador' );
            $email_seguradora = new TCombo( 'email_seguradora' );
            $status_final     = new TCombo( 'status_final' );
            
            $statu->forceUpperCase();
            $email_liberador->addItems( Utilidades::sim_nao() );
            $email_seguradora->addItems( Utilidades::sim_nao() );
            $status_final->addItems( Utilidades::sim_nao() );
            $email_liberador->setValue( 0 );
            
            $prazo->setRange( 1, 100, 1 );
            $prazo->style = 'text-align:center !important;background-color: #F3F781';
            
            $id->setEditable( FALSE );
            
            $campo_id         = [ new TLabel( 'Código' ), $id ];
            $campo_nome       = [ new TLabel( 'Status' ), $statu ];
            $campo_prazo      = [ new TLabel( 'Prazo em Dias' ), $prazo ];
            $campo_liberador  = [ new TLabel( 'E-Mail para Liberador' ), $email_liberador ];
            $campo_seguradora = [ new TLabel( 'E-Mail para Seguradora' ), $email_seguradora ];
            $campo_final      = [ new TLabel( 'Status Final' ), $status_final ];
            $campo_email      = [ new TLabel( 'E-Mail Cobrança(Separar os emails com ; (PONTO E VIRGULA)  ' ), $email_cobranca ];
            
            $this->form->addFields( $campo_id, $campo_nome, $campo_prazo )->layout = [ 'col-sm-2', 'col-sm-8', 'col-sm-2' ];
            $this->form->addFields( $campo_liberador, $campo_seguradora, $campo_final )->layout  = [ 'col-sm-3', 'col-sm-3', 'col-sm-2' ];
            $this->form->addFields( $campo_email );
            
            /* BOTÕES */
            $this->form->addAction( _t( 'Save' ), new TAction( [ $this, 'onSave' ] ), 'far:save blue' );
            $this->form->addActionLink( _t( 'New' ), new TAction( [ $this, 'onClear' ] ), 'fa:eraser red' );
            $this->form->addActionLink( 'Retornar', new TAction( [ $this, 'onFechaRightPanel' ] ), 'fa: fa-times green' );
            /* BOTÕES */
    
            $pos_action = new TAction( [ 'statusList', 'onReload' ] );
            self::setAfterSaveAction( $pos_action );
    
    
            $container        = new TVBox();
            $container->style = 'width: 100%';
            $container->add( $this->form );
            
            parent::add( $container );
        }
    }
