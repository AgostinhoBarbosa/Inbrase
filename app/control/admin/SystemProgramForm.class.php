<?php
    
    use Adianti\Base\TStandardForm;
    use Adianti\Widget\Form\TLabel;
    use Adianti\Wrapper\BootstrapFormBuilder;
    
    /**
     * SystemProgramForm
     *
     * @version    1.0
     * @package    control
     * @subpackage admin
     * @author     Pablo Dall'Oglio
     * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
     * @license    http://www.adianti.com.br/framework-license
     */
    class SystemProgramForm extends TStandardForm
    {

        function __construct( $param )
        {
            parent::__construct();

            parent::setTargetContainer( 'adianti_right_panel' );

            $this->form = new BootstrapFormBuilder( 'form_SystemProgram' );
            $this->form->setFormTitle( _t( 'Program' ) );

            parent::setDatabase( 'permission' );
            parent::setActiveRecord( 'SystemProgram' );

            $id         = new TEntry( 'id' );
            $controller = new TUniqueSearch( 'controller' );
            $name       = new TEntry( 'name' );
            $groups     = new TDBCheckGroup( 'groups', 'permission', 'SystemGroup', 'id', 'name' );

            $id->setEditable( FALSE );

            $controller->addItems( $this->getPrograms( empty( $param[ 'id' ] ) ) );
            $controller->setMinLength( 0 );
            $controller->setChangeAction( new TAction( [$this, 'onChangeController'] ) );
            $groups->setLayout( 'horizontal' );

            if ( $groups->getLabels() ) {
                foreach ( $groups->getLabels() as $label ) {
                    $label->setSize( 200 );
                }
            }

            $campo_codigo     = [new TLabel( 'Código' ), $id];
            $campo_controller = [new TLabel( _t( 'Controller' ) ), $controller];
            $campo_nome       = [new TLabel( _t( 'Name' ) ), $name];

            $this->form->addFields( $campo_codigo, $campo_controller, $campo_nome )->layout = ['col-md-2', 'col-md-5', 'col-md-5'];
            $this->form->addFields( [new TFormSeparator( _t( 'Groups' ) )] );
            $this->form->addFields( [$groups] );

//            $id->setSize( '30%' );
//            $name->setSize( '70%' );
//            $controller->setSize( '100%' );

            // validations
            $name->addValidation( _t( 'Name' ), new TRequiredValidator() );
            $controller->addValidation( ( 'Controller' ), new TRequiredValidator() );

            /* BOTÕES */
            $this->form->addAction( _t( 'Save' ), new TAction( array($this, 'onSave') ), 'far:save blue' );
            $this->form->addActionLink( _t( 'New' ), new TAction( array($this, 'onClear') ), 'fa:eraser red' );
            $this->form->addActionLink( 'Retornar', new TAction( [$this, 'onFechaRightPanel'] ), 'fa: fa-times green' );
            /* BOTÕES */

            $pos_action = new TAction( ['SystemProgramList', 'onReload'] );
            self::setAfterSaveAction( $pos_action );


            $container        = new TVBox();
            $container->style = 'width: 100%';
            $container->add( $this->form );

            parent::add( $container );
        }

        /**
         * Change controller, generate name
         */
        public static function onChangeController( $param )
        {
            if ( ! empty( $param[ 'controller' ] ) AND empty( $param[ 'name' ] ) ) {
                $obj       = new stdClass();
                $obj->name = preg_replace( '/([a-z])([A-Z])/', '$1 $2', $param[ 'controller' ] );
                TForm::sendData( 'form_SystemProgram', $obj );
            }
        }

        /**
         * Return all the programs under app/control
         */
        public function getPrograms( $just_new_programs = false )
        {
            try {
                TTransaction::open( 'permission' );
                $registered_programs = SystemProgram::getIndexedArray( 'id', 'controller' );
                TTransaction::close();

                $entries  = array();
                $iterator = new AppendIterator();
                $iterator->append( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( 'app/control' ), RecursiveIteratorIterator::CHILD_FIRST ) );
                $iterator->append( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( 'app/view' ), RecursiveIteratorIterator::CHILD_FIRST ) );

                foreach ( $iterator as $arquivo ) {
                    if ( substr( $arquivo, -4 ) == '.php' ) {
                        $name   = $arquivo->getFileName();
                        $pieces = explode( '.', $name );
                        $class  = (string) $pieces[ 0 ];

                        if ( $just_new_programs ) {
                            if ( ! in_array( $class, $registered_programs ) AND ! in_array( $class, array_keys( TApplication::getDefaultPermissions() ) ) AND substr( $class, 0, 6 ) !== 'System' ) {
                                $entries[ $class ] = $class;
                            }
                        } else {
                            $entries[ $class ] = $class;
                        }
                    }
                }

                ksort( $entries );
                return $entries;
            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );
            }
        }

        /**
         * method onEdit()
         * Executed whenever the user clicks at the edit button da datagrid
         *
         * @param  $param An array containing the GET ($_GET) parameters
         */
        public function onEdit( $param )
        {
            try {
                if ( isset( $param[ 'key' ] ) ) {
                    $key = $param[ 'key' ];

                    TTransaction::open( $this->database );
                    $class  = $this->activeRecord;
                    $object = new $class( $key );

                    $groups = array();

                    if ( $groups_db = $object->getSystemGroups() ) {
                        foreach ( $groups_db as $group ) {
                            $groups[] = $group->id;
                        }
                    }
                    $object->groups = $groups;
                    $this->form->setData( $object );

                    TTransaction::close();

                    return $object;
                } else {
                    $this->form->clear();
                }
            } catch ( Exception $e ) // in case of exception
            {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();
            }
        }

        /**
         * method onSave()
         * Executed whenever the user clicks at the save button
         */
        public function onSave()
        {
            try {
                TTransaction::open( $this->database );

                $data = $this->form->getData();

                $object             = new SystemProgram();
                $object->id         = $data->id;
                $object->name       = $data->name;
                $object->controller = $data->controller;

                $this->form->validate();
                $object->store();
                $data->id = $object->id;
                $this->form->setData( $data );

                $object->clearParts();

                if ( ! empty( $data->groups ) ) {
                    foreach ( $data->groups as $group_id ) {
                        $object->addSystemGroup( new SystemGroup( $group_id ) );
                    }
                }

                TTransaction::close();

                new TMessage( 'info', AdiantiCoreTranslator::translate( 'Record saved' ), $this->afterSaveAction );

                return $object;
            } catch ( Exception $e ) {
                // get the form data
                $object = $this->form->getData( $this->activeRecord );
                $this->form->setData( $object );
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();
            }
        }
    }
