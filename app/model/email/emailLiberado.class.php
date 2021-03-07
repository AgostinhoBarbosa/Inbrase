<?php
    class emailLiberado
    {
        private $processoa;


        public function __construct( Processo $processo)
        {
            $this->processoa = $processo;
            $this->email_seguradora = $this->processoa->get_seguradoras()->email;
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

                $modelo = 'app/resources/email_liberacao.html';

                $fp = fopen($modelo, 'r');
                $texto = fread($fp, filesize($modelo));
                fclose($fp);

                $data_processo = date('d/m/Y');

                $imagem  = "app/images/".$empresa->logo;

                $texto   = str_replace('[LOGO]',                $imagem, $texto);
                $texto   = str_replace('[DATA_EMISSAO]',        $data_processo, $texto);

                $texto   = str_replace('[SEGURADORA]',          $this->processoa->seguradoras->nome, $texto);
                $texto   = str_replace('[SEGURADO]',            $this->processoa->nome_segurado, $texto);
                $texto   = str_replace('[MARCA_MODELO]',        $this->processoa->marca_modelo, $texto);
                $texto   = str_replace('[ANO_FAB]',             $this->processoa->ano, $texto);
                $texto   = str_replace('[PLACA]',               $this->processoa->placa, $texto);
                $texto   = str_replace('[UF]',                  $this->processoa->uf, $texto);
                $texto   = str_replace('[TIPO]',                $this->processoa->tipo, $texto);

                $texto   = str_replace('[CHASSI]',              $this->processoa->chassi, $texto);
                $texto   = str_replace('[CONDCHASSI]',          Utilidades::condicao_chassi_motor()[$this->processoa->condChassi], $texto);
                $texto   = str_replace('[CONDMOTOR]',           Utilidades::condicao_chassi_motor()[$this->processoa->condMotor], $texto);
                $texto   = str_replace('[RESTRICAO]',           $this->processoa->restricao, $texto);
                if (!empty($this->processoa->restricao02)) {
                    $texto = str_replace('[RESTRICAO02]', Utilidades::estado_bem()[$this->processoa->restricao02], $texto);
                }else{
                    $texto = str_replace('[RESTRICAO02]',  " ", $texto);
                }
                $texto   = str_replace('[RESTRICAO03]',         $this->processoa->restricao03, $texto);
                $texto   = str_replace('[RESTRICAO04]',         $this->processoa->restricao04, $texto);
                $texto   = str_replace('[DP_REC]',              $this->processoa->dp_rec, $texto);
                $texto   = str_replace('[CIDADE_REC]',          $this->processoa->cidade_rec."(".$this->processoa->uf_rec.")", $texto);
                $texto   = str_replace('[BO_REC]',              $this->processoa->bo_rec, $texto);
                $texto   = str_replace('[UF_REC]',              $this->processoa->uf_rec, $texto);
                $texto   = str_replace('[OBS_REC]',             $this->processoa->obs_rec, $texto);
                $texto   = str_replace('[DDD_INFORMANTE_REC]',  $this->processoa->ddd_informante_rec, $texto);
                $texto   = str_replace('[FONE_INFORMANTE_REC]', $this->processoa->fone_informante_rec, $texto);
                $texto   = str_replace('[RENAVAM]',             $this->processoa->renavam, $texto);
                $texto   = str_replace('[MOTOR]',               $this->processoa->motor, $texto);
                $texto   = str_replace('[COR]',                 $this->processoa->cor, $texto);
                $texto   = str_replace('[COMBUSTIVEL]',         $this->processoa->combustivel, $texto);
                $texto   = str_replace('[SINISTRO]',            $this->processoa->sinistro, $texto);
                $texto   = str_replace('[CIDADE_DEC]',          $this->processoa->cidade_dec."(".$this->processoa->uf_dec.")", $texto);
                $texto   = str_replace('[UF_DEC]',              $this->processoa->uf_dec, $texto);
                $texto   = str_replace('[INFORMANTE_DEC]',      $this->processoa->informante_dec, $texto);
                $texto   = str_replace('[DDD_INFORMANTE_DEC]',  $this->processoa->ddd_informante_dec, $texto);
                $texto   = str_replace('[FONE_INFORMANTE_DEC]', $this->processoa->fone_informante_dec, $texto);
                $texto   = str_replace('[DP_DEC]',              $this->processoa->dp_dec, $texto);
                $texto   = str_replace('[BO_DEC]',              $this->processoa->bo_dec, $texto);
                $texto   = str_replace('[DATA_DEC]',            TDate::date2br($this->processoa->data_dec), $texto);
                $texto   = str_replace('[CIDADE_REC]',          $this->processoa->cidade_rec."(".$this->processoa->uf_rec.")", $texto);
                $texto   = str_replace('[DP_REC]',              $this->processoa->dp_rec, $texto);
                $texto   = str_replace('[BO_REC]',              $this->processoa->bo_rec, $texto);
                $texto   = str_replace('[DATA_REC]',            TDate::date2br($this->processoa->data_rec), $texto);
                $texto   = str_replace('[CIDADE_DEV]',          $this->processoa->cidade_dev."(".$this->processoa->uf_dev.")", $texto);
                $texto   = str_replace('[UF_DEV]',              $this->processoa->uf_dev, $texto);
                $texto   = str_replace('[DP_DEV]',              $this->processoa->dp_dev, $texto);
                $texto   = str_replace('[BO_DEV]',              $this->processoa->bo_dev, $texto);
                $texto   = str_replace('[DATA_ENTREGA_DEV]',    TDate::date2br($this->processoa->data_entrega_dev), $texto);
                $texto   = str_replace('[RESPONSAVEL_DEV]',     $this->processoa->responsavel_dev, $texto);
                $texto   = str_replace('[OBS_DEV]',             $this->processoa->obs_dev, $texto);
                $texto   = str_replace('[LOCAL_ENTREGA_DEV]',   $this->processoa->local_entrega_dev, $texto);
                $texto   = str_replace('[TELEFONE_DEV]',        $this->processoa->telefone_dev, $texto);

                $texto   = str_replace('[USUARIO]',             EMPRESA, $texto);

                $assunto  = "LIBERAÇÃO DE VEÍCULO - ".$this->processoa->marca_modelo;
                $assunto .= " PLACA ".$this->processoa->placa;
                $assunto .= " CHASSI ".$this->processoa->chassi;
                $assunto .= " SEGURADORA ".$this->processoa->nome;
                $assunto .= " SINISTRO ".$this->processoa->sinistro;

                $mail           = new TMail;
                $mail->setDebug( FALSE );
                $mail->setFrom($dados_email->mail_from, $dados_email->mail_from);
                $mail->setSubject( $assunto  );
                $mail->setHtmlBody($texto);

                if ( !empty($this->email_seguradora) ) {
                    $end = explode(';', $this->email_seguradora);
                    if ( $end ) {
                        for ( $i = 0; $i < count($end); $i++ ) {
                            $mail->addAddress($end[ $i ]);
                        }
                    } else {
                        $mail->addAddress($this->email_seguradora);
                    }
                }

                $mail->addAddress("transportes@afincco.com.br");
                $mail->addAddress("ocorrencias@afincco.com.br");
                $mail->addAddress( 'indenizados@afincco.com.br' );


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

