<?php
    
    use Adianti\Base\TStandardForm;
    use Adianti\Widget\Form\TEntry;
    
    class tiposervicoForm extends TStandardForm
    {
        
        function __construct()
        {
            parent::__construct();
            
            parent::setTargetContainer( 'adianti_right_panel' );
            
            $this->setDatabase( 'afincco' );
            $this->setActiveRecord( 'Tiposervico' );
            
            $this->form = new BootstrapFormBuilder( 'form_'.__CLASS__ );
            $this->form->setFormTitle( 'Manutenção de Tipos de Serviço' );
            
            $id   = new TEntry( 'id' );
            $nome = new TEntry( 'nome' );
            
            $id->setEditable( FALSE );
            
            $id->style .= 'text-align:center';
            
            $campo_id   = [ new TLabel( 'Código' ), $id ];
            $campo_nome = [ new TLabel( 'Descrição' ), $nome ];
            
            $this->form->addFields( $campo_id, $campo_nome )->layout = [ 'col-md-2', 'col-md-6' ];
            
            /* BOTÕES */
            $this->form->addAction( _t( 'Save' ), new TAction( [ $this, 'onSave' ] ), 'far:save blue' );
            $this->form->addActionLink( _t( 'New' ), new TAction( [ $this, 'onClear' ] ), 'fa:eraser red' );
            $this->form->addActionLink( 'Retornar', new TAction( [ $this,
                                                                   'onFechaRightPanel' ] ), 'fa: fa-times green' );
            /* BOTÕES */
            
            $pos_action = new TAction( [ 'tiposervicoList', 'onReload' ] );
            self::setAfterSaveAction( $pos_action );
            
            $container        = new TVBox();
            $container->style = 'width: 100%';
            $container->add( $this->form );
            
            parent::add( $container );
        }
    }
