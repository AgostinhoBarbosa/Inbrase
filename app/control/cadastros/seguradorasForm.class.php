<?php
    
    use Adianti\Base\TStandardForm;
    
    class seguradorasForm extends TStandardForm
    {
        function __construct()
        {
            parent::__construct();
            
            parent::setTargetContainer( 'adianti_right_panel' );
            
            $this->setDatabase( 'afincco' );
            $this->setActiveRecord( 'Seguradoras' );
            
            $this->form = new BootstrapFormBuilder( 'form_'.__CLASS__ );
            $this->form->setFormTitle( 'Manutenção Cadastro Seguradoras' );
            
            $id           = new TEntry( 'id' );
            $nome         = new TEntry( 'nome' );
            $gerente      = new TEntry( 'gerente' );
            $end          = new TEntry( 'end' );
            $num          = new TEntry( 'num' );
            $compl        = new TEntry( 'compl' );
            $cidade       = new TEntry( 'cidade' );
            $uf           = new TCombo( 'uf' );
            $email        = new TEntry( 'email' );
            $telefone     = new TEntry( 'telefone' );
            $fax          = new TEntry( 'fax' );
            $enviar_email = new TCombo( 'enviar_email' );
            
            $id->setEditable( FALSE );
            
            $enviar_email->addItems( Utilidades::sim_nao() );
            $uf->addItems( Utilidades::uf() );
            $uf->enableSearch();
            
            $id->style = 'text-align:center;color:ff0000;';
            
            $campo_id          = [ new TLabel( 'Código' ), $id ];
            $campo_nome        = [ new TLabel( 'Nome' ), $nome ];
            $campo_gerente     = [ new TLabel( 'Gerente' ), $gerente ];
            $campo_endereco    = [ new TLabel( 'Endereço' ), $end ];
            $campo_numero      = [ new TLabel( 'Nº' ), $num ];
            $campo_complemento = [ new TLabel( 'Complemento' ), $compl ];
            $campo_cidade      = [ new TLabel( 'Cidade' ), $cidade ];
            $campo_uf          = [ new TLabel( 'UF' ), $uf ];
            $campo_email       = [ new TLabel( 'E-Mail' ), $email ];
            $campo_fone        = [ new TLabel( 'Telefone' ), $telefone ];
            $campo_fax         = [ new TLabel( 'Fax' ), $fax ];
            $campo_enviar      = [ new TLabel( 'Enviar E-Mail Automático' ), $enviar_email ];
            
            $row         = $this->form->addFields( $campo_id, $campo_nome, $campo_gerente );
            $row->layout = [ 'col-sm-1', 'col-sm-6', 'col-sm-5' ];
            $row         = $this->form->addFields( $campo_endereco, $campo_numero, $campo_complemento );
            $row->layout = [ 'col-sm-6', 'col-sm-1', 'col-sm-5' ];
            $row         = $this->form->addFields( $campo_cidade, $campo_uf );
            $row->layout = [ 'col-sm-7', 'col-sm-5' ];
            $row         = $this->form->addFields( $campo_email, $campo_enviar );
            $row->layout = [ 'col-sm-9', 'col-sm-3' ];
            $row         = $this->form->addFields( $campo_fone, $campo_fax );
            $row->layout = [ 'col-sm-3', 'col-sm-3' ];
            
            /* BOTÕES */
            $this->form->addAction( _t( 'Save' ), new TAction( [ $this, 'onSave' ] ), 'far:save blue' );
            $this->form->addActionLink( _t( 'New' ), new TAction( [ $this, 'onClear' ] ), 'fa:eraser red' );
            $this->form->addActionLink( 'Retornar', new TAction( [ $this,
                                                                   'onFechaRightPanel' ] ), 'fa: fa-times green' );
            /* BOTÕES */
            
            $pos_action = new TAction( [ 'seguradorasList', 'onReload' ] );
            self::setAfterSaveAction( $pos_action );
            
            $container        = new TVBox();
            $container->style = 'width: 100%';
            $container->add( $this->form );
            
            parent::add( $container );
        }
    }
