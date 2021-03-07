<?php

    echo "iniciando importação;";
    `echo "iniciando importação;" >> /var/log/syslog`;
    include( '/home/sistema/db.php' );
    include( '/home/sistema/email/function_tratar_arquivo_mitsui.php' );
    include( '/home/sistema/email/function_tratar_arquivo_hdi.php' );
    include( '/home/sistema/email/function_tratar_arquivo_mapfre.php' );
    include( '/home/sistema/email/function_tratar_arquivo_bbmapfre.php' );
    include( '/home/sistema/email/function_tratar_arquivo_liberty.php' );
    include( '/home/sistema/email/function_tratar_arquivo_azul.php' );
    include( '/home/sistema/email/function_tratar_arquivo_eml.php' );
    include( '/home/sistema/email/function_tratar_arquivo_sulamerica.php' );
    include( '/home/sistema/email/function_tratar_arquivo_allianz.php' );


    $mysql_ID = cria_db();


    $path      = "/home/sistema/email/downloads/";
    $diretorio = dir( $path );

    while ( $arquivo = $diretorio->read() ) {

        $arq   = fopen( $path . $arquivo, 'r' );
        $ident = '';

        while ( !feof( $arq ) ) {
            $linha = fgets( $arq );

            if ( stripos( $linha, '@hdi.com.br' ) > 0 and ( $ident == '' ) ) {
                $ident = "HDI";
                break;
            }
            if ( stripos( $linha, '@mapfre.com.br' ) > 0 and ( $ident == '' ) ) {
                $ident = "MAPFRE";
                break;
            }

            if ( stripos( $linha, '@msig.com.br' ) > 0 and ( $ident == '' ) ) {
                $ident = "MITSUI";
                break;
            }

            if ( stripos( $linha, '@bbmapfre.com.br' ) > 0 and ( $ident == '' ) ) {
                $ident = "BBMAPFRE";
                break;
            }

            if ( stripos( $linha, 'INDICADA PARA REGULACAO' ) > 0 and ( $ident == '' ) ) {
                $ident = "LIBERTY";
                break;
            }

            if ( stripos( $linha, ';TIPO;PLACA;CHASSI;MOD;COR;' ) > 0 and ( $ident == '' ) ) {
                $ident = "AZUL";
                break;
            }

            if ( stripos( $linha, '@suhaiseguros.com.br' ) > 0 and ( $ident == '' ) ) {
                $ident = "SUHAI";
                break;
            }

            if ( stripos( $linha, '@sulamerica.com.br' ) > 0 and ( $ident == '' ) ) {
                $ident = "SULAMERICA";
                break;
            }

            if ( stripos( $linha, '@allianz.com.br' ) > 0 and ( $ident == '' ) ) {
                $ident = "ALLIANZ_";
                break;
            }

            $fp = fopen( "resultsql.txt", "a" );

            ######### tratamento dos dados
        }
        if ( $ident == 'HDI' ) {
            $array_lista_itens = array( array( 'Segurado', '15', '<p>' ), array( 'lice/Endosso/Item', '24', '<p>' ), array( 'culo Sinistrado', '22', '<br>' ), array( 'Ano Fab/Mod', '18', '-' ), array( 'Placa', '12', '-' ), array( 'Chassi', '13', '<p>' ), array( 'Data/Hora Ocorr', '36', '-' ), array( 'Data Aviso', '16', '<br>' ), array( 'Local de Ocorr', '26', '<br>' ), array( 'Condutor', '15', '-' ), array( 'Fone: ', '11', '<br>' ), array( 'Bairro', '13', '-' ), array( 'Cidade-UF', '15', '<br>' ), array( 'CallCenter', '32', '</font>' ), array( 'Boletim de ocorrencia', '27', '<p>' ) );

            $conteudo  = ( file_get_contents( $path . $arquivo ) );
            $resultado = Tratar_arquivo_hdi( $conteudo, $array_lista_itens );

            $seguradora = '';
            $segurado   = '';
            $apolice    = '';
            $veiculo    = '';
            $anoF       = '';
            $anoM       = '';
            $placa      = '';
            $Chassi     = '';
            $dataOcorr  = '';
            $DataAviso  = '';
            $LocalOcorr = '';
            $Condutor   = '';
            $Fone       = '';
            $Bairro     = '';
            $Cidade     = '';
            $UF         = '';
            $CallCenter = '';
            $BO         = '';

            foreach ( $resultado as $value ) {
                switch ( $value[ 0 ] ) {
                    case 'seguradora':
                        $seguradora = $value[ 1 ];
                        break;

                    case 'Segurado':
                        $segurado = $value[ 1 ];
                        break;

                    case 'lice/Endosso/Item':
                        $apolice = $value[ 1 ];
                        break;

                    case 'culo Sinistrado':
                        $veiculo = $value[ 1 ];
                        break;

                    case 'Ano Fab/Mod':
                        $anos = explode( '/', $value[ 1 ] );
                        $anoF = $anos[ 0 ];
                        $anoM = $anos[ 1 ];
                        break;

                    case 'Placa':
                        $placa = $value[ 1 ];
                        $placa = str_replace( '\n', '', $placa );
                        $placa = str_replace( '-', '', $placa );
                        $placa = str_replace( ' ', '', $placa );
                        break;

                    case 'Chassi':
                        $Chassi = $value[ 1 ];
                        break;

                    case 'Data/Hora Ocorr':
                        $temp      = explode( ' / ', $value[ 1 ] );
                        $dataOcorr = $temp[ 0 ];

                        break;

                    case 'Data Aviso':
                        $DataAviso = $value[ 1 ];
                        break;

                    case 'Local de Ocorr':
                        $LocalOcorr = $value[ 1 ];
                        break;

                    case 'Condutor':
                        $Condutor = $value[ 1 ];
                        break;

                    #print $value[0]."\n";
                    case 'Fone: ':
                        $Fone = $value[ 1 ];
                        break;

                    case 'Bairro':
                        $Bairro = $value[ 1 ];
                        break;

                    case 'Cidade-UF':
                        $temp   = explode( '-', $value[ 1 ] );
                        $Cidade = $temp[ 0 ];
                        $UF     = $temp[ 1 ];
                        break;

                    case 'CallCenter':
                        $CallCenter = $value[ 1 ];
                        break;

                    case 'Boletim de ocorrencia':
                        $BO = $value[ 1 ];
                        break;
                }


            }

            $sql     = "call SP_inserir_veiculos_iportacao('$seguradora','$segurado','$apolice','$veiculo','$anoF','$anoM','$placa','$Chassi','$dataOcorr','$DataAviso','$LocalOcorr','$Condutor','$Fone','$Bairro','$Cidade','$UF','$CallCenter','$BO');";
            $escreve = fwrite( $fp, $sql . "\n" );
            $res     = exec_query_ID( $sql, $mysql_ID );

            fclose( $arq );
            unlink( $path . $arquivo );

            #########################

        }

        if ( $ident == 'MITSUI' ) {
            $array_lista_itens = array( array( 'Data da Ocorr', '22', '' ), array( 'Local', '7', '' ), array( 'Modelo do Ve', '19', '' ), array( 'Ano Fabrica', '27', '' ), array( 'Placa', '7', '' ), array( 'Chassi', '8', '' ) );

            $conteudo  = ( file_get_contents( $path . $arquivo ) );
            $resultado = Tratar_arquivo_mitsui( $conteudo, $array_lista_itens );

            $seguradora = '';
            $segurado   = '';
            $apolice    = '';
            $veiculo    = '';
            $anoF       = '';
            $anoM       = '';
            $placa      = '';
            $Chassi     = '';
            $dataOcorr  = '';
            $DataAviso  = '';
            $LocalOcorr = '';
            $Condutor   = '';
            $Fone       = '';
            $Bairro     = '';
            $Cidade     = '';
            $UF         = '';
            $CallCenter = '';
            $BO         = '';

            foreach ( $resultado as $value ) {
                switch ( $value[ 0 ] ) {
                    case 'seguradora':
                        $seguradora = $value[ 1 ];
                        break;

                    case 'Data da Ocorr':
                        $dataOcorr = $value[ 1 ];
                        break;

                    case 'Local':
                        $temp = explode( ' /  ', $value[ 1 ] );
                        if ( array_key_exists( 1, $temp ) ) {
                            $Cidade = $temp[ 0 ];
                            $UF     = $temp[ 1 ];
                        } else {
                            $Cidade = $temp[ 0 ];
                        }
                        break;

                    case 'Modelo do Ve':
                        $veiculo = $value[ 1 ];
                        $veiculo = str_replace( ':', '', $veiculo );
                        break;

                    case 'Ano Fabrica':
                        $anos = explode( '/', $value[ 1 ] );
                        $anoF = $anos[ 0 ];
                        $anoM = $anos[ 1 ];
                        break;

                    case 'Placa':
                        $placa = $value[ 1 ];
                        $placa = str_replace( '\n', '', $placa );
                        $placa = str_replace( '-', '', $placa );
                        $placa = str_replace( ' ', '', $placa );
                        break;

                    case 'Chassi':
                        $Chassi = $value[ 1 ];
                        break;

                }
            }
            $segurado   = '';
            $apolice    = '';
            $DataAviso  = '';
            $LocalOcorr = '';
            $Condutor   = '';
            $Fone       = '';
            $Bairro     = '';
            $CallCenter = '';
            $BO         = '';


            $sql     = 'call SP_inserir_veiculos_iportacao("' . $seguradora . '","' . $segurado . '","' . $apolice . '","' . $veiculo . '","' . $anoF . '","' . $anoM . '","' . $placa . '","' . $Chassi . '","' . $dataOcorr . '","' . $DataAviso . '","' . $LocalOcorr . '","' . $Condutor . '","' . $Fone . '","' . $Bairro . '","' . $Cidade . '","' . $UF . '","' . $CallCenter . '","' . $BO . '");';
            $escreve = fwrite( $fp, $sql . "\n" );
            $res     = exec_query_ID( $sql, $mysql_ID );

            fclose( $arq );
            unlink( $path . $arquivo );
        }

        #########################

        if ( $ident == 'MAPFRE' ) {
            $array_lista_itens = array( array( 'protocolo de sinistro', '21', ' conforme' ), array( 'Apólice:', '10', '=' ), array( 'culo:', '5', 'Placa:' ), array( 'Placa:', '6', '' ), array( 'Chassi:', '7', '' ), array( ' informa que em', '15', '=E0s' ), array( 'Cidade:', '7', 'Estado:' ), array( 'Estado:', '7', '' ), array( 'Local do sinistro:', '19', '' ) );
            $conteudo          = ( file_get_contents( $path . $arquivo ) );
            $resultado         = Tratar_arquivo_mapfre( $conteudo, $array_lista_itens );
            $seguradora        = '';
            $segurado          = '';
            $apolice           = '';
            $veiculo           = '';
            $anoF              = '';
            $anoM              = '';
            $placa             = '';
            $Chassi            = '';
            $dataOcorr         = '';
            $DataAviso         = '';
            $LocalOcorr        = '';
            $Condutor          = '';
            $Fone              = '';
            $Bairro            = '';
            $Cidade            = '';
            $UF                = '';
            $CallCenter        = '';
            $BO                = '';

            foreach ( $resultado as $value ) {

                switch ( $value[ 0 ] ) {
                    case 'seguradora':
                        $seguradora = $value[ 1 ];
                        break;

                    case 'protocolo de sinistro':
                        $CallCenter = str_replace( ',', '', $value[ 1 ] );
                        break;

                    case 'culo:':
                        $veiculo = $value[ 1 ];
                        $veiculo = str_replace( ':', '', $veiculo );
                        break;

                    case 'Placa:':
                        $placa = $value[ 1 ];
                        $placa = str_replace( '\n', '', $placa );
                        $placa = str_replace( '-', '', $placa );
                        $placa = str_replace( ' ', '', $placa );
                        break;

                    case 'Chassi:':
                        $Chassi = $value[ 1 ];
                        break;

                    case 'informa que em':
                        $dataOcorr = $value[ 1 ];
                        break;

                    case 'Cidade:':
                        $Cidade = $value[ 1 ];
                        break;

                    case 'Estado:':
                        $UF = $value[ 1 ];
                        break;

                    case 'Local do sinistro:':
                        $LocalOcorr = $value[ 1 ];
                        break;


                }
            }
            $sql     = "call SP_inserir_veiculos_iportacao('$seguradora','$segurado','$apolice','$veiculo','$anoF','$anoM','$placa','$Chassi','$dataOcorr','$DataAviso','$LocalOcorr','$Condutor','$Fone','$Bairro','$Cidade','$UF','$CallCenter','$BO');";
            $escreve = fwrite( $fp, $sql . "\n" );
            `echo $sql >> /home/sistema/email/testesql.txt`;
            $res = exec_query_ID( $sql, $mysql_ID );


            fclose( $arq );
            unlink( $path . $arquivo );
        }

        ############################
        if ( $ident == 'BBMAPFRE' ) {
            $array_lista_itens = array( array( 'protocolo de sinistro', '21', ' conforme' ), array( 'Apólice:', '10', '=' ), array( 'culo:', '5', 'Placa:' ), array( 'Placa:', '6', '' ), array( 'Chassi:', '7', '' ), array( ' informa que em', '15', '=E0s' ), array( 'Cidade:', '7', 'Estado:' ), array( 'Estado:', '7', '' ), array( 'Local do sinistro:', '19', '' ), );
            $conteudo          = ( file_get_contents( $path . $arquivo ) );
            $resultado         = Tratar_arquivo_bbmapfre( $conteudo, $array_lista_itens );
            $seguradora        = '';
            $segurado          = '';
            $apolice           = '';
            $veiculo           = '';
            $anoF              = '';
            $anoM              = '';
            $placa             = '';
            $Chassi            = '';
            $dataOcorr         = '';
            $DataAviso         = '';
            $LocalOcorr        = '';
            $Condutor          = '';
            $Fone              = '';
            $Bairro            = '';
            $Cidade            = '';
            $UF                = '';
            $CallCenter        = '';
            $BO                = '';

            foreach ( $resultado as $value ) {


                switch ( $value[ 0 ] ) {
                    case 'seguradora':
                        $seguradora = $value[ 1 ];
                        break;

                    case 'protocolo de sinistro':
                        $CallCenter = str_replace( ',', '', $value[ 1 ] );
                        break;

                    case 'culo:':
                        $veiculo = $value[ 1 ];
                        $veiculo = str_replace( ':', '', $veiculo );
                        break;

                    case 'Placa:':
                        $placa = $value[ 1 ];
                        $placa = str_replace( '\n', '', $placa );
                        $placa = str_replace( '-', '', $placa );
                        $placa = str_replace( ' ', '', $placa );
                        break;

                    case 'Chassi:':
                        $Chassi = $value[ 1 ];
                        break;

                    case 'informa que em':
                        $dataOcorr = $value[ 1 ];
                        break;

                    case 'Cidade:':
                        $Cidade = $value[ 1 ];
                        break;

                    case 'Estado:':
                        $UF = $value[ 1 ];
                        break;

                    case 'Local do sinistro:':
                        $LocalOcorr = $value[ 1 ];
                        break;


                }

            }
            $sql     = "call SP_inserir_veiculos_iportacao('$seguradora','$segurado','$apolice','$veiculo','$anoF','$anoM','$placa','$Chassi','$dataOcorr','$DataAviso','$LocalOcorr','$Condutor','$Fone','$Bairro','$Cidade','$UF','$CallCenter','$BO');";
            $escreve = fwrite( $fp, $sql . "\n" );
            `echo $sql >> /home/sistema/email/testesql.txt`;
            $res = exec_query_ID( $sql, $mysql_ID );

            fclose( $arq );
            unlink( $path . $arquivo );
        }
        #################################################

        if ( $ident == 'LIBERTY' ) {
            $array_lista_itens = array( array( 'OCORRENCIA:', '12', '' ), array( 'APOLICE: ', '9', '' ), array( 'PLACA:', '6', '' ), array( 'CHASSI:', '7', '' ), array( 'MARCA:', '6', '' ), array( 'MODELO:', '7', '' ), array( 'ANO MODELO:', '11', '' ), array( 'ANO FABRICACAO:', '15', '' ), array( 'DATA DA OCORRENCIA:', '19', '' ), array( 'LOCAL:', '6', '' ), array( 'CIDADE:', '7', '' ), array( 'UF:', '3', '' ) );
            $conteudo          = ( file_get_contents( $path . $arquivo ) );
            $resultado         = Tratar_arquivo_liberty( $conteudo, $array_lista_itens );
            $seguradora        = '';
            $segurado          = '';
            $apolice           = '';
            $veiculo           = '';
            $anoF              = '';
            $anoM              = '';
            $placa             = '';
            $Chassi            = '';
            $dataOcorr         = '';
            $DataAviso         = '';
            $LocalOcorr        = '';
            $Condutor          = '';
            $Fone              = '';
            $Bairro            = '';
            $Cidade            = '';
            $UF                = '';
            $CallCenter        = '';
            $BO                = '';

            foreach ( $resultado as $value ) {


                switch ( $value[ 0 ] ) {
                    case 'seguradora':
                        $seguradora = $value[ 1 ];
                        break;


                    case 'OCORRENCIA:':
                        $CallCenter = str_replace( ',', '', $value[ 1 ] );
                        break;

                    case 'APOLICE: ':
                        $apolice = $value[ 1 ];
                        break;

                    case 'PLACA:':
                        $placa = $value[ 1 ];
                        $placa = str_replace( '\n', '', $placa );
                        $placa = str_replace( '-', '', $placa );
                        $placa = str_replace( ' ', '', $placa );
                        break;

                    case 'CHASSI:':
                        $Chassi = $value[ 1 ];
                        break;

                    case 'MARCA:':
                        $marca = $value[ 1 ];
                        break;

                    case 'MODELO:':
                        $veiculo = $value[ 1 ];
                        $veiculo = str_replace( ':', '', $veiculo );
                        break;


                    case 'ANO MODELO:':
                        $anoM = $value[ 1 ];
                        break;

                    case 'ANO FABRICACAO:':
                        $anoF = $value[ 1 ];
                        break;


                    case 'DATA DA OCORRENCIA:':
                        $dataOcorr = $value[ 1 ];
                        break;

                    case 'LOCAL:':
                        $LocalOcorr = $value[ 1 ];
                        break;

                    case 'CIDADE:':
                        $Cidade = $value[ 1 ];
                        break;

                    case 'UF:':
                        $UF = $value[ 1 ];
                        break;
                }
            }
            $sql     = "call SP_inserir_veiculos_iportacao('$seguradora','$segurado','$apolice','$veiculo','$anoF','$anoM','$placa','$Chassi','$dataOcorr','$DataAviso','$LocalOcorr','$Condutor','$Fone','$Bairro','$Cidade','$UF','$CallCenter','$BO');";
            $escreve = fwrite( $fp, $sql . "\n" );
            `echo $sql >> /home/sistema/email/testesql.txt`;
            $res = exec_query_ID( $sql, $mysql_ID );

            fclose( $arq );
            unlink( $path . $arquivo );

        }

        ############################################################################################################
        if ( $ident == 'AZUL' ) {
            $conteudo   = ( file_get_contents( $path . $arquivo ) );
            $resultado  = Tratar_arquivo_azul( $conteudo );
            $seguradora = '';
            $segurado   = '';
            $apolice    = '';
            $veiculo    = '';
            $anoF       = '';
            $anoM       = '';
            $placa      = '';
            $Chassi     = '';
            $dataOcorr  = '';
            $DataAviso  = '';
            $LocalOcorr = '';
            $Condutor   = '';
            $Fone       = '';
            $Bairro     = '';
            $Cidade     = '';
            $UF         = '';
            $CallCenter = '';
            $BO         = '';

            for ( $f = 1; $f <= ( count( $resultado ) - 1 ); $f++ ) {
                $seguradora = 'AZUL';
                $segurado   = '';
                $apolice    = '';
                $veiculo    = $resultado[ $f ][ 1 ];
                $anoF       = '';
                $anoM       = '';
                $placa      = $resultado[ $f ][ 2 ];
                $placa      = str_replace( '\n', '', $placa );
                $placa      = str_replace( '-', '', $placa );
                $placa      = str_replace( ' ', '', $placa );
                $Chassi     = $resultado[ $f ][ 3 ];
                $dataOcorr  = substr( ( $resultado[ $f ][ 6 ] ), 0, 2 ) . '/' . substr( ( $resultado[ $f ][ 6 ] ), 2, 2 ) . '/' . substr( ( $resultado[ $f ][ 6 ] ), 4, 4 );
                $DataAviso  = '';
                $LocalOcorr = $resultado[ $f ][ 7 ];
                $Condutor   = '';
                $Fone       = '';
                $Bairro     = $resultado[ $f ][ 8 ];
                $Cidade     = $resultado[ $f ][ 9 ];
                $UF         = $resultado[ $f ][ 10 ];
                $CallCenter = '';
                $BO         = '';

                $sql     = "call SP_inserir_veiculos_iportacao('$seguradora','$segurado','$apolice','$veiculo','$anoF','$anoM','$placa','$Chassi','$dataOcorr','$DataAviso','$LocalOcorr','$Condutor','$Fone','$Bairro','$Cidade','$UF','$CallCenter','$BO');";
                $escreve = fwrite( $fp, $sql . "\n" );
                `echo $sql >> /home/sistema/email/testesql.txt`;
                $res = exec_query_ID( $sql, $mysql_ID );


            }

            fclose( $arq );
            unlink( $path . $arquivo );
        }
        ###########################################################################################################

        if ( $ident == 'SUHAI' ) {
            $conteudo  = ( file_get_contents( $path . $arquivo ) );
            $resultado = Tratar_arquivo_anexo_eml( '.xls', $conteudo );

            for ( $f = 1; $f <= ( count( $resultado ) - 1 ); $f++ ) {
                $seguradora = 'SUHAI';
                $segurado   = '';
                $apolice    = '';
                $veiculo    = $resultado[ $f ][ 'MODELO' ];
                $Marca      = $resultado[ $f ][ 'MARCA' ];
                $anoF       = '';
                $anoM       = '';
                $placa      = $resultado[ $f ][ 'PLACA' ];
                $placa      = str_replace( '\n', '', $placa );
                $placa      = str_replace( '-', '', $placa );
                $placa      = str_replace( ' ', '', $placa );
                $Chassi     = $resultado[ $f ][ 'CHASSI' ];
                $datatemp   = explode( ' ', $resultado[ $f ][ 'DATA' ] );
                $dataOcorr  = $datatemp[ 0 ];
                $DataAviso  = '';
                $LocalOcorr = utf8_decode( $resultado[ $f ][ 'Local Sinistro' ] );
                $Condutor   = '';
                $Fone       = '';
                if ( stripos( $resultado[ $f ][ 'ZONA/ESTADO DO SINISTRO' ], '/' ) > 0 ) {
                    $LocalTemp = explode( '/', $resultado[ $f ][ 'ZONA/ESTADO DO SINISTRO' ] );
                    $Cidade    = $LocalTemp[ 0 ];
                    $UF        = $LocalTemp[ 1 ];

                } else {
                    $Cidade = '';
                    $UF     = $resultado[ $f ][ 'ZONA/ESTADO DO SINISTRO' ];

                }
                $Bairro     = '';
                $CallCenter = '';
                $BO         = '';

                $sql     = "call SP_inserir_veiculos_iportacao('$seguradora','$segurado','$apolice','$veiculo','$anoF','$anoM','$placa','$Chassi','$dataOcorr','$DataAviso','$LocalOcorr','$Condutor','$Fone','$Bairro','$Cidade','$UF','$CallCenter','$BO');";
                $escreve = fwrite( $fp, $sql . "\n" );
                `echo $sql >> /home/sistema/email/testesql.txt`;
                $res = exec_query_ID( $sql, $mysql_ID );


            }
            fclose( $arq );
            unlink( $path . $arquivo );
        }
        #######################################33
        if ( $ident == 'SULAMERICA_' ) {
            $conteudo  = ( file_get_contents( $path . $arquivo ) );
            $resultado = Tratar_arquivo_anexo_sulamerica( '.xls', $conteudo );

            for ( $f = 1; $f <= ( count( $resultado ) - 1 ); $f++ ) {
                $seguradora = 'SULAMERICA';
                $segurado   = '';
                $apolice    = '';
                $veiculo    = '';
                $Marca      = '';
                $anoF       = '';
                $anoM       = '';
                $placa      = $resultado[ $f ][ 'COD_PLACA' ];
                $Chassi     = $resultado[ $f ][ 'SIG_CHASSI' ];
                $dataOcorr  = '';
                $DataAviso  = $resultado[ $f ][ 'DAT_AVISO_SINISTRO' ];
                $LocalOcorr = utf8_decode( $resultado[ $f ][ 'NME_BAIRRO' ] );
                $Condutor   = '';
                $Fone       = '';
                $Cidade     = '';
                $UF         = $resultado[ $f ][ 'SIG_UF' ];
                $Bairro     = '';
                $CallCenter = $resultado[ $f ][ 'NUM_SINISTRO' ];
                $BO         = '';
                $sql        = "call SP_inserir_veiculos_iportacao('$seguradora','$segurado','$apolice','$veiculo','$anoF','$anoM','$placa','$Chassi','$dataOcorr','$DataAviso','$LocalOcorr','$Condutor','$Fone','$Bairro','$Cidade','$UF','$CallCenter','$BO');";
                $escreve    = fwrite( $fp, $sql . "\n" );
                `echo $sql >> /home/sistema/email/testesql.txt`;
                $res = exec_query_ID( $sql, $mysql_ID );
            }

            fclose( $arq );
            unlink( $path . $arquivo );
        }

        ############################################################################################################
        if ( $ident == 'ALLIANZ' ) {
            $conteudo   = ( file_get_contents( $path . $arquivo ) );
            $resultado  = Tratar_arquivo_allianz( $conteudo );
            $seguradora = '';
            $segurado   = '';
            $apolice    = '';
            $veiculo    = '';
            $anoF       = '';
            $anoM       = '';
            $placa      = '';
            $Chassi     = '';
            $dataOcorr  = '';
            $DataAviso  = '';
            $LocalOcorr = '';
            $Condutor   = '';
            $Fone       = '';
            $Bairro     = '';
            $Cidade     = '';
            $UF         = '';
            $CallCenter = '';
            $BO         = '';

            for ( $f = 1; $f <= ( count( $resultado ) - 1 ); $f++ ) {
                $seguradora     = 'ALLIANZ';
                $segurado       = '';
                $apolice        = '';
                $veiculo        = $resultado[ $f ][ 'Marca/Modelo' ];
                $anoF           = '';
                $anoM           = '';
                $placa          = $resultado[ $f ][ 'Placa' ];
                $placa          = str_replace( '\n', '', $placa );
                $placa          = str_replace( '-', '', $placa );
                $placa          = str_replace( ' ', '', $placa );
                $Chassi         = $resultado[ $f ][ 'Chassi' ];
                $dataOcorr_temp = $resultado[ $f ][ 'Ocorr=C3=AAncia' ];
                $dataOcorr      = $dataOcorr_temp;
                $DataAviso      = '';
                $LocalOcorr     = '';
                $Condutor       = '';
                $Fone           = '';
                $Bairro         = '';
                $Cidade         = '';
                $UF             = '';
                $CallCenter     = $resultado[ $f ][ 'Sinistro' ];
                $BO             = '';
                $sql            = "call SP_inserir_veiculos_iportacao('$seguradora','$segurado','$apolice','$veiculo','$anoF','$anoM','$placa','$Chassi','$dataOcorr','$DataAviso','$LocalOcorr','$Condutor','$Fone','$Bairro','$Cidade','$UF','$CallCenter','$BO');";
                $escreve        = fwrite( $fp, $sql . "\n" );
                `echo $sql >> /home/sistema/email/testesql.txt`;
                $res = exec_query_ID( $sql, $mysql_ID );
            }
            fclose( $arq );
            unlink( $path . $arquivo );
        }
        fclose( $fp );
    }

    $diretorio->close();

    echo "Fim da importação;";
    `echo "FIM importação;" >> /var/log/syslog`;
?>

