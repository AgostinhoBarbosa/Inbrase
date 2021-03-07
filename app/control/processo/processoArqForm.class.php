<?php
    
    use Adianti\Wrapper\BootstrapFormBuilder;
    
    class processoArqForm extends TWindow
{
    protected $form;

    use Adianti\Base\AdiantiStandardFormTrait;

    function __construct()
    {
        parent::__construct();
        parent::setTitle('Envio de Arquivo por E-Mail');
        parent::setSize(0.5, NULL);

        $this->setDatabase('afincco');
        $this->setActiveRecord('ProcessoArq');
    
        $this->form = new BootstrapFormBuilder( 'form_'.__CLASS__ );
        $this->form->setFormTitle('Arquivos do Processo');

        $id_processo = new TEntry('id_processo');
        $nome        = new TEntry('nome');
        $assunto     = new TEntry('assunto');
        $destino     = new TEntry('destino');
        $corpo       = new THtmlEditor('corpo');

        $id_processo->setEditable(FALSE);
        $nome->setEditable(FALSE);
        $corpo->setSize('100%', 300);

        $this->form->addQuickField('Processo',     $id_processo,  '50%' );
        $this->form->addQuickField('Arquivo',      $nome,         '100%' );
        $this->form->addQuickField('Destinatário', $destino,      '100%' );
        $this->form->addQuickField('Assunto',      $assunto,      '100%' );
        $this->form->addQuickField('Mensagem',     $corpo,        '100%' );

        if (!empty($id_arq))
        {
            $id_arq->setEditable(FALSE);
        }

        $this->form->addQuickAction('Enviar',    new TAction(array($this, 'onEnviarArquivo')), 'far:envelope green')->class = 'btn btn-sm btn-default';
        $this->form->addQuickAction('Retornar',  new TAction(array($this, 'onRetornar')), 'fa:table white')->class = 'btn btn-sm btn-primary';

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(TPanelGroup::pack('', $this->form));

        parent::add($container);
    }

    public function onRetornar( $param )
    {
        $this->closeWindow;
        $ret = array();
        $ret['key'] = $param['id_processo'];
        $ret['id']  = $param['id_processo'];
        AdiantiCoreApplication::loadPage('processoaForm', 'onEdit', $ret);
    }

    public static function onEnviarArquivo($param)
    {
        try {
            TTransaction::open('permission');

            $preferences = (object) SystemPreference::getAllPreferences();
            if ($preferences) {
                $source_file  = 'app/arquivos/'.$param['id_processo'].'/'.$param['nome'];

                if (file_exists($source_file))
                {
                    $mail           = new TMail;
                    $mail->setDebug(FALSE);
                    $mail->setFrom($preferences->mail_from);
                    $mail->setSubject($param['assunto']);
                    $mail->setHtmlBody($param['corpo']);
                    $mail->addAttach($source_file);
                    $mail->addAddress($param['destino']);

                    //$mail->addAddress('agostinho@softgt.com.br');

                    $mail->SetUseSmtp(true);
                    $mail->SetSmtpHost($preferences->smtp_host, $preferences->smtp_port);
                    $mail->SetSmtpUser($preferences->smtp_user, $preferences->smtp_pass);

                    $mail->send();
                }

            }

            TTransaction::close();
            new TMessage('info', 'E-Mail enviado com sucesso!!!!');
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }

    }

}
