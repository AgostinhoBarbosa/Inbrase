<?php
    
    use Adianti\Wrapper\BootstrapFormBuilder;
    
    /**
     * SystemUserForm
     *
     * @version    1.0
     * @package    control
     * @subpackage admin
     * @author     Pablo Dall'Oglio
     * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
     * @license    http://www.adianti.com.br/framework-license
     */
    class SystemUserForm extends TPage
    {
        protected $form; // form
        protected $program_list;

        function __construct()
        {
            parent::__construct();

            parent::setTargetContainer( 'adianti_right_panel' );

            $this->form = new BootstrapFormBuilder( 'form_System_user' );
            $this->form->setFieldSizes( '100%' );
            $this->form->setFormTitle( _t( 'User' ) );

            $id           = new TEntry( 'id' );
            $name         = new TEntry( 'name' );
            $login        = new TEntry( 'login' );
            $password     = new TPassword( 'password' );
            $repassword   = new TPassword( 'repassword' );
            $email        = new TEntry( 'email' );
            $unit_id      = new TDBCombo( 'system_unit_id', 'permission', 'SystemUnit', 'id', 'name' );
            $groups       = new TDBCheckGroup( 'groups', 'permission', 'SystemGroup', 'id', 'name' );
            $frontpage_id = new TDBUniqueSearch( 'frontpage_id', 'permission', 'SystemProgram', 'id', 'name', 'name' );
            $units        = new TDBCheckGroup( 'units', 'permission', 'SystemUnit', 'id', 'name' );

            $units->setLayout( 'horizontal' );
            if ( $units->getLabels() ) {
                foreach ( $units->getLabels() as $label ) {
                    $label->setSize( 200 );
                }
            }

            $groups->setLayout( 'horizontal' );
            if ( $groups->getLabels() ) {
                foreach ( $groups->getLabels() as $label ) {
                    $label->setSize( 200 );
                }
            }

            /* BOTÕES */
            $this->form->addAction( _t( 'Save' ), new TAction( [$this, 'onSave'] ), 'far:save blue' );
            $this->form->addActionLink( _t( 'New' ), new TAction( [$this, 'onEdit'] ), 'fa:eraser red' );
            $this->form->addActionLink( 'Retornar', new TAction( ['SystemUserList', 'onReload'] ), 'fa: fa-times green' );
            /* BOTÕES */

            $id->setEditable( FALSE );

            $name->addValidation( _t( 'Name' ), new TRequiredValidator() );
            $login->addValidation( 'Login', new TRequiredValidator() );
            $email->addValidation( 'Email', new TEmailValidator() );

            $campo_codigo  = [new TLabel( 'Código' ), $id];
            $campo_nome    = [new TLabel( _t( 'Name' ) ), $name];
            $campo_login   = [new TLabel( _t( 'Login' ) ), $login];
            $campo_email   = [new TLabel( _t( 'Email' ) ), $email];
            $campo_unidade = [new TLabel( _t( 'Main unit' ) ), $unit_id];
            $campo_page    = [new TLabel( _t( 'Front page' ) ), $frontpage_id];
            $campo_senha   = [new TLabel( _t( 'Password' ) ), $password];
            $campo_senha1  = [new TLabel( _t( 'Password confirmation' ) ), $repassword];

            $this->form->addFields( $campo_codigo, $campo_nome )->layout  = ['col-sm-6', 'col-sm-6'];
            $this->form->addFields( $campo_login, $campo_email )->layout  = ['col-sm-6', 'col-sm-6'];
            $this->form->addFields( $campo_unidade, $campo_page )->layout = ['col-sm-6', 'col-sm-6'];
            $this->form->addFields( $campo_senha, $campo_senha1 )->layout = ['col-sm-6', 'col-sm-6'];

            $this->form->addFields( [new TFormSeparator( _t( 'Units' ) )] );
            $this->form->addFields( [$units] );
            $this->form->addFields( [new TFormSeparator( _t( 'Groups' ) )] );
            $this->form->addFields( [$groups] );

            $search              = new TEntry( 'search' );
            $search->placeholder = _t( 'Search' );
            $search->style       = 'width:50%;margin-left: 4px; border-radius: 4px';

            $this->program_list = new TCheckList( 'program_list' );
            $this->program_list->setIdColumn( 'id' );
            $this->program_list->addColumn( 'id', 'ID', 'center', '10%' );
            $this->program_list->addColumn( 'name', _t( 'Name' ) . $search->getContents(), 'left', '50%' );
            $col_program = $this->program_list->addColumn( 'controller', _t( 'Menu path' ), 'left', '40%' );
            $col_program->enableAutoHide( 500 );
            $this->program_list->setHeight( 150 );
            $this->program_list->makeScrollable();

            $col_program->setTransformer( function ( $value, $object, $row )
            {
                $menuparser = new TMenuParser( 'menu.xml' );
                $paths      = $menuparser->getPath( $value );

                if ( $paths ) {
                    return implode( ' &raquo; ', $paths );
                }
            } );

            $this->program_list->enableSearch( $search, 'name' );

            $this->form->addFields( [new TFormSeparator( _t( 'Programs' ) )] );
            $this->form->addFields( [$this->program_list] );

            TTransaction::open( 'permission' );
            $this->program_list->addItems( SystemProgram::get() );
            TTransaction::close();

            $container        = new TVBox();
            $container->style = 'width: 100%';
            $container->add( $this->form );

            // add the container to the page
            parent::add( $container );
        }

        /**
         * Save user data
         */
        public function onSave( $param )
        {
            try {
                // open a transaction with database 'permission'
                TTransaction::open( 'permission' );

                $data = $this->form->getData();
                $this->form->setData( $data );

                $object = new SystemUser();
                $object->fromArray( (array) $data );

                $senha = $object->password;

                if ( empty( $object->login ) ) {
                    throw new Exception( TAdiantiCoreTranslator::translate( 'The field ^1 is required', _t( 'Login' ) ) );
                }

                if ( empty( $object->id ) ) {
                    if ( SystemUser::newFromLogin( $object->login ) instanceof SystemUser ) {
                        throw new Exception( _t( 'An user with this login is already registered' ) );
                    }

                    if ( SystemUser::newFromEmail( $object->email ) instanceof SystemUser ) {
                        throw new Exception( _t( 'An user with this e-mail is already registered' ) );
                    }

                    if ( empty( $object->password ) ) {
                        throw new Exception( TAdiantiCoreTranslator::translate( 'The field ^1 is required', _t( 'Password' ) ) );
                    }

                    $object->active = 'Y';
                }

                if ( $object->password ) {
                    if ( $object->password !== $param[ 'repassword' ] ) {
                        throw new Exception( _t( 'The passwords do not match' ) );
                    }

                    $object->password = md5( $object->password );
                } else {
                    unset( $object->password );
                }

                $object->store();
                $object->clearParts();

                if ( ! empty( $data->groups ) ) {
                    foreach ( $data->groups as $group_id ) {
                        $object->addSystemUserGroup( new SystemGroup( $group_id ) );
                    }
                }

                if ( ! empty( $data->units ) ) {
                    foreach ( $param[ 'units' ] as $unit_id ) {
                        $object->addSystemUserUnit( new SystemUnit( $unit_id ) );
                    }
                }

                if ( ! empty( $data->program_list ) ) {
                    foreach ( $data->program_list as $program_id ) {
                        $object->addSystemUserProgram( new SystemProgram( $program_id ) );
                    }
                }

                $data     = new stdClass();
                $data->id = $object->id;
                TForm::sendData( 'form_System_user', $data );

                TTransaction::close();

                $pos_action = new TAction( ['SystemUserList', 'onReload'] );
                new TMessage( 'info', TAdiantiCoreTranslator::translate( 'Record saved' ), $pos_action );
            } catch ( Exception $e ) // in case of exception
            {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();
            }
        }

        /**
         * method onEdit()
         * Executed whenever the user clicks at the edit button da datagrid
         */
        function onEdit( $param )
        {
            try {
                if ( isset( $param[ 'key' ] ) ) {
                    // get the parameter $key
                    $key = $param[ 'key' ];

                    // open a transaction with database 'permission'
                    TTransaction::open( 'permission' );

                    // instantiates object System_user
                    $object = new SystemUser( $key );

                    unset( $object->password );

                    $groups = [];
                    $units  = [];

                    if ( $groups_db = $object->getSystemUserGroups() ) {
                        foreach ( $groups_db as $group ) {
                            $groups[] = $group->id;
                        }
                    }

                    if ( $units_db = $object->getSystemUserUnits() ) {
                        foreach ( $units_db as $unit ) {
                            $units[] = $unit->id;
                        }
                    }

                    $program_ids = [];
                    foreach ( $object->getSystemUserPrograms() as $program ) {
                        $program_ids[] = $program->id;
                    }

                    $object->program_list = $program_ids;
                    $object->groups       = $groups;
                    $object->units        = $units;

                    // fill the form with the active record data
                    $this->form->setData( $object );

                    // close the transaction
                    TTransaction::close();
                } else {
                    $this->form->clear();
                }
            } catch ( Exception $e ) // in case of exception
            {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();
            }
        }
    }
