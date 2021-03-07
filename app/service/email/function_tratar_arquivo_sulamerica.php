<?php


    function Tratar_arquivo_anexo_sulamerica( $extensao, $Dados )
    {

        $dados           = $Dados;
        $dados_trat      = explode( PHP_EOL, $dados );
        $cont_dados_trat = count( $dados_trat );
        $xls_part        = '';

        $temp    = 0;
        $valor[] = array( 'seguradora', 'SULAMERICA' );

        for ( $i = 0; $cont_dados_trat > $i; $i++ ) #foreach ($array_lista_itens as $key => $value)
        {
            if ( ( stripos( $dados_trat[ $i ], $extensao ) > 0 ) ) {
                $temp = 1;
            }
            if ( ( $temp == 2 ) and ( ( strlen( $dados_trat[ $i ] ) ) <= 1 or substr_count( $dados_trat[ $i ], '--' ) == 2 ) ) {
                $temp = 0;
            }

            if ( ( $temp == 1 ) and ( strlen( $dados_trat[ $i ] ) <= 1 ) ) {
                $temp = 2;
            }

            if ( ( $temp == 2 ) ) {

                $xls_part = $xls_part . $dados_trat[ $i ];
            }


        }
        $xls = imap_base64( $xls_part );

        $fp = fopen( "Imp_tmp.txt", "w" );

        $escreve = fwrite( $fp, $xls );

        fclose( $fp );
        $valor = coverte_xlsx_csv1( "Imp_tmp.txt" );
        return $valor;
    }


    function coverte_xlsx_csv1( $arq )
    {
        require_once 'PHPexcel/Classes/PHPExcel/IOFactory.php';
        $xls    = PHPExcel_IOFactory::load( $arq );
        $writer = PHPExcel_IOFactory::createWriter( $xls, 'CSV' );
        $writer->setDelimiter( ";" );
        $writer->setEnclosure( "" );
        $writer->save( "teste.csv" );
        $ret = csvToArray1( "teste.csv" );
        return $ret;
    }

    function csvToArray1( $file )
    {
        $rows    = array();
        $headers = array();
        if ( file_exists( $file ) && is_readable( $file ) ) {
            $handle = fopen( $file, 'r' );
            while ( !feof( $handle ) ) {
                $row = fgetcsv( $handle, 10240, ';', '"' );
                if ( empty( $headers ) )
                    $headers = $row; else if ( is_array( $row ) ) {
                    array_splice( $row, count( $headers ) );
                    $rows[] = array_combine( $headers, $row );
                }
            }
            fclose( $handle );
        } else {
            throw new Exception( $file . ' doesn`t exist or is not readable.' );
        }
        return $rows;
    }

