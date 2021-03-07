<?php
    
    use Adianti\Base\TStandardForm;
    use Adianti\Database\TCriteria;
    use Adianti\Database\TTransaction;
    use Adianti\Widget\Base\TScript;
    use Adianti\Widget\Form\TCombo;
    use Adianti\Widget\Form\TDate;
    use Adianti\Widget\Form\TEntry;
    use Adianti\Widget\Form\THtmlEditor;
    use Adianti\Widget\Form\TLabel;
    use Adianti\Wrapper\BootstrapFormBuilder;
    
    class pessoaForm extends TStandardForm
    {
        
        protected $subform;
        
        function __construct()
        {
            
            parent::__construct();
            
            parent::setTargetContainer( 'adianti_right_panel' );
            
            $this->setDatabase( 'afincco' );
            $this->setActiveRecord( 'Pessoa' );
            
            $this->form = new BootstrapFormBuilder( 'form_'.__CLASS__ );
            $this->form->setFormTitle( 'Clientes/Fornecedores' );
            
            $criteria_usuario = new TCriteria();
            $filter           = new TFilter( 'id', '>', '1' );
            $criteria_usuario->add( $filter );
            
            $id              = new TEntry( 'id' );
            $tipo_pessoa     = new TCombo( 'tipo_pessoa' );
            $documento       = new TEntry( 'documento' );
            $rg_ie           = new TEntry( 'rg_ie' );
            $nome            = new TEntry( 'nome' );
            $email           = new TEntry( 'email' );
            $data_nascimento = new TDate( 'data_nascimento' );
            $agenda          = new TCombo( 'agenda' );
            $observacao      = new THtmlEditor( 'observacao' );
            $contato         = new TEntry( 'contato' );
            $liberador       = new TCombo( 'liberador' );
            $seguradora      = new TCombo( 'seguradora' );
            $usuario         = new TDBCombo( 'usuario', 'permission', 'SystemUser', 'id', 'name', 'name', $criteria_usuario );
            $apelido         = new TEntry( 'apelido' );
            
            $rua         = new TEntry( 'rua' );
            $numero      = new TEntry( 'numero' );
            $complemento = new TEntry( 'complemento' );
            $bairro      = new TEntry( 'bairro' );
            $cep         = new TEntry( 'cep' );
            $cidade      = new TEntry( 'cidade' );
            $uf          = new TCombo( 'uf' );
            
            $usuario->enableSearch();
            
            $fone_id        = new THidden( 'fone_id' );
            $fone_pessoa_id = new THidden( 'fone_pessoa_id' );
            $fone_tipo      = new TCombo( 'fone_tipo' );
            $fone_numero    = new TEntry( 'fone_numero' );
            
            $tipo_pessoa->addItems( Utilidades::tipo_pessoa() );
            $fone_tipo->addItems( Utilidades::tipo_telefone() );
            $agenda->addItems( Utilidades::sim_nao() );
            $liberador->addItems( Utilidades::sim_nao() );
            $seguradora->addItems( Utilidades::sim_nao() );
            $uf->addItems( Utilidades::uf() );
            
            $uf->setValue( 'PR' );
            $documento->onKeyUp = 'FormatarCpfCnpj(this, "lbl_doc", "lbl_nome","lbl_rg", "lbl_data")';
            $documento->onInput = 'FormatarCpfCnpj(this, "lbl_doc", "lbl_nome","lbl_rg", "lbl_data")';
            
            $nome->addValidation( 'Nome', new TRequiredValidator() );
            $documento->addValidation( 'CPF/CNPJ', new TRequiredValidator() );
            
            $id->setEditable( FALSE );
            $fone_id->setEditable( FALSE );
            
            $id->style          = ( 'text-align:center;color:#ff0000;background-color:#F7F2E0;' );
            $nome->style        = ( 'background-color: #FFFEEB;' );
            $documento->style   = ( 'background-color: #FFFEEB;' );
            $tipo_pessoa->style = ( 'background-color: #FFFEEB;' );
            $email->style       = ( 'text-transform:lowercase;' );
            $cep->style         = ( 'background-color: #FFFEEB;' );
            $fone_id->style     = ( 'text-align:center;color:#ff0000;background-color:#F7F2E0;' );
            
            $observacao->setSize( '100%', '100' );
            
            $data_nascimento->setMask( 'dd/mm/yyyy' );
            $data_nascimento->setDatabaseMask( 'yyyy-mm-dd' );
            $cep->setMask( '99.999-999' );
            
            $buscaCep = new TAction( [ $this, 'onCep' ] );
            $cep->setExitAction( $buscaCep );
            
            $buscaCnpj = new TAction( [ $this, 'onCNPJ' ] );
            $documento->setExitAction( $buscaCnpj );
            
            $campo_id          = [ new TLabel( 'Código' ), $id ];
            $campo_nome        = [ new TLabel( 'Nome', NULL, NULL, NULL, NULL, 'lbl_nome' ), $nome ];
            $campo_tipopessoa  = [ new TLabel( 'Tipo Cliente' ), $tipo_pessoa ];
            $campo_documento   = [ new TLabel( 'CPF', NULL, NULL, NULL, NULL, 'lbl_doc' ), $documento ];
            $campo_rg          = [ new TLabel( 'RG/Identidade', NULL, NULL, NULL, NULL, 'lbl_rg' ), $rg_ie ];
            $campo_nascimento  = [ new TLabel( 'Data Nascimento', NULL, NULL, NULL, NULL, 'lbl_data' ),
                                   $data_nascimento ];
            $campo_email       = [ new TLabel( 'E-Mail' ), $email ];
            $campo_agenda      = [ new TLabel( 'Agenda' ), $agenda ];
            $campo_contato     = [ new TLabel( 'Contato' ), $contato ];
            $campo_liberador   = [ new TLabel( 'Liberador' ), $liberador ];
            $campo_seguradora  = [ new TLabel( 'Seguradora' ), $seguradora ];
            $campo_cep         = [ new TLabel( 'Cep' ), $cep ];
            $campo_rua         = [ new TLabel( 'Rua' ), $rua ];
            $campo_numero      = [ new TLabel( 'Nº.' ), $numero ];
            $campo_complemento = [ new TLabel( 'Complemento' ), $complemento ];
            $campo_bairro      = [ new TLabel( 'Bairro' ), $bairro ];
            $campo_cidade      = [ new TLabel( 'Cidade' ), $cidade ];
            $campo_uf          = [ new TLabel( 'UF' ), $uf ];
            $campo_obs         = [ new TLabel( ' ' ), $observacao ];
            $campo_fone_id     = [ $fone_id, $fone_pessoa_id ];
            $campo_fone_tipo   = [ new TLabel( 'Tipo ' ), $fone_tipo ];
            $campo_fone_numero = [ new TLabel( 'Numero ' ), $fone_numero ];
            $campo_usuario     = [ new TLabel( 'Usuário ' ), $usuario ];
            $campo_apelido     = [ new TLabel( 'Nome Fantasia' ), $apelido ];
            
            $row         = $this->form->addFields( $campo_id, $campo_nome, $campo_apelido, $campo_tipopessoa );
            $row->layout = [ 'col-md-2', 'col-md-4', 'col-md-3', 'col-md-3' ];

            $row         = $this->form->addFields( $campo_agenda, $campo_liberador, $campo_seguradora, $campo_contato );
            $row->layout = [ 'col-md-2', 'col-md-2', 'col-md-2', 'col-md-6' ];

            
            $row         = $this->form->addFields( $campo_documento, $campo_rg, $campo_nascimento, $campo_email );
            $row->layout = [ 'col-md-3', 'col-md-3', 'col-md-2', 'col-md-4' ];
            
            $label1        = new TLabel( 'Endereço', '#5A73DB', 12, '' );
            $label1->style = 'text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
            $this->form->addContent( [ $label1 ] );
            
            $row         = $this->form->addFields( $campo_cep, $campo_rua, $campo_numero, $campo_complemento );
            $row->layout = [ 'col-md-2', 'col-md-5', 'col-md-2', 'col-md-3' ];
            
            $row         = $this->form->addFields( $campo_bairro, $campo_cidade, $campo_uf, $campo_usuario );
            $row->layout = [ 'col-md-3', 'col-md-4', 'col-md-2', 'col-md-3' ];
//ABAS
            $this->subform = new BootstrapFormBuilder( 'form_pessoaTelefone', TRUE );
            $this->subform->setFieldSizes( '100%' );
            $this->subform->setProperty( 'style', 'border:none; padding: none' );
            
            $this->subform->appendPage( 'Observação' );
            
            $this->subform->addFields( $campo_obs );
// Telefone
            $this->subform->appendPage( 'Telefones' );
            
            $add_fone       = new TButton( 'add_fone' );
            $action_addfone = new TAction( [ $this, 'onAddFone' ] );
            $add_fone->setAction( $action_addfone, 'Adicionar' );
            $add_fone->setImage( 'fa:plus green' );
            
            $campo_add_fone = [ new TLabel( ' ' ), $add_fone ];
            
            $row         = $this->subform->addFields( $campo_fone_tipo, $campo_fone_numero, $campo_add_fone, $campo_fone_id );
            $row->layout = [ 'col-md-2', 'col-md-2', 'col-md-2', 'col-md-2' ];
            
            $this->grid_fones        = new BootstrapDatagridWrapper( new TQuickGrid() );
            $this->grid_fones->style = 'width: 100%';
            $this->grid_fones->disableDefaultClick();
            $this->grid_fones->disableHtmlConversion();
            
            $column_fone_id     = new TDataGridColumn( 'fone_id', 'Código', 'center', '10%' );
            $column_fone_tipo   = new TDataGridColumn( 'tipo_nome', 'Tipo', 'center', '30%' );
            $column_fone_numero = new TDataGridColumn( 'fone_numero', 'Numero', 'center', '40%' );
            
            $this->grid_fones->addColumn( $column_fone_id );
            $this->grid_fones->addColumn( $column_fone_tipo );
            $this->grid_fones->addColumn( $column_fone_numero );
            
            $action_edit = new TDataGridAction( [ $this, 'onEditTelefone' ], [ 'key'            => '{fone_id}',
                                                                               'register_state' => 'false' ] );
            $action_del  = new TDataGridAction( [ $this, 'onDeleteTelefone' ], [ 'key'            => '{fone_id}',
                                                                                 'register_state' => 'false' ] );
            
            $this->grid_fones->addAction( $action_edit, _t( 'Edit' ), 'far:edit blue fa-lg' );
            $this->grid_fones->addAction( $action_del, _t( 'Delete' ), 'far:trash-alt red fa-lg' );
            
            $this->grid_fones->createModel();
            
            $this->subform->addContent( [ $this->grid_fones ] );
            
            $this->form->addContent( [ $this->subform ] );
            
            /* BOTÕES */
            $this->form->addAction( _t( 'Save' ), new TAction( [ $this, 'onSave' ] ), 'far:save blue' );
            $this->form->addActionLink( _t( 'New' ), new TAction( [ $this, 'onClear' ] ), 'fa:eraser red' );
            $this->form->addActionLink( 'Retornar', new TAction( [ $this,
                                                                   'onFechaRightPanel' ] ), 'fa: fa-times green' );
            /* BOTÕES */
            
            $pos_action = new TAction( [ 'pessoaList', 'onReload' ] );
            self::setAfterSaveAction( $pos_action );
            
            $container        = new TVBox();
            $container->style = 'width: 100%';
            $container->add( $this->form );
            
            parent::add( $container );
        }
        
        public static function onCep( $param )
        {
            try {
                if ( isset( $param[ 'cep' ] ) and $param[ 'cep' ] ) {
                    TSession::setValue( 'pessoa_onCep', '1' );
                    $retorno = Utilidades::onCep( $param[ 'cep' ] );
                    $objeto  = json_decode( $retorno );
                    if ( isset( $objeto->logradouro ) ) {
                        $obj         = new stdClass();
                        $obj->rua    = $objeto->logradouro;
                        $obj->bairro = $objeto->bairro;
                        $obj->cidade = $objeto->localidade;
                        $obj->uf     = $objeto->uf;
                        
                        TForm::sendData( 'form_'.__CLASS__, $obj );
                        unset( $obj );
                    }
                }
                TSession::delValue( 'pessoa_onCep' );
            } catch ( Exception $e ) {
                new TMessage( 'error', '<b>Error:</b> '.$e->getMessage() );
            } finally {
                TSession::delValue( 'pessoa_onCep' );
            }
        }
        
        public static function onCNPJ( $param )
        {
            try {
                if ( isset( $param[ 'documento' ] ) and $param[ 'documento' ] and $param[ 'tipo_pessoa' ] == 2 ) {
                    $retorno = Utilidades::onCNPJ( $param[ 'documento' ] );
                    $objeto  = json_decode( $retorno );
                    if ( isset( $objeto->logradouro ) ) {
                        $obj                  = new stdClass();
                        $obj->nome            = $objeto->nome;
                        $obj->tipo_pessoa     = 2;
                        $obj->rua             = $objeto->logradouro;
                        $obj->numero          = $objeto->numero;
                        $obj->bairro          = $objeto->bairro;
                        $obj->complemento     = $objeto->complemento;
                        $obj->cidade          = $objeto->municipio;
                        $obj->uf              = $objeto->uf;
                        $obj->data_nascimento = $objeto->abertura;
                        $obj->cep             = $objeto->cep;
                        $obj->observacao      = "";
                        for ( $i = 0; $i < count( $objeto->qsa ); $i++ ) {
                            $obj->observacao .= $objeto->qsa[ $i ]->qual." - ";
                            $obj->observacao .= $objeto->qsa[ $i ]->nome."<br>";
                        }
                        for ( $i = 0; $i < count( $objeto->atividade_principal ); $i++ ) {
                            $obj->observacao .= "Atividade Principal ".$objeto->atividade_principal[ $i ]->code." - ";
                            $obj->observacao .= $objeto->atividade_principal[ $i ]->text."<br>";
                        }
                        for ( $i = 0; $i < count( $objeto->atividades_secundarias ); $i++ ) {
                            $obj->observacao .= "Atividade Secundaria ".$objeto->atividades_secundarias[ $i ]->code." - ";
                            $obj->observacao .= $objeto->atividades_secundarias[ $i ]->text."<br>";
                        }
                        
                        $obj->observacao .= "Natureza Juridica - ".$objeto->natureza_juridica."<br>";
                        $obj->observacao .= "Capital Social - R$-".number_format( $objeto->capital_social, 2, ',', '.' )."<br>";
                        
                        TForm::sendData( 'form_'.__CLASS__, $obj );
                        unset( $obj );
                    }
                }
            } catch ( Exception $e ) {
                new TMessage( 'error', '<b>Error:</b> '.$e->getMessage() );
            }
        }
        
        public static function onEditTelefone( $param )
        {
            $data                 = new StdClass();
            $fones                = TSession::getValue( 'pessoa_fones' );
            $fone                 = $fones[ (int)$param[ 'fone_id' ] ];
            $data->fone_id        = $fone[ 'id' ];
            $data->fone_pessoa_id = $fone[ 'fone_pessoa_id' ];
            $data->fone_tipo      = $fone[ 'fone_tipo' ];
            $data->fone_numero    = $fone[ 'fone_numero' ];
            $data->tipo_nome      = $fone[ 'tipo_nome' ];
            
            TForm::sendData( 'form_'.__CLASS__, $data );
        }
        
        public function onDeleteTelefone( $param )
        {
            try {
                $data = $this->form->getData();
                
                TTransaction::open( 'afincco' );
                
                $id = (int)$param[ 'telefone_id' ];
                
                Pessoa_fone::where( 'id', '=', $id )->delete();
                
                $fones = TSession::getValue( 'pessoa_fones' );
                unset( $fones[ $id ] );
                TSession::setValue( 'pessoa_fones', $fones );
                
                $data->fone_id        = '';
                $data->fone_pessoa_id = '';
                $data->fone_tipo      = '';
                $data->fone_numero    = '';
                $data->tipo_nome      = '';
                
                $this->form->setData( $data );
                
                TTransaction::close();
                
                $this->onReload( $param );
                
            } catch ( Exception $e ) {
                $this->form->setData( $this->form->getData() );
                TTransaction::close();
                new TMessage( 'error', $e->getMessage() );
                $this->onReload( $param );
            }
        }
        
        function onReload( $param )
        {
            
            $fones = TSession::getValue( 'pessoa_fones' );
            
            $this->grid_fones->clear();
            
            if ( $fones ) {
                foreach ( $fones as $fone_id => $fone ) {
                    $item                 = new StdClass();
                    $item->fone_id        = $fone[ 'fone_id' ];
                    $item->fone_pessoa_id = $fone[ 'fone_pessoa_id' ];
                    $item->fone_tipo      = $fone[ 'fone_tipo' ];
                    $item->tipo_nome      = $fone[ 'tipo_nome' ];
                    $item->fone_numero    = $fone[ 'fone_numero' ];
                    $row                  = $this->grid_fones->addItem( $item );
                    $row->onmouseover     = '';
                    $row->onmouseout      = '';
                }
            }
            $this->loaded = TRUE;
        }
        
        public function onAddFone( $param )
        {
            try {
                $data = $this->form->getData();
                
                if ( ( !$data->fone_tipo ) || ( !$data->fone_numero ) ) {
                    throw new Exception( 'Os campos TIPO E NUMERO são obrigatórios' );
                }
                
                TTransaction::open( 'afincco' );
                
                $id = (int)$data->fone_id;
                
                if ( empty( $data->id ) ) {
                    $object = $this->form->getData( 'pessoa' );
                    $object->store();
                    $data->id = $object->id;
                }
                
                $fone                 = new Pessoa_fone( $id );
                $fone->id             = $id;
                $fone->fone_pessoa_id = $data->id;
                $fone->fone_tipo      = $data->fone_tipo;
                $fone->fone_numero    = $data->fone_numero;
                $fone->store();
                
                $fones = TSession::getValue( 'pessoa_fones' );
                
                $id                               = $fone->id;
                $fones[ $id ]                     = $fone->toArray();
                $fones[ $id ][ 'fone_id' ]        = $fone->id;
                $fones[ $id ][ 'fone_pessoa_id' ] = $fone->fone_pessoa_id;
                $fones[ $id ][ 'fone_tipo' ]      = $fone->fone_tipo;
                $fones[ $id ][ 'fone_numero' ]    = $fone->fone_numero;
                $fones[ $id ][ 'tipo_nome' ]      = $fone->tipo_nome;
                
                TSession::setValue( 'pessoa_fones', $fones );
                
                $data->fone_id        = '';
                $data->fone_pessoa_id = '';
                $data->fone_tipo      = '';
                $data->fone_numero    = '';
                $data->tipo_nome      = '';
                
                $this->form->setData( $data );
                
                TTransaction::close();
                
                $this->onReload( $param );
                $this->subform->setCurrentPage( 1 );
            } catch ( Exception $e ) {
                $this->form->setData( $this->form->getData() );
                TTransaction::close();
                new TMessage( 'error', $e->getMessage() );
                $this->onReload( $param );
            }
        }
        
        public function onEdit( $param )
        {
            try {
                if ( isset( $param[ 'key' ] ) ) {
                    
                    $key = $param[ 'key' ];
                    TTransaction::open( 'afincco' );
                    
                    $object = new pessoa( $key );
                    
                    $fones = $object->get_telefones();
                    
                    $session_fones = [];
                    
                    if ( !empty( $fones[ 0 ] ) ) {
                        foreach ( $fones as $fone ) {
                            $key                                       = $fone->id;
                            $session_fones[ $key ]                     = $fone->toArray();
                            $session_fones[ $key ][ 'fone_id' ]        = $fone->id;
                            $session_fones[ $key ][ 'fone_pessoa_id' ] = $fone->fone_pessoa_id;
                            $session_fones[ $key ][ 'fone_tipo' ]      = $fone->fone_tipo;
                            $session_fones[ $key ][ 'fone_numero' ]    = $fone->fone_numero;
                            $session_fones[ $key ][ 'tipo_nome' ]      = $fone->tipo_nome;
                        }
                    }
                    
                    TSession::setValue( 'pessoa_fones', $session_fones );
                    
                    $this->form->setData( $object );
                    TTransaction::close();
                    
                    TScript::create( "$('input[name=\"documento\"]').keyup();" );
                    $this->onReload( $param );
                } else {
                    TSession::setValue( 'pessoa_fones', NULL );
                    $this->form->clear();
                    $this->onReload( $param );
                    $this->form->setCurrentPage( 0 );
                }
            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();
            }
        }
        
    }
