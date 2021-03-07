<?php
    
    use Adianti\Base\TStandardForm;
    
    class TipoarquivoForm extends TStandardForm
    {
        
        function __construct()
        {
            parent::__construct();
            
            parent::setTargetContainer( 'adianti_right_panel' );
            
            $this->setDatabase( 'afincco' );
            $this->setActiveRecord( 'Tipoarquivo' );
            
            $this->form = new BootstrapFormBuilder( 'form_'.__CLASS__ );
            $this->form->setFormTitle( 'Manutenção de Tipos de Arquivo' );
            
            $id        = new TEntry( 'id' );
            $nome      = new TEntry( 'nome' );
            $liberacao = new TEntry( 'liberacao' );
            
            $id->setEditable(False);
            
            $id->style .= 'text-align:center';
            
            $campo_id        = [ new TLabel( 'Código' ), $id ];
            $campo_nome      = [ new TLabel( 'Descrição' ), $nome ];
            $campo_liberacao = [ new TLabel( 'Texto Liberação' ), $liberacao ];
            
            $row         = $this->form->addFields( $campo_id, $campo_nome, $campo_liberacao );
            $row->layout = [ 'col-md-2', 'col-md-5', 'col-md-5' ];
            
            /* BOTÕES */
            $this->form->addAction( _t( 'Save' ), new TAction( [ $this, 'onSave' ] ), 'far:save blue' );
            $this->form->addActionLink( _t( 'New' ), new TAction( [ $this, 'onClear' ] ), 'fa:eraser red' );
            $this->form->addActionLink( 'Retornar', new TAction( [ $this,
                                                                   'onFechaRightPanel' ] ), 'fa: fa-times green' );
            /* BOTÕES */
            
            $pos_action = new TAction( [ 'TipoarquivoList', 'onReload' ] );
            self::setAfterSaveAction( $pos_action );
            
            $container        = new TVBox();
            $container->style = 'width: 100%';
            $container->add( $this->form );
            
            parent::add( $container );
        }
    }
