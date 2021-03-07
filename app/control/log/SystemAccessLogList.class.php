<?php
    
    use Adianti\Widget\Form\TDateTime;
    use Adianti\Wrapper\BootstrapFormBuilder;
    
    /**
     * SystemAccessLogList
     *
     * @version    1.0
     * @package    control
     * @subpackage log
     * @author     Pablo Dall'Oglio
     * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
     * @license    http://www.adianti.com.br/framework-license
     */
    class SystemAccessLogList extends TStandardList
    {
        public function __construct()
        {
            parent::__construct();

            parent::setDatabase( 'log' );
            parent::setActiveRecord( 'SystemAccessLog' );
            parent::setDefaultOrder( 'id', 'desc' );
            parent::addFilterField( 'login', 'like' );
            parent::setLimit( 20 );

            $this->form = new BootstrapFormBuilder( 'form_search_SystemAccessLog' );
            $this->form->setFieldSizes( '100%' );
            $this->form->setFormTitle( 'Access Log' );
            $this->form->enableCSRFProtection();

            $login = new TEntry( 'login' );

            $this->form->addFields( [new TLabel( _t( 'Login' ) ), $login] )->layout = ['col-sm-4'];

            $this->form->setData( TSession::getValue( 'SystemAccessLog_filter_data' ) );

            $this->form->addAction( _t( 'Find' ), new TAction( array($this, 'onSearch') ), 'fa:search blue' );

            $this->datagrid            = new BootstrapDatagridWrapper( new TQuickGrid() );
            $this->datagrid->style     = 'width: 100%';
            $this->datagrid->datatable = 'true';


            $id          = $this->datagrid->addQuickColumn( 'Código', 'id', 'center', '5%' );
            $sessionid   = $this->datagrid->addQuickColumn( 'Sessão', 'sessionid', 'left', '5%' );
            $login       = $this->datagrid->addQuickColumn( _t( 'Login' ), 'login', 'center', '10%' );
            $login_time  = $this->datagrid->addQuickColumn( 'Horario Login', 'login_time', 'center', '20%' );
            $logout_time = $this->datagrid->addQuickColumn( 'Horario Saida', 'logout_time', 'center', '20%' );
            $access_ip   = $this->datagrid->addQuickColumn( 'IP', 'access_ip', 'center', '20%' );


            $action1 = new TDataGridAction( ['SystemSqlLogList', 'filterSession'], ['session_id' => '{sessionid}'] );
            $action2 = new TDataGridAction( ['SystemChangeLogView', 'filterSession'], ['session_id' => '{sessionid}'] );
            $action3 = new TDataGridAction( ['SystemRequestLogList', 'filterSession'], ['session_id' => '{sessionid}'] );

            $this->datagrid->addAction( $action1, _t( 'SQL Log' ), 'fa:database blue fa-lg' );
            $this->datagrid->addAction( $action2, _t( 'Change Log' ), 'fa:film green fa-lg' );
            $this->datagrid->addAction( $action3, _t( 'Request Log' ), 'fa:globe orange fa-lg' );


            $login->setTransformer( function ( $value, $object, $row )
            {
                if ( $object->impersonated == 'Y' ) {
                    $div        = new TElement( 'span' );
                    $div->class = "label label-info";
                    $div->style = "text-shadow:none; font-size:12px";
                    $div->add( _t( 'Impersonated' ) );

                    return $value . ' ' . $div;
                }
                return $value;
            } );

            $login_time->setTransformer( function ( $value, $object, $row )
            {
                if ( date( 'w', strtotime( $value ) ) == 0 ) {
                    $row->style = 'background-color: red;color: white';
                }
                if ( date( 'w', strtotime( $value ) ) == 6 ) {
                    $row->style = 'background-color: yellow;';
                }
                return TDateTime::convertToMask( $value, 'yyyy-mm-dd hh:ii:ss', 'dd/mm/yyyy hh:ii:ss' );
            } );
            $logout_time->setTransformer( function ( $value, $object, $row )
            {
                return TDateTime::convertToMask( $value, 'yyyy-mm-dd hh:ii:ss', 'dd/mm/yyyy hh:ii:ss' );
            } );

            $order_id = new TAction( array($this, 'onReload') );
            $order_id->setParameter( 'order', 'id' );
            $id->setAction( $order_id );

            $this->datagrid->createModel();

            $this->pageNavigation = new TPageNavigation();
            $this->pageNavigation->setAction( new TAction( array($this, 'onReload') ) );
            $this->pageNavigation->setWidth( $this->datagrid->getWidth() );

            $panel = new TPanelGroup();
            $panel->add( $this->datagrid );
            $panel->addFooter( $this->pageNavigation );

            $container        = new TVBox();
            $container->style = 'width: 100%';
            $container->add( $this->form );
            $container->add( $panel );

            parent::add( $container );
        }
    }
