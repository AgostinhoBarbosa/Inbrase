<?php
    
    use Adianti\Wrapper\BootstrapFormBuilder;
    
    /**
     * SystemDocumentUploadForm
     *
     * @version    1.0
     * @package    control
     * @subpackage communication
     * @author     Pablo Dall'Oglio
     * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
     * @license    http://www.adianti.com.br/framework-license
     */
    class SystemDocumentUploadForm extends TPage
    {
        protected $form; // form

        /**
         * Form constructor
         *
         * @param $param Request
         */
        public function __construct( $param )
        {
            parent::__construct();

            $this->form = new BootstrapFormBuilder( 'form_SystemUploadDocument' );
            $this->form->setFormTitle( _t( 'Send document' ) );

            $id       = new THidden( 'id' );
            $filename = new TFile( 'filename' );

            $filename->setService( 'SystemDocumentUploaderService' );

            $this->form->addFields( [new TLabel( _t( 'File' ) ), $filename, $id] );

            $filename->addValidation( _t( 'File' ), new TRequiredValidator() );

            $this->form->addAction( _t( 'Next' ), new TAction( [$this, 'onNext'] ), 'far:arrow-alt-circle-right blue' );

            $container        = new TVBox();
            $container->style = 'width: 100%';
            $container->add( $this->form );

            parent::add( $container );
        }

        public function onNew()
        {
        }

        public function onEdit( $param )
        {
            if ( $param[ 'id' ] ) {
                $obj     = new stdClass();
                $obj->id = $param[ 'id' ];
                $this->form->setData( $obj );
            }
        }

        /**
         * Save form data
         *
         * @param $param Request
         */
        public function onNext( $param )
        {
            try {
                $data = $this->form->getData(); // get form data as array
                $this->form->validate();        // validate form data
                TSession::setValue( 'SystemDocumentUpload_file', $data->filename );

                if ( $data->id ) {
                    $param[ 'key' ]           = $param[ 'id' ];
                    $param[ 'hasfile' ]       = '1';
                    $param[ 'regiter_state' ] = 'false';
                    AdiantiCoreApplication::loadPage( 'SystemDocumentForm', 'onEdit', $param );
                } else {
                    $param[ 'hasfile' ]       = '1';
                    $param[ 'regiter_state' ] = 'false';
                    AdiantiCoreApplication::loadPage( 'SystemDocumentForm', 'onEdit', $param );
                }
            } catch ( Exception $e ) // in case of exception
            {
                new TMessage( 'error', $e->getMessage() );      // shows the exception error message
                $this->form->setData( $this->form->getData() ); // keep form data
            }
        }
    }
