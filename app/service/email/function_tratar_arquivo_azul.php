<?php
    function Tratar_arquivo_azul( $Dados )
    {

        $dados           = $Dados;
        $dados_trat      = explode( PHP_EOL, $dados );
        $cont_dados_trat = count( $dados_trat );


        $temp = 0;
        for ( $i = 0; $cont_dados_trat > $i; $i++ )
        {
            if ( stripos( $dados_trat[ $i ], 'TIPO;PLACA;CHASSI;MOD;' ) > 0 ) {
                $temp = 1;
            }
            if ( ( $temp == 1 ) and ( count( explode( ';', $dados_trat[ $i ] ) ) ) <= 1 ) {
                $temp = 0;
            }

            if ( ( $temp == 1 ) ) {
                $valor[] = explode( ';', $dados_trat[ $i ] );
            }
        }
        return $valor;
    }

