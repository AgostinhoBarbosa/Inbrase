<?php

    `echo "pegando email" >> /var/log/syslog`;
    $user    = 'importveiculosseg@gmail.com';
    $senha   = 'amandha16';
    $caminho = '/home/sistema/email/downloads/';

    $mbox = imap_open( "{imap.gmail.com:993/imap/ssl}INBOX", $user, $senha ) or die( 'Cannot connect to Gmail: ' . imap_last_error() );
    #$emails = imap_search($mbox,'ALL');
    #print_r($emails);

    #print_r($mbox);

    #$id=2;
    #$overview = imap_fetch_overview($mbox,$id,0);
    #$message = imap_fetchbody($mbox,$id,0);


    //Lê todas as mensagens
    for ( $m = 1; $m <= imap_num_msg( $mbox ); $m++ ) {

        $overview = imap_fetch_overview( $mbox, $m, 0 );
        #$teste=imap_fetchbody ($mbox, $m,);

        #print_r($overview);
        if ( ( $overview[ 0 ]->seen ) != 1 ) {


            $estrutura = imap_fetchstructure( $mbox, $m );

            $cont_anx = count( $estrutura->parts );
            $time_mensagem = $overview[ 0 ]->udate;
            $time_sys      = time();
            $visto         = $overview[ 0 ]->seen;

            for ( $C = 1; ( ( $C < $cont_anx ) ); $C++ ) {
                $dadostotal = imap_fetchbody( $mbox, $m, '' );
                $arq        = fopen( $caminho . $C . '__' . $time_sys, "w" );
                fwrite( $arq, $dadostotal );
                fclose( $arq );

                $teste = imap_fetchbody( $mbox, $m, $C );
                $arquivo = $estrutura->parts[ $C ]->dparameters[ 0 ]->value;

                if ( stripos( $arquivo, '.eml' ) > 0 ) {
                    $dados = imap_base64( imap_fetchbody( $mbox, $m, $C ) );

                    $nomeAnexo = $time_mensagem . $time_sys . 'arquivo' . $C . '.eml';
                    $caminho = '/home/sistema/email/downloads/';
                    $arq = fopen( $caminho . $nomeAnexo, "w" );
                    fwrite( $arq, $dados );
                    fclose( $arq );
                }


                ######## pegar XLSX


                #	if(stripos($arquivo,'.xlsx')>0){
                #print_r($estrutura);
                #	$dados1 = imap_fetchbody($mbox, $m,2);
                #$dados1 = imap_fetchbody($mbox, $m,$C);
                #	print_r($dados1);
                #	$dados=imap_base64($dados1);
                #print_r($dados);
                #print_r($teste);
                #print(imap_fetchbody($mbox, $m,$C));
                //determine o nome do arquivo
                #$nomeAnexo = $time_mensagem.$time_sys.$arquivo;
                #print($arquivo);
                #print_r($overview);
                #	$nomeAnexo = $arquivo;
                #$nomeAnexo = $time_mensagem.$time_sys.'arquivo'.$C.'.xlsx';
                #print("$nomeAnexo\n");
                //determine o caminho onde será gravado o anexo
                #	$caminho = '/home/afincco/email/arquivos_xls/';
                //Cria um arquivo com o nome do anexo
                #	$arq = fopen($caminho.$nomeAnexo,"w");
                //Grava o conteúdo do anexo no novo arquivo.
                #	fwrite($arq, $dados);
                //Fecha o novo arquivo
                #	fclose($arq);
                #	}


                ####### Fim pegar XLSX
            }
        }### fecha if de verificação visto
        #print_r($arquivo);
        #$m=9;
        # $dados = imap_base64(imap_fetchbody($mbox, $m,2));
        # $dados = imap_base64(imap_fetchbody($mbox, $m,2));

        #}

        #$overview = imap_fetch_overview($mbox,$m,0);
        #$message = imap_fetchbody($mbox,$m,0);
        #$estrutura = imap_fetchstructure($mbox, $m);
        #print_r($estrutura);
        //pego o nome do anexo e se quiser o tipo de arquivo do anexo


        #$contador=count($estrutura->parts);

        #print_r($contador);


        #for($u = 1; $m <= imap_num_msg($mbox); $u++){

        #$nomeAnexo = $estrutura->parts[$m]->dparameters[0]->value;
        #$extensao = strtolower(pathinfo($nomeAnexo, PATHINFO_EXTENSION));

        #$nomeAnexo = '1';
        #if ($nomeAnexo!='')
        #{
        //determine o nome do arquivo
        #$nomeAnexo = $nomeAnexo.$extensao;
        #print("$nomeAnexo\n");
        //determine o caminho onde será gravado o anexo
        #$caminho = 'downloads/';
        //Cria um arquivo com o nome do anexo
        #$arq = fopen($caminho.$nomeAnexo,"w");
        //Grava o conteúdo do anexo no novo arquivo.
        #fwrite($arq, $dados);
        //Fecha o novo arquivo
        #fclose($arq);
        #print_r($overview)	;
        #print_r($message)	;
        #}
        #}

    }


    #print_r($overview)	;
    #print_r($message)	;

    imap_close( $mbox );

    `echo "Fim pegando email" >> /var/log/syslog`;
    include 'importar_arquivos_email.php';
?>
