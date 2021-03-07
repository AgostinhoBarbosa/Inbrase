<?php

    use Adianti\Database\TTransaction;

    class emailPendencia
    {

        public static function onEnviar050615() {
            try {
                TTransaction::open( 'permission' );
                $preferences = (object)SystemPreference::getAllPreferences();
                TTransaction::close();

                TTransaction::open( 'afincco' );

                if ( $preferences ) {
                    $conn          = TTransaction::get();
                    $sql           = "select h0.id_processo, h0.id_status, data_cadastro,  datediff(curdate(), h0.data_cadastro) as dias from hstatus h0 WHERE ";
                    $sql           .= "h0.id in (select max(h1.id) from hstatus h1 where h1.id_processo = h0.id_processo)";
                    $result        = $conn->query( $sql );
                    $lista_objetos = [];
                    foreach ( $result as $row ) {
                        $verifica = FALSE;

                        switch ( $row[ 'id_status' ] ) {
                            case "30" :
                            case "31" :
                            case "40" :
                                $verifica = TRUE;
                                break;
                        }

                        if ( $verifica ) {
                            $dias = $row[ 'dias' ];
                            if ( $dias > 6 ) {
                                $processo             = Processo::find( $row[ 'id_processo' ] );
                                $objeto               = new stdClass();
                                $objeto->processo     = $row[ 'id_processo' ];
                                $objeto->status       = $row[ 'id_status' ];
                                $objeto->dias         = $dias;
                                $objeto->placa        = $processo->placa;
                                $objeto->nome_usu     = empty( $processo->usuario ) ? "" : $processo->usuarios->name;
                                $objeto->nome_ges     = empty( $processo->gestor ) ? "" : $processo->gestores->name;
                                $objeto->nome_lib     = empty( $processo->liberador ) ? "" : $processo->liberadores->nome;
                                $objeto->gestor       = empty( $processo->gestor ) ? $processo->usuario : $processo->gestor;
                                $objeto->liberador    = empty( $processo->liberador ) ? $objeto->gestor : $processo->liberador;
                                $objeto->email_lib    = !empty( $processo->liberadores->email ) ? $processo->liberadores->email : $processo->usuarios->email;
                                $objeto->email_gestor = !empty( $processo->gestores->email ) ? $processo->gestores->email : $processo->usuarios->email;

                                if ( $objeto->email_lib === 'teste@softgt.com.br' ) {
                                    $objeto->email_lib = "";
                                }
                                if ( $objeto->email_gestor === 'teste@softgt.com.br' ) {
                                    $objeto->email_gestor = "";
                                }
                                $objeto->datahora = $row[ 'data_cadastro' ];
                                if ( $objeto->email_lib !== "" && $objeto->email_lib !== NULL ) {
                                    $lista_objetos[ $objeto->email_lib ][] = $objeto;
                                }
                                if ( $objeto->email_gestor !== "" && $objeto->email_gestor !== NULL ) {
                                    $lista_objetos[ $objeto->email_gestor ][] = $objeto;
                                }
                            }
                        }
                    }
                    if ( count( $lista_objetos ) > 0 ) {
                        $liberador       = "";
                        $email_gestor    = "";
                        $email_liberador = "";
                        foreach ( $lista_objetos as $key => $objeto ) {
                            if ( $liberador === "" ) {
                                $corpo     = "<table class='table table-bordered' style='width:100%;border-collapse:collapse;'>";
                                $corpo     .= "<tbody>";
                                $corpo     .= "    <tr>";
                                $corpo     .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:200px !important;'>PROCESSO</th>";
                                $corpo     .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:200px !important;'>PLACA</th>";
                                $corpo     .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:200px !important;'>DATA/HORA</th>";
                                $corpo     .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:100px !important;'>DIAS ATRASO</th>";
                                $corpo     .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:100px !important;'>USUARIO</th>";
                                $corpo     .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:100px !important;'>GESTOR</th>";
                                $corpo     .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:100px !important;'>LIBERADOR</th>";
                                $corpo     .= "    </tr>";
                                $liberador = $key;
                            }
                            if ( $liberador !== $key ) {
                                $corpo .= "</tbody>";
                                $corpo .= "</table>";

                                $assunto = 'Listagem de Processos com Status (05,06, 15) a mais de 7 dias';

                                $mail = new TMail();
                                $mail->setDebug( FALSE );
                                $mail->setFrom( $preferences->mail_from );
                                $mail->setSubject( $assunto );
                                $mail->setHtmlBody( $corpo );

                                if ( $email_gestor !== "" ) {
                                    $mail->addAddress( $email_gestor );
                                }
                                if ( $email_liberador !== "" ) {
                                    $mail->addAddress( $email_liberador );
                                }

                                $mail->addAddress( 'afincco@afincco.com.br' );

                                $mail->SetUseSmtp( TRUE );
                                $mail->SetSmtpHost( $preferences->smtp_host, $preferences->smtp_port );
                                $mail->SetSmtpUser( $preferences->smtp_user, $preferences->smtp_pass );

                                $mail->send();
                                $corpo           = "<table class='table table-bordered' style='width:100%;border-collapse:collapse;'>";
                                $corpo           .= "<tbody>";
                                $corpo           .= "    <tr>";
                                $corpo           .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:200px !important;'>PROCESSO</th>";
                                $corpo           .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:200px !important;'>PLACA</th>";
                                $corpo           .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:200px !important;'>DATA/HORA</th>";
                                $corpo           .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:100px !important;'>DIAS ATRASO</th>";
                                $corpo           .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:100px !important;'>USUARIO</th>";
                                $corpo           .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:100px !important;'>GESTOR</th>";
                                $corpo           .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:100px !important;'>LIBERADOR</th>";
                                $corpo           .= "    </tr>";
                                $liberador       = $key;
                                $email_gestor    = "";
                                $email_liberador = "";

                            }
                            foreach ( $objeto as $obj ) {
                                $corpo .= "    <tr>";
                                $corpo .= "        <td style='text-align: center; background-color: rgb(255, 231, 206);border:1px solid;width:200px !important;'>$obj->processo</td>";
                                $corpo .= "        <td style='text-align: center; background-color: rgb(255, 231, 206);border:1px solid;width:200px !important;'>".$obj->placa."</td>";
                                $corpo .= "        <td style='text-align: center; background-color: rgb(255, 231, 206);border:1px solid;width:200px !important;'>".$obj->datahora."</td>";
                                $corpo .= "        <td style='text-align: center; background-color: rgb(255, 231, 206);border:1px solid;width:100px !important;'>".$obj->dias."</td>";
                                $corpo .= "        <td style='text-align: center; background-color: rgb(255, 231, 206);border:1px solid;width:100px !important;'>".$obj->nome_usu."</td>";
                                $corpo .= "        <td style='text-align: center; background-color: rgb(255, 231, 206);border:1px solid;width:100px !important;'>".$obj->nome_ges."</td>";
                                $corpo .= "        <td style='text-align: center; background-color: rgb(255, 231, 206);border:1px solid;width:100px !important;'>".$obj->nome_lib."</td>";
                                $corpo .= "    </tr>";
                                if ( $email_gestor === "" && $obj->email_gestor !== NULL ) {
                                    $email_gestor = $obj->email_gestor;
                                }
                                if ( $email_liberador === "" && $obj->email_lib !== NULL ) {
                                    $email_liberador = $obj->email_lib;
                                }
                            }
                        }
                        $corpo .= "</tbody>";
                        $corpo .= "</table>";

                        $assunto = 'Listagem de Processos com Status (05,06, 15) a mais de 7 dias';

                        $mail = new TMail();
                        $mail->setDebug( FALSE );
                        $mail->setFrom( $preferences->mail_from );
                        $mail->setSubject( $assunto );
                        $mail->setHtmlBody( $corpo );

                        if ( $email_gestor !== "" ) {
                            $mail->addAddress( $email_gestor );
                        }
                        if ( $email_liberador !== "" ) {
                            $mail->addAddress( $email_liberador );
                        }

                        $mail->addAddress( 'afincco@afincco.com.br' );

                        $mail->SetUseSmtp( TRUE );
                        $mail->SetSmtpHost( $preferences->smtp_host, $preferences->smtp_port );
                        $mail->SetSmtpUser( $preferences->smtp_user, $preferences->smtp_pass );

                        $mail->send();
                    }

                }

                TTransaction::close();
            } catch ( Exception $e ) {
                TTransaction::rollback();
            }
        }

        public static function onEnviar04091011() {
            try {
                TTransaction::open( 'permission' );
                $preferences = (object)SystemPreference::getAllPreferences();
                TTransaction::close();

                TTransaction::open( 'afincco' );
                if ( $preferences ) {
                    $conn          = TTransaction::get();
                    $sql           = "select h0.id_processo, h0.id_status, data_cadastro,  datediff(curdate(), h0.data_cadastro) as dias from hstatus h0 WHERE ";
                    $sql           .= "h0.id in (select max(h1.id) from hstatus h1 where h1.id_processo = h0.id_processo)";
                    $result        = $conn->query( $sql );
                    $lista_objetos = [];
                    foreach ( $result as $row ) {
                        $verifica = FALSE;

                        switch ( $row[ 'id_status' ] ) {
                            case "29" :
                            case "34" :
                            case "35" :
                            case "36" :
                                $verifica = TRUE;
                                break;
                        }

                        if ( $verifica ) {
                            $dias = $row[ 'dias' ];
                            if ( $dias > 6 ) {
                                $processo             = Processo::find( $row[ 'id_processo' ] );
                                $objeto               = new stdClass();
                                $objeto->processo     = $row[ 'id_processo' ];
                                $objeto->status       = $row[ 'id_status' ];
                                $objeto->dias         = $dias;
                                $objeto->placa        = $processo->placa;
                                $objeto->gestor       = empty( $processo->gestor ) ? $processo->usuario : $processo->gestor;
                                $objeto->liberador    = empty( $processo->liberador ) ? $objeto->gestor : $processo->liberador;
                                $objeto->nome_usu     = empty( $processo->usuario ) ? "" : $processo->usuarios->name;
                                $objeto->nome_ges     = empty( $processo->gestor ) ? "" : $processo->gestores->nome;
                                $objeto->nome_lib     = empty( $processo->liberador ) ? "" : $processo->liberadores->nome;
                                $objeto->email_lib    = !empty( $processo->liberadores->email ) ? $processo->liberadores->email : $processo->usuarios->email;
                                $objeto->email_gestor = !empty( $processo->gestores->email ) ? $processo->gestores->email : $processo->usuarios->email;

                                $objeto->datahora = $row[ 'data_cadastro' ];
                                if ( $objeto->email_lib === 'teste@softgt.com.br' ) {
                                    $objeto->email_lib = "";
                                }
                                if ( $objeto->email_gestor === 'teste@softgt.com.br' ) {
                                    $objeto->email_gestor = "";
                                }
                                $objeto->datahora = $row[ 'data_cadastro' ];
                                if ( $objeto->email_lib !== "" && $objeto->email_lib !== NULL ) {
                                    $lista_objetos[ $objeto->email_lib ][] = $objeto;
                                }
                                if ( $objeto->email_gestor !== "" && $objeto->email_gestor !== NULL ) {
                                    $lista_objetos[ $objeto->email_gestor ][] = $objeto;
                                }
                            }
                        }
                    }
                    if ( count( $lista_objetos ) > 0 ) {
                        $liberador       = "";
                        $email_gestor    = "";
                        $email_liberador = "";
                        foreach ( $lista_objetos as $key => $objeto ) {
                            if ( $liberador === "" ) {
                                $corpo = "<table class='table table-bordered' style='width:100%;border-collapse:collapse;'>";
                                $corpo .= "<tbody>";
                                $corpo .= "    <tr>";
                                $corpo .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:200px !important;'>PROCESSO</th>";
                                $corpo .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:200px !important;'>PLACA</th>";
                                $corpo .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:200px !important;'>DATA/HORA</th>";
                                $corpo .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:100px !important;'>DIAS ATRASO</th>";
                                $corpo .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:100px !important;'>USUARIO</th>";
                                $corpo .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:100px !important;'>GESTOR</th>";
                                $corpo .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:100px !important;'>LIBERADOR</th>";

                                $corpo     .= "    </tr>";
                                $liberador = $key;
                            }
                            if ( $liberador !== $key ) {
                                $corpo .= "</tbody>";
                                $corpo .= "</table>";

                                $assunto = 'Listagem de Processos com Status (04,09,10, 11) a mais de 7 dias';

                                $mail = new TMail();
                                $mail->setDebug( FALSE );
                                $mail->setFrom( $preferences->mail_from );
                                $mail->setSubject( $assunto );
                                $mail->setHtmlBody( $corpo );

                                if ( $email_gestor !== "" ) {
                                    $mail->addAddress( $email_gestor );
                                }
                                if ( $email_liberador !== "" ) {
                                    $mail->addAddress( $email_liberador );
                                }

                                $mail->addAddress( 'afincco@afincco.com.br' );

                                $mail->SetUseSmtp( TRUE );
                                $mail->SetSmtpHost( $preferences->smtp_host, $preferences->smtp_port );
                                $mail->SetSmtpUser( $preferences->smtp_user, $preferences->smtp_pass );

                                $mail->send();
                                $corpo           = "<table class='table table-bordered' style='width:100%;border-collapse:collapse;'>";
                                $corpo           .= "<tbody>";
                                $corpo           .= "    <tr>";
                                $corpo           .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:200px !important;'>PROCESSO</th>";
                                $corpo           .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:200px !important;'>PLACA</th>";
                                $corpo           .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:200px !important;'>DATA/HORA</th>";
                                $corpo           .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:100px !important;'>DIAS ATRASO</th>";
                                $corpo           .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:100px !important;'>USUARIO</th>";
                                $corpo           .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:100px !important;'>GESTOR</th>";
                                $corpo           .= "        <th style='text-align: center;background-color: rgb(255, 231, 206);border:1px solid;width:100px !important;'>LIBERADOR</th>";
                                $corpo           .= "    </tr>";
                                $liberador       = $key;
                                $email_gestor    = "";
                                $email_liberador = "";

                            }
                            foreach ( $objeto as $obj ) {
                                $corpo .= "    <tr>";
                                $corpo .= "        <td style='text-align: center; background-color: rgb(255, 231, 206);border:1px solid;width:200px !important;'>$obj->processo</td>";
                                $corpo .= "        <td style='text-align: center; background-color: rgb(255, 231, 206);border:1px solid;width:200px !important;'>".$obj->placa."</td>";
                                $corpo .= "        <td style='text-align: center; background-color: rgb(255, 231, 206);border:1px solid;width:200px !important;'>".$obj->datahora."</td>";
                                $corpo .= "        <td style='text-align: center; background-color: rgb(255, 231, 206);border:1px solid;width:100px !important;'>".$obj->dias."</td>";
                                $corpo .= "        <td style='text-align: center; background-color: rgb(255, 231, 206);border:1px solid;width:100px !important;'>".$obj->nome_usu."</td>";
                                $corpo .= "        <td style='text-align: center; background-color: rgb(255, 231, 206);border:1px solid;width:100px !important;'>".$obj->nome_ges."</td>";
                                $corpo .= "        <td style='text-align: center; background-color: rgb(255, 231, 206);border:1px solid;width:100px !important;'>".$obj->nome_lib."</td>";
                                $corpo .= "    </tr>";
                                if ( $email_gestor === "" && $obj->email_gestor !== NULL ) {
                                    $email_gestor = $obj->email_gestor;
                                }
                                if ( $email_liberador === "" && $obj->email_liberador !== NULL ) {
                                    $email_liberador = $obj->email_liberador;
                                }
                            }
                        }
                        $corpo .= "</tbody>";
                        $corpo .= "</table>";

                        $assunto = 'Listagem de Processos com Status (04,09,10,11) a mais de 7 dias';

                        $mail = new TMail();
                        $mail->$mail->setDebug( FALSE );
                        $mail->setFrom( $preferences->mail_from );
                        $mail->setSubject( $assunto );
                        $mail->setHtmlBody( $corpo );

                        if ( $email_gestor !== "" ) {
                            $mail->addAddress( $email_gestor );
                        }
                        if ( $email_liberador !== "" ) {
                            $mail->addAddress( $email_liberador );
                        }

                        $mail->addAddress( 'afincco@afincco.com.br' );

                        $mail->SetUseSmtp( TRUE );
                        $mail->SetSmtpHost( $preferences->smtp_host, $preferences->smtp_port );
                        $mail->SetSmtpUser( $preferences->smtp_user, $preferences->smtp_pass );

                        $mail->send();
                    }

                }

                TTransaction::close();
            } catch ( Exception $e ) {
                TTransaction::rollback();
            }
        }
    }
