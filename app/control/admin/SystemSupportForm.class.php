<?php
/**
 * SystemSupportForm
 *
 * @version    1.0
 * @package    control
 * @subpackage admin
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class SystemSupportForm extends TWindow
{
    protected $form;

    function __construct()
    {
        parent::__construct();
        parent::setSize( 800, null );
        parent::setTitle( _t( 'Open ticket' ) );
        parent::setProperty( 'class', 'window_modal' );

        $this->form        = new BootstrapFormWrapper( new TQuickForm( 'form_SystemMessage' ) );
        $this->form->style = 'display: table;width:100%';
        $this->form->setProperty( 'style', 'border:0' );

        $this->form->setFormTitle( 'Abrir Ticket Suporte' );

        $subject = new TEntry( 'subject' );
        $message = new TText( 'message' );

        $this->form->addQuickField( _t( 'Title' ), $subject, '90%', new TRequiredValidator() );
        $this->form->addQuickField(_t('Message'), $message,  '90%', new TRequiredValidator );
        $message->setSize( '90%', '100' );

        if ( ! empty( $id ) ) {
            $id->setEditable( FALSE );
        }

        $this->form->addQuickAction( _t( 'Send' ), new TAction( array($this, 'onSend') ), 'far:envelope blue' );
        $this->form->addQuickAction( _t( 'Clear form' ), new TAction( array($this, 'onClear') ), 'fa:eraser red' );

        $container        = new TVBox();
        $container->style = 'width: 90%; margin:40px';
        $container->add( $this->form );

        parent::add( $container );
    }

    public function onClear($param)
    {
        $this->form->clear();
    }

    public function onSend($param)
    {
        try
        {
            $data = $this->form->getData();
            $this->form->validate();

            TTransaction::open('permission');
            $preferences = SystemPreference::getAllPreferences();
            TTransaction::close();

            MailService::send( trim($preferences['mail_support']), "[".EMPRESA."]".$data->subject, $data->message );

            new TMessage('info', _t('Message sent successfully'));
        }
        catch (Exception $e)
        {
            $object = $this->form->getData();

            $this->form->setData($object);

            new TMessage('error', $e->getMessage());

            TTransaction::rollback();
        }
    }
}
