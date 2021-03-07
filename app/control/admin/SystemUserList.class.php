<?php
    
    use Adianti\Widget\Datagrid\TDataGridColumn;
    use Adianti\Wrapper\BootstrapDatagridWrapper;
    
    /**
     * SystemUserList
     *
     * @version    1.0
     * @package    control
     * @subpackage admin
     * @author     Pablo Dall'Oglio
     * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
     * @license    http://www.adianti.com.br/framework-license
     */
    class SystemUserList extends TStandardList
    {

        public function __construct()
        {
            parent::__construct();

            parent::setDatabase( 'permission' );
            parent::setActiveRecord( 'SystemUser' );
            parent::setDefaultOrder( 'id', 'asc' );
            parent::addFilterField( 'id', '=', 'id' );
            parent::addFilterField( 'name', 'like', 'name' );
            parent::addFilterField( 'email', 'like', 'email' );
            parent::addFilterField( 'active', '=', 'active' );

            $this->form = new BootstrapFormBuilder( 'form_search_SystemUser' );
            $this->form->setFormTitle( _t( 'Users' ) );

            $id     = new TEntry( 'id' );
            $name   = new TEntry( 'name' );
            $email  = new TEntry( 'email' );
            $active = new TCombo( 'active' );

            $active->addItems( ['Y' => _t( 'Yes' ), 'N' => _t( 'No' )] );

            $campo_codigo = [new TLabel( 'Código' ), $id];
            $campo_nome   = [new TLabel( _t( 'Name' ) ), $name];
            $campo_email  = [new TLabel( _t( 'Email' ) ), $email];
            $campo_ativo  = [new TLabel( _t( 'Active' ) ), $active];

            $this->form->addFields( $campo_codigo, $campo_nome, $campo_email, $campo_ativo )->layout = ['col-sm-1',
                                                                                                        'col-sm-3',
                                                                                                        'col-sm-3',
                                                                                                        'col-sm-1'];
            $this->form->setData( TSession::getValue( 'SystemUser_filter_data' ) );

            $this->form->addAction( _t( 'Find' ), new TAction( array($this, 'onSearch') ), 'fa:search blue' );
            $this->form->addActionlink( _t( 'New' ), new TAction( ['SystemUserForm', 'onEdit'], ['register_state' => 'false'] ), 'fa:eraser red' );

            $this->datagrid        = new BootstrapDatagridWrapper( new TDataGrid() );

            $column_id     = new TDataGridColumn( 'id', 'Código', 'center', '5%' );
            $column_name   = new TDataGridColumn( 'name', _t( 'Name' ), 'left', '40%' );
            $column_login  = new TDataGridColumn( 'login', _t( 'Login' ), 'left', '15%' );
            $column_email  = new TDataGridColumn( 'email', _t( 'Email' ), 'left', '30%' );
            $column_active = new TDataGridColumn( 'active', _t( 'Active' ), 'center', '10%' );

            $column_login->enableAutoHide( 500 );
            $column_email->enableAutoHide( 500 );
            $column_active->enableAutoHide( 500 );

            $this->datagrid->addColumn( $column_id );
            $this->datagrid->addColumn( $column_name );
            $this->datagrid->addColumn( $column_login );
            $this->datagrid->addColumn( $column_email );
            $this->datagrid->addColumn( $column_active );

            $column_active->setTransformer( function ( $value, $object, $row )
            {
                if ( $value == 'N' ) {
                    return "<img src='app/images/icons/ico_no.jpg' />";
                } else {
                    return "<img src='app/images/icons/ico_ok.png' />";
                }
            } );

            $order_id = new TAction( [$this, 'onReload'] );
            $order_id->setParameter( 'order', 'id' );
            $column_id->setAction( $order_id );

            $order_name = new TAction( [$this, 'onReload'] );
            $order_name->setParameter( 'order', 'name' );
            $column_name->setAction( $order_name );

            $order_login = new TAction( [$this, 'onReload'] );
            $order_login->setParameter( 'order', 'login' );
            $column_login->setAction( $order_login );

            $order_email = new TAction( [$this, 'onReload'] );
            $order_email->setParameter( 'order', 'email' );
            $column_email->setAction( $order_email );

            $action_edit   = new TDataGridAction( ['SystemUserForm', 'onEdit'], ['key' => '{id}', 'register_state' => 'false'] );
            $action_del    = new TDataGridAction( [$this, 'onDelete'], ['key' => '{id}', 'register_state' => 'false'] );
            $action_clone  = new TDataGridAction( [$this, 'onClone'], ['key' => '{id}', 'register_state' => 'false'] );
            $action_onoff  = new TDataGridAction( [$this, 'onTurnOnOff'], ['key' => '{id}', 'register_state' => 'false'] );
            $action_person = new TDataGridAction( [$this, 'onImpersonation'], ['register_state' => 'false'] );
            $action_person->setFields( ['id', 'login'] );

            $this->datagrid->addAction( $action_edit, _t( 'Edit' ), 'far:edit blue fa-lg' );
            $this->datagrid->addAction( $action_del, _t( 'Delete' ), 'far:trash-alt red fa-lg' );
            $this->datagrid->addAction( $action_clone, _t( 'Clone' ), 'far:clone green fa-lg' );
            $this->datagrid->addAction( $action_onoff, _t( 'Activate/Deactivate' ), 'fa:power-off orange fa-lg' );
            $this->datagrid->addAction( $action_person, _t( 'Impersonation' ), 'far:user-circle gray fa-lg' );

            $this->datagrid->createModel();

            $this->pageNavigation = new TPageNavigation();
            $this->pageNavigation->setAction( new TAction( [$this, 'onReload'] ) );
            $this->pageNavigation->setWidth( $this->datagrid->getWidth() );

            $panel                                = new TPanelGroup();
            $panel->add( $this->datagrid )->style = 'overflow-x:auto';
            $panel->addFooter( $this->pageNavigation );

            // header actions
            $dropdown = new TDropDown( _t( 'Export' ), 'fa:list' );
            $dropdown->setPullSide( 'right' );
            $dropdown->setButtonClass( 'btn btn-default waves-effect dropdown-toggle' );
            $dropdown->addAction( _t( 'Save as CSV' ), new TAction( [$this,
                                                                     'onExportCSV'], ['register_state' => 'false',
                                                                                      'static'         => '1'] ), 'fa:table fa-fw blue' );
            $dropdown->addAction( _t( 'Save as PDF' ), new TAction( [$this,
                                                                     'onExportPDF'], ['register_state' => 'false',
                                                                                      'static'         => '1'] ), 'far:file-pdf fa-fw red' );
            $dropdown->addAction( _t( 'Save as XML' ), new TAction( [$this,
                                                                     'onExportXML'], ['register_state' => 'false',
                                                                                      'static'         => '1'] ), 'fa:code fa-fw green' );
            $panel->addHeaderWidget( $dropdown );

            $container        = new TVBox();
            $container->style = 'width: 100%';
            $container->add( $this->form );
            $container->add( $panel );

            parent::add( $container );
        }

        /**
         * Turn on/off an user
         */
        public function onTurnOnOff( $param )
        {
            try {
                TTransaction::open( 'permission' );
                $user = SystemUser::find( $param[ 'id' ] );
                if ( $user instanceof SystemUser ) {
                    $user->active = $user->active == 'Y' ? 'N' : 'Y';
                    $user->store();
                }

                TTransaction::close();

                $this->onReload( $param );
            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();
            }
        }

        /**
         * Clone group
         */
        public function onClone( $param )
        {
            try {
                TTransaction::open( 'permission' );
                $user = new SystemUser( $param[ 'id' ] );
                $user->cloneUser();
                TTransaction::close();

                $this->onReload();
            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();
            }
        }

        /**
         * Impersonation user
         */
        public function onImpersonation( $param )
        {
            try {
                TTransaction::open( 'permission' );
                TSession::regenerate();
                $user = SystemUser::validate( $param[ 'login' ] );
                ApplicationAuthenticationService::loadSessionVars( $user );
                SystemAccessLogService::registerLogin( TRUE );
                AdiantiCoreApplication::gotoPage( 'EmptyPage' );
                TTransaction::close();
            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();
            }
        }
    }
