<?php

    function Tratar_arquivo_mapfre( $Dados, $array_lista_itens )
    {
        $dados          = $Dados;
        $dados_trat     = explode( PHP_EOL, $dados );
        $dados_retratar = '';
        foreach ( $dados_trat as $key => $value ) {
            $dados_retratar = $dados_retratar . substr_replace( $value, ' ', -2 );
        }

        $dados_trat      = explode( '=0A', $dados_retratar );
        $cont_dados_trat = count( $dados_trat );

        $valor[] = array( 'seguradora', 'MAPFRE' );
        foreach ( $array_lista_itens as $key => $value ) {
            for ( $i = 0; $cont_dados_trat > $i; $i++ ) #foreach ($dados_trat as $key1 => $value1)
            {
                $value1 = $dados_trat[ $i ];
                $posicao = strpos( $value1, $value[ 0 ] );

                if ( ( $posicao === 0 ) or ( $posicao > 0 ) ) {

                    if ( $value[ 2 ] == '' ) {
                        $valor_temp = ltrim( substr( $value1, $posicao + $value[ 1 ] ), " " );
                    } else {
                        $posicao_final   = strpos( $value1, $value[ 2 ] );
                        $posicao_inicial = $posicao + $value[ 1 ];
                        $posicao_final   = $posicao_final - $posicao_inicial;
                        $valor_temp = substr( $value1, $posicao_inicial, $posicao_final );
                    }

                    if ( strpos( $valor_temp, ':' ) > 0 ) {
                        $temp_array = explode( ':', $valor_temp );
                        $valor_temp = $temp_array[ 1 ];
                    }
                    $valor[] = array( $value[ 0 ], $valor_temp );
                    $posicao = '';
                    break;
                }
            }
        }
        return $valor;
    }

