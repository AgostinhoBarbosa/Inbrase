<?php
    class emailJuridico
    {
        private $processoa;
        private $status;

        public function __construct( Processo $processo, $status)
        {
            $this->processoa = $processo;
            $this->status    = status::find($status);
            $this->gerarEmail();
        }

        public function gerarEmail()
        {
            try
            {
                $dados_email = NULL;

                TTransaction::open('permission');

                $preferences = SystemPreference::getAllPreferences();
                if ($preferences)
                {
                    $dados_email = ((object) $preferences);
                }else{
                    new TMessage('error', 'Erro ao ler os dados para envio de email');
                    exit;
                }

                TTransaction::close();

                $empresa = new Empresa( 1);

                $modelo = 'app/resources/email_juridico.html';

                $fp = fopen($modelo, 'r');
                $texto = fread($fp, filesize($modelo));
                fclose($fp);

                $texto = str_replace("\n","", $texto);

                $texto   = str_replace('[LIBERADOR]',           $this->processoa->liberadores->nome, $texto);
                $texto   = str_replace('[STATUS]',              $this->status->statu, $texto);
                $texto   = str_replace('[SEGURADORA]',          $this->processoa->seguradoras->nome, $texto);
                $texto   = str_replace('[MARCA_MODELO]',        $this->processoa->marca_modelo, $texto);
                $texto   = str_replace('[ANO_FAB]',             $this->processoa->ano, $texto);
                $texto   = str_replace('[PLACA]',               $this->processoa->placa, $texto);
                $texto   = str_replace('[CHASSI]',              $this->processoa->chassi, $texto);
                $texto   = str_replace('[CONDCHASSI]',          Utilidades::condicao_chassi_motor()[$this->processoa->condChassi], $texto);
                $texto   = str_replace('[CONDMOTOR]',           Utilidades::condicao_chassi_motor()[$this->processoa->condMotor], $texto);
                if (!empty($this->processoa->restricao02)) {
                    $texto = str_replace('[RESTRICAO02]', Utilidades::estado_bem()[$this->processoa->restricao02], $texto);
                }else{
                    $texto = str_replace('[RESTRICAO02]',  " ", $texto);
                }
                $texto   = str_replace('[MOTOR]',               $this->processoa->motor, $texto);
                $texto   = str_replace('[COR]',                 $this->processoa->cor, $texto);
                $texto   = str_replace('[SINISTRO]',            $this->processoa->sinistro, $texto);
                $texto   = str_replace('[AUTOS]',               $this->processoa->processo_origem, $texto);
                $texto   = str_replace('[USUARIO]',             EMPRESA, $texto);

                $assunto  = "SOLICITAÇÃO DE AUTORIZAÇÃO PARA ATUAÇÃO JUDICIAL - ".$this->processoa->marca_modelo;
                $assunto .= " PLACA ".$this->processoa->placa;
                $assunto .= " CHASSI ".$this->processoa->chassi;
                $assunto .= " SEGURADORA ".$this->processoa->nome;
                $assunto .= " SINISTRO ".$this->processoa->sinistro;

                $mail           = new TMail;
                $mail->setDebug( FALSE );
                $mail->setFrom($dados_email->mail_from, $dados_email->mail_from);
                $mail->setSubject( $assunto  );
                $mail->setHtmlBody($texto);

                $mail->addAddress("juridico@afincco.com.br");
                $mail->addAddress("recuperados@afincco.com.br");
                $mail->addAddress("identificacao@afincco.com.br");
                $mail->addAddress("indenizados@afincco.com.br");
                $mail->addAddress($this->processoa->get_seguradoras()->email);

                $mail->SetUseSmtp(TRUE);
                $mail->SetSmtpHost( $dados_email->smtp_host , $dados_email->smtp_port);
                $mail->SetSmtpUser( $dados_email->smtp_user, $dados_email->smtp_pass);

                $mail->send();

                return true;
            }
            catch (Exception $e)
            {
                new TMessage('error', '<b>Erro</b> ' . $e->getMessage());
                $this->arquivo = false;
                return false;
            }
        }

    }

