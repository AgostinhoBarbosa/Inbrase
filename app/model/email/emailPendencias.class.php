<?php
    
    class emailPendencias
    {
        public static function enviar( viewPendencias $viewPendencia )
        {
            
            
            $dados_email = NULL;
            
            TTransaction::open( 'permission' );
            
            $preferences = SystemPreference::getAllPreferences();
            if ( $preferences ) {
                $dados_email = ( (object)$preferences );
            } else {
                exit;
            }
            
            TTransaction::close();
            
            $textos = Textos::find( 10 );
            $texto  = $textos->texto;
            
            $texto = str_replace( '[NOME]', $viewPendencia->get_nome_cobranca(), $texto );
            $texto = str_replace( '[PROCESSO]', $viewPendencia->id, $texto );
            $texto = str_replace( '[SEGURADORA]', $viewPendencia->nome, $texto );
            $texto = str_replace( '[SINISTRO]', $viewPendencia->sinistro, $texto );
            $texto = str_replace( '[VEICULO]', $viewPendencia->marca_modelo, $texto );
            $texto = str_replace( '[MARCA_MODELO]', $viewPendencia->marca_modelo, $texto );
            $texto = str_replace( '[PLACA]', $viewPendencia->placa, $texto );
            $texto = str_replace( '[CHASSI]', $viewPendencia->chassi, $texto );
            $texto = str_replace( '[MOTOR]', $viewPendencia->motor, $texto );
            $texto = str_replace( '[ANO_FAB]', $viewPendencia->ano, $texto );
            $texto = str_replace( '[STATUS]', $viewPendencia->statu, $texto );
            $texto = str_replace( '[TIPO_SERVICO_DEC]', $viewPendencia->get_processo()->tipo_servico->nome, $texto );
;
            $texto = str_replace('[CONDCHASSI]', Utilidades::condicao_chassi_motor()[ $viewPendencia->get_processo()->condChassi ], $texto);
            $texto = str_replace('[CONDMOTOR]', Utilidades::condicao_chassi_motor()[ $viewPendencia->get_processo()->condMotor ], $texto);
            if (!empty($viewPendencia->get_processo()->restricao02)) {
                $texto = str_replace('[ESTADO_VEICULO]', Utilidades::estado_bem()[$viewPendencia->get_processo()->restricao02], $texto);
            }else{
                $texto = str_replace('[ESTADO_VEICULO]',  " ", $texto);
            }
            $texto   = str_replace('[DATA_REC]',            TDate::date2br($viewPendencia->get_processo()->data_rec), $texto);
            $texto   = str_replace('[DATA_ENTREGA_DEV]',    TDate::date2br($viewPendencia->get_processo()->data_entrada_dev), $texto);
            $cidade_dev    = $viewPendencia->get_processo()->cidade_dev;
            if (strlen($viewPendencia->get_processo()->uf_dev) > 0) {
                $cidade_dev .= '('.$viewPendencia->get_processo()->uf_dev.')';
            }
            $texto = str_replace( '[CIDADE_DEV]', $cidade_dev, $texto );
    
            require_once( 'vendor/autoload.php' );
            
            $assunto = "PROCESSO COM PENDÊNCIA - URGENTE";
            
            $mail = new TMail();
            $mail->setDebug( 0 );
            $mail->setFrom( $dados_email->mail_from, $dados_email->mail_from );
            $mail->addBCC( $dados_email->mail_from, $dados_email->mail_from );
            $mail->setSubject( $assunto );
            $mail->setHtmlBody( $texto );
            $enviar = false;
            if ( $viewPendencia->get_status() ) {
                $destino = $viewPendencia->get_status()->email_cobranca;
                if ( !empty( $destino ) ) {
                    if (strpos($destino, ';')) {
                        $destino = explode( ';', $destino );
                        if ( $destino ) {
                            foreach ( $destino as $dest ) {
                                $mail->addAddress( $dest );
                                $enviar = TRUE;
                            }
                        }
                    }else{
                        $mail->addAddress( $destino );
                        $enviar = TRUE;
                    }
                }
                $destino = $viewPendencia->get_status()->email_liberador;
                if ( $destino == 1 ) {
                    $mail->addAddress($viewPendencia->get_email_cobranca());
                    $enviar = true;
                }
                $destino = $viewPendencia->get_status()->email_seguradora;
                if ( $destino == 1 ) {
                    $mail->addAddress($viewPendencia->get_seguradora()->email);
                    $enviar = true;
                }
                
                if ($enviar) {
                    $mail->SetUseSmtp( TRUE );
                    $mail->SetSmtpHost( $dados_email->smtp_host, $dados_email->smtp_port );
                    $mail->SetSmtpUser( $dados_email->smtp_user, $dados_email->smtp_pass );
    
                    $mail->send();
                    sleep( 2 );
                }
            }
        }
    }

