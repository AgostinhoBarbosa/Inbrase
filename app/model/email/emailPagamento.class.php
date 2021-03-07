<?php
    class emailPagamento
    {
        private $processoa;
        private $status;
        private $recibo;
        private $nota;


        public function __construct($processo, $status)
        {
            $this->processoa = $processo;
            $this->status    = status::find($status);
            if ($status == 16 || $status == 18){
                $titulos = Titulo::where('processo_id', '=', $this->processoa->id)
                                 ->where('tipolancamento_id', '=', 36)
                                 ->where('pagar_receber', '=', 'R')
                                 ->where('dc', '=', 'C')->load();
                if ($titulos){
                    $this->recibo = $titulos[0];
                }
            }
            if ($status == 17 || $status == 18){
                $titulos = Titulo::where('processo_id', '=', $this->processoa->id)
                                 ->where('tipolancamento_id', '=', 79)
                                 ->where('pagar_receber', '=', 'R')
                                 ->where('dc', '=', 'C')->load();
                if ($titulos){
                    $this->nota = $titulos[0];
                }
            }
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


                // close the transaction
                TTransaction::close();

                $empresa = new Empresa( 1);

                $modelo = 'app/resources/email_pagamento.html';

                $fp = fopen($modelo, 'r');
                $texto = fread($fp, filesize($modelo));
                fclose($fp);

                $texto = str_replace("\n","", $texto);

                $tipo_pagamento = 'PROVIDENCIAR PAGAMENTO REFERENTE ';

                if ($this->status->id == 16){
                    $tipo_pagamento .= 'DESPESAS RECEBIDAS';
                }
                if ($this->status->id == 17){
                    $tipo_pagamento .= 'HONORÁRIOS RECEBIDOS';
                }
                if ($this->status->id == 18){
                    $tipo_pagamento .= 'HONORÁRIOS E DESPESAS RECEBIDAS';
                }
                $texto   = str_replace('[NR_NOTA]',             $this->nota->numero, $texto);
                $texto   = str_replace('[NR_RECIBO]',           $this->recibo->numero, $texto);

                $texto   = str_replace('[LIBERADOR]',           $this->processoa->liberadores->nome, $texto);
                $texto   = str_replace('[STATUS]',              $this->status->statu, $texto);
                $texto   = str_replace('[TIPO_PAGAMENTO]',       $tipo_pagamento, $texto);
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
                $texto   = str_replace('[USUARIO]',             EMPRESA, $texto);

                $assunto  = "PROVIDENCIAR PAGAMENTO - ".$this->processoa->marca_modelo;
                $assunto .= " PLACA ".$this->processoa->placa;
                $assunto .= " CHASSI ".$this->processoa->chassi;
                $assunto .= " SEGURADORA ".$this->processoa->nome;
                $assunto .= " SINISTRO ".$this->processoa->sinistro;

                $mail           = new TMail;
                $mail->setDebug( FALSE );
                $mail->setFrom($dados_email->mail_from, $dados_email->mail_from);
                $mail->setSubject( $assunto  );
                $mail->setHtmlBody($texto);

                $mail->addAddress("indenizados@afincco.com.br");
                $mail->addAddress("financeiro@afincco.com.br");
                $mail->addAddress("administrativo@afincco.com.br");

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

