<?php
    
    use Adianti\Base\TStandardForm;
    use Adianti\Wrapper\BootstrapFormBuilder;
    
    class textoDevolucaoForm extends TStandardForm
    {

        public function __construct( $param ) {
            parent::__construct();

            parent::setTargetContainer( 'adianti_right_panel' );

            $this->setDatabase( 'afincco' );
            $this->setActiveRecord( 'TextoDevolucao' );
    
            $this->form = new BootstrapFormBuilder( 'form_'.__CLASS__ );
            $this->form->setFormTitle( 'Texto Devolução' );

            $id    = new TEntry( 'id' );
            $nome  = new TEntry( 'nome' );
            $texto = new THtmlEditor( 'texto' );

            $id->setEditable( FALSE );

            $texto->setSize( '100%', 500 );

            $nome->addValidation( 'Descrição', new TRequiredValidator() );

            $id->style   = ( 'text-align:center;color:#ff0000;' );
            $nome->style = ( 'color:#ff0000;' );

            $campo_id    = [ new TLabel( 'Código' ), $id ];
            $campo_nome  = [ new TLabel( 'Nome' ), $nome ];
            $campo_texto = [ new TLabel( 'Texto' ), $texto ];

            $row         = $this->form->addFields( $campo_id, $campo_nome );
            $row->layout = [ 'col-sm-1', 'col-sm-6' ];
            $row         = $this->form->addFields( $campo_texto );
            $row->layout = [ 'col-sm-12' ];

            /* BOTÕES */
            $this->form->addAction( _t( 'Save' ), new TAction( array($this, 'onSave') ), 'far:save blue' );
            $this->form->addActionLink( _t( 'New' ), new TAction( array($this, 'onClear') ), 'fa:eraser red' );
            $this->form->addActionLink( 'Retornar', new TAction( [$this, 'onFechaRightPanel'] ), 'fa: fa-times green' );
            /* BOTÕES */

            $container        = new TVBox();
            $container->style = 'width: 100%';
            $container->add( $this->form );

            parent::add( $container );
        }
    }
