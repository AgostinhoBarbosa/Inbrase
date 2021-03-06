<?php
/**
 * SystemGroupForm
 *
 * @version    1.0
 * @package    control
 * @subpackage admin
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class SystemGroupForm extends TPage
{
    protected $form; // form
    protected $program_list;
    protected $user_list;

    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();

        parent::setTargetContainer( 'adianti_right_panel' );

        // creates the form
        $this->form = new BootstrapFormBuilder( 'form_System_group' );
        $this->form->setFieldSizes( '100%' );
        $this->form->setFormTitle( _t( 'Group' ) );

        $id   = new TEntry( 'id' );
        $name = new TEntry( 'name' );

        $name->addValidation( 'name', new TRequiredValidator() );

        $id->setEditable( false );

        $campo_codigo = [new TLabel( 'Código' ), $id];
        $campo_nome   = [new TLabel( _t( 'Name' ) ), $name];

        $this->form->addFields( $campo_codigo, $campo_nome );

        $search_program              = new TEntry( 'search' );
        $search_program->placeholder = _t( 'Search' );
        $search_program->style       = 'margin-left: 4px; border-radius: 4px';

        $this->program_list = new TCheckList( 'program_list' );
        $this->program_list->setIdColumn( 'id' );
        $this->program_list->addColumn( 'id', 'Código', 'center', '10%' );
        $this->program_list->addColumn( 'name', _t( 'Name' ) . $search_program->getContents(), 'left', '50%' );
        $col_program = $this->program_list->addColumn( 'controller', _t( 'Menu path' ), 'left', '40%' );
        $col_program->enableAutoHide( 500 );
        $this->program_list->setHeight( 350 );
        $this->program_list->makeScrollable();

        $col_program->setTransformer( function ( $value, $object, $row )
        {
            $menuparser = new TMenuParser( 'menu.xml' );
            $paths      = $menuparser->getPath( $value );

            if ( $paths ) {
                return implode( ' &raquo; ', $paths );
            }
        } );

        $this->program_list->enableSearch( $search_program, 'name' );

        $search_user              = new TEntry( 'search' );
        $search_user->placeholder = _t( 'Search' );
        $search_user->style       = 'margin-left: 4px; border-radius: 4px';

        $this->user_list = new TCheckList( 'user_list' );
        $this->user_list->setIdColumn( 'id' );
        $this->user_list->addColumn( 'id', 'Código', 'center', '10%' );
        $this->user_list->addColumn( 'name', _t( 'Name' ) . $search_user->getContents(), 'left', '90%' );
        $this->user_list->setHeight( 350 );
        $this->user_list->makeScrollable();

        $this->user_list->enableSearch( $search_user, 'name' );

        $subform = new BootstrapFormBuilder( 'subform', true );
        //$subform->setFieldSizes('100%');
        $subform->setProperty( 'style', 'border:none; box-shadow:none' );

        $subform->appendPage( _t( 'Programs' ) );
        $subform->addFields( [$this->program_list] );

        $subform->appendPage( _t( 'Users' ) );
        $subform->addFields( [$this->user_list] );

        $this->form->addContent( [$subform] );

        TTransaction::open( 'permission' );
        $this->program_list->addItems( SystemProgram::get() );
        $this->user_list->addItems( SystemUser::get() );
        TTransaction::close();

        $btn = $this->form->addAction( _t( 'Save' ), new TAction( array($this, 'onSave') ), 'far:save blue' );
        $this->form->addActionLink( _t( 'Clear' ), new TAction( array($this, 'onEdit') ), 'fa:eraser red' );
        $this->form->addActionLink( _t( 'Back' ), new TAction( array('SystemGroupList', 'onReload') ), 'far:arrow-alt-circle-left green' );

        $container        = new TVBox();
        $container->style = 'width:100%';
        $container->add( $this->form );

        parent::add( $container );
    }

    /**
     * method onSave()
     * Executed whenever the user clicks at the save button
     */
    public function onSave( $param )
    {
        try {
            // open a transaction with database 'permission'
            TTransaction::open( 'permission' );

            $data = $this->form->getData();
            $this->form->setData( $data );

            // get the form data into an active record System_group
            $object = new SystemGroup();
            $object->fromArray( (array) $data );
            $object->store();
            $object->clearParts();

            if ( ! empty( $data->program_list ) ) {
                foreach ( $data->program_list as $program_id ) {
                    $object->addSystemProgram( new SystemProgram( $program_id ) );
                }
            }

            if ( ! empty( $data->user_list ) ) {
                foreach ( $data->user_list as $user_id ) {
                    $object->addSystemUser( new SystemUser( $user_id ) );
                }
            }

            $data     = new stdClass();
            $data->id = $object->id;
            TForm::sendData( 'form_System_group', $data );

            TTransaction::close(); // close the transaction
            $pos_action = new TAction( ['SystemGroupList', 'onReload'] );

            new TMessage( 'info', _t( 'Record saved' ), $pos_action ); // shows the success message
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    /**
     * method onEdit()
     * Executed whenever the user clicks at the edit button da datagrid
     */
    function onEdit($param)
    {
        try
        {
            if (isset($param['key'])) {
                // get the parameter $key
                $key = $param[ 'key' ];

                // open a transaction with database 'permission'
                TTransaction::open( 'permission' );

                // instantiates object System_group
                $object = new SystemGroup( $key );

                $program_ids = array();
                foreach ( $object->getSystemPrograms() as $program ) {
                    $program_ids[] = $program->id;
                }

                $object->program_list = $program_ids;


                $user_ids = array();
                foreach ( $object->getSystemUsers() as $user ) {
                    $user_ids[] = $user->id;
                }

                $object->user_list = $user_ids;

                // fill the form with the active record data
                $this->form->setData( $object );

                // close the transaction
                TTransaction::close();
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}
