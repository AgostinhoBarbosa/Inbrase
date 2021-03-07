<?php
    
    use Adianti\Core\AdiantiApplicationConfig;
    
    /**
     * SystemUnitForm
     *
     * @version    1.0
     * @package    control
     * @subpackage admin
     * @author     Pablo Dall'Oglio
     * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
     * @license    http://www.adianti.com.br/framework-license
     */
    class SystemUnitForm extends TStandardForm
    {
        function __construct()
        {
            parent::__construct();

            parent::setTargetContainer( 'adianti_right_panel' );

            $ini = AdiantiApplicationConfig::get();

            $this->setDatabase( 'permission' );
            $this->setActiveRecord( 'SystemUnit' );

            $this->form = new BootstrapFormBuilder( 'form_SystemUnit' );
            $this->form->setFieldSizes( '100%' );
            $this->form->setFormTitle( _t( 'Unit' ) );

            $id   = new TEntry( 'id' );
            $name = new TEntry( 'name' );

            $campo_codigo = [new TLabel( 'Código' ), $id];
            $campo_nome   = [new TLabel( _t( 'Name' ) ), $name];

            if ( ! empty( $ini[ 'general' ][ 'multi_database' ] ) and $ini[ 'general' ][ 'multi_database' ] == '1' ) {
                $database = new TCombo( 'connection_name' );
                $database->addItems( SystemDatabaseInformationService::getConnections() );
                $campo_banco = [new TLabel( _t( 'Database' ) ), $database];
                $this->form->addFields( $campo_codigo, $campo_nome, $campo_banco );
            } else {
                $this->form->addFields( $campo_codigo, $campo_nome );
            }

            $id->setEditable( FALSE );
            $name->addValidation( _t( 'Name' ), new TRequiredValidator() );

            $btn = $this->form->addAction( _t( 'Save' ), new TAction( array($this, 'onSave') ), 'far:save blue' );
            $this->form->addActionLink( _t( 'Clear' ), new TAction( array($this, 'onClear') ), 'fa:eraser red' );
            $this->form->addActionLink( _t( 'Back' ), new TAction( array('SystemUnitList', 'onReload') ), 'far:arrow-alt-circle-left green' );

            $pos_action = new TAction( ['SystemUnitList', 'onReload'] );
            self::setAfterSaveAction( $pos_action );

            $container        = new TVBox();
            $container->style = 'width: 100%';
            $container->add( $this->form );

            parent::add( $container );
        }
    }
