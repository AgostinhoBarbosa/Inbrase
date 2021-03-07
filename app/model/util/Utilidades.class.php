<?php

    use Adianti\Widget\Form\TButton;

    class Utilidades
    {
        public static function onDataAtual( $mostra_semana = TRUE, $value = NULL, $cidade_uf = '' )
        {
            if ( empty( $value ) ) {
                $value = date( 'Y-m-d H:i:s' );
            }
            $dia    = date( 'd', strtotime( $value ) );
            $mes    = date( 'm', strtotime( $value ) );
            $ano    = date( 'Y', strtotime( $value ) );
            $semana = date( 'w', strtotime( $value ) );
            $hora   = date( 'H', strtotime( $value ) );
            $minuto = date( 'i', strtotime( $value ) );
            $cidade = $cidade_uf;

            switch ( $mes ) {
                case 1:
                    $mes = "Janeiro";
                    break;
                case 2:
                    $mes = "Fevereiro";
                    break;
                case 3:
                    $mes = "Março";
                    break;
                case 4:
                    $mes = "Abril";
                    break;
                case 5:
                    $mes = "Maio";
                    break;
                case 6:
                    $mes = "Junho";
                    break;
                case 7:
                    $mes = "Julho";
                    break;
                case 8:
                    $mes = "Agosto";
                    break;
                case 9:
                    $mes = "Setembro";
                    break;
                case 10:
                    $mes = "Outubro";
                    break;
                case 11:
                    $mes = "Novembro";
                    break;
                case 12:
                    $mes = "Dezembro";
                    break;
            }

            switch ( $semana ) {
                case 0:
                    $semana = "Domingo";
                    break;
                case 1:
                    $semana = "Segunda Feira";
                    break;
                case 2:
                    $semana = "Terça Feira";
                    break;
                case 3:
                    $semana = "Quarta Feira";
                    break;
                case 4:
                    $semana = "Quinta Feira";
                    break;
                case 5:
                    $semana = "Sexta Feira";
                    break;
                case 6:
                    $semana = "Sábado";
                    break;
            }

            if ( $mostra_semana ) {
                if ( empty( $cidade ) ) {
                    if ( $hora == '00' ){
                        return ( "$semana, $dia de $mes de $ano");
                    }else {
                        return ( "$semana, $dia de $mes de $ano - $hora:$minuto" );
                    }
                } else {
                    if ( $hora == '00' ) {
                        return ( "$cidade, $semana, $dia de $mes de $ano" );
                    }else {
                        return ( "$cidade, $semana, $dia de $mes de $ano - $hora:$minuto" );
                    }
                }
            } else {
                if ( empty( $cidade ) ) {
                    if ($hora == '00') {
                        return ( "$dia de $mes de $ano" );
                    }else{
                        return ( "$dia de $mes de $ano - $hora:$minuto" );
                    }
                } else {
                    if ( $hora == '00' ) {
                        return ( "$cidade  , $dia de $mes de $ano" );
                    }else {
                        return ( "$cidade  , $dia de $mes de $ano - $hora:$minuto" );
                    }
                }
            }
        }

        public static function onCriaDiretorio( $diretorio )
        {
            if ( ! file_exists( $diretorio ) ) {
                return mkdir( $diretorio, 0777, TRUE );
            }
            return FALSE;
        }

        public static function onValidaValor( $valor )
        {
            if ( empty( $valor ) ) {
                return 0;
            }
            return $valor;
        }

        public static function onAnoCorrente()
        {
            $value = date( 'Y-m-d H:i:s' );

            return date( 'Y', strtotime( $value ) );
        }

        public static function dataExtenso( $value )
        {
            if ( empty( $value ) ) {
                return $value;
            }

            $dia = date( 'd', strtotime( $value ) );
            $mes = date( 'm', strtotime( $value ) );
            $ano = date( 'Y', strtotime( $value ) );

            $dia_extenso = self::valorPorExtenso( intval( $dia ), FALSE );
            $mes_extenso = self::mes_extenso( $mes );
            $ano_extenso = self::valorPorExtenso( $ano, FALSE );

            return mb_strtolower( $dia_extenso . ' dias do mês de ' . $mes_extenso . ' do ano de ' . $ano_extenso );
        }

        public static function valorPorExtenso( $valor = 0, $bolExibirMoeda = TRUE, $bolPalavraFeminina = FALSE )
        {

            $valor = self::removerFormatacaoNumero( $valor );

            $singular = NULL;
            $plural   = NULL;

            if ( $bolExibirMoeda ) {
                $singular = ["centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão"];
                $plural   = ["centavos", "reais", "mil", "milhões", "bilhões", "trilhões", "quatrilhões"];
            } else {
                $singular = ["", "", "mil", "milhão", "bilhão", "trilhão", "quatrilhão"];
                $plural   = ["", "", "mil", "milhões", "bilhões", "trilhões", "quatrilhões"];
            }

            $c   = ["", "cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos",
                    "oitocentos", "novecentos"];
            $d   = ["", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta",
                    "noventa"];
            $d10 = ["dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezessete", "dezoito",
                    "dezenove"];
            $u   = ["", "um", "dois", "três", "quatro", "cinco", "seis", "sete", "oito", "nove"];

            if ( $bolPalavraFeminina ) {

                if ( $valor == 1 ) {
                    $u = ["", "uma", "duas", "três", "quatro", "cinco", "seis", "sete", "oito", "nove"];
                } else {
                    $u = ["", "um", "duas", "três", "quatro", "cinco", "seis", "sete", "oito", "nove"];
                }

                $c = ["", "cem", "duzentas", "trezentas", "quatrocentas", "quinhentas", "seiscentas", "setecentas",
                      "oitocentas", "novecentas"];

            }

            $z = 0;

            $valor   = number_format( $valor, 2, ".", "." );
            $inteiro = explode( ".", $valor );

            for ( $i = 0, $iMax = count( $inteiro ); $i < $iMax; $i++ ) {
                for ( $ii = mb_strlen( $inteiro[ $i ] ); $ii < 3; $ii++ ) {
                    $inteiro[ $i ] = "0" . $inteiro[ $i ];
                }
            }

            // $fim identifica onde que deve se dar junção de centenas por "e" ou por "," ;)
            $rt  = NULL;
            $fim = count( $inteiro ) - ( $inteiro[ count( $inteiro ) - 1 ] > 0 ? 1 : 2 );
            for ( $i = 0, $iMax = count( $inteiro ); $i < $iMax; $i++ ) {
                $valor = $inteiro[ $i ];
                $rc    = ( ( $valor > 100 ) && ( $valor < 200 ) ) ? "cento" : $c[ $valor[ 0 ] ];
                $rd    = ( $valor[ 1 ] < 2 ) ? "" : $d[ $valor[ 1 ] ];
                $ru    = ( $valor > 0 ) ? ( ( $valor[ 1 ] == 1 ) ? $d10[ $valor[ 2 ] ] : $u[ $valor[ 2 ] ] ) : "";

                $r = $rc . ( ( $rc && ( $rd || $ru ) ) ? " e " : "" ) . $rd . ( ( $rd && $ru ) ? " e " : "" ) . $ru;
                $t = count( $inteiro ) - 1 - $i;
                $r .= $r ? " " . ( $valor > 1 ? $plural[ $t ] : $singular[ $t ] ) : "";
                if ( $valor == "000" ) {
                    $z++;
                } elseif ( $z > 0 ) {
                    $z--;
                }

                if ( ( $t == 1 ) && ( $z > 0 ) && ( $inteiro[ 0 ] > 0 ) ) {
                    $r .= ( ( $z > 1 ) ? " de " : "" ) . $plural[ $t ];
                }

                if ( $r ) {
                    $rt = $rt . ( ( ( $i > 0 ) && ( $i <= $fim ) && ( $inteiro[ 0 ] > 0 ) && ( $z < 1 ) ) ? ( ( $i < $fim ) ? ", " : " e " ) : " " ) . $r;
                }
            }

            $rt = mb_substr( $rt, 1 );

            return ( $rt ? trim( $rt ) : "zero" );

        }

        public static function mes_extenso( $mes )
        {
            switch ( $mes ) {
                case 1:
                    $mes = "Janeiro";
                    break;
                case 2:
                    $mes = "Fevereiro";
                    break;
                case 3:
                    $mes = "Março";
                    break;
                case 4:
                    $mes = "Abril";
                    break;
                case 5:
                    $mes = "Maio";
                    break;
                case 6:
                    $mes = "Junho";
                    break;
                case 7:
                    $mes = "Julho";
                    break;
                case 8:
                    $mes = "Agosto";
                    break;
                case 9:
                    $mes = "Setembro";
                    break;
                case 10:
                    $mes = "Outubro";
                    break;
                case 11:
                    $mes = "Novembro";
                    break;
                case 12:
                    $mes = "Dezembro";
                    break;
            }

            return $mes;
        }

        public static function removerFormatacaoNumero( $strNumero )
        {
            if ( $strNumero == NULL ) {
                return 0;
            }

            $strNumero = trim( str_replace( "R$", NULL, $strNumero ) );

            $vetVirgula = explode( ",", $strNumero );
            if ( count( $vetVirgula ) == 1 ) {
                $acentos   = ["."];
                $resultado = str_replace( $acentos, "", $strNumero );

                return $resultado;
            } elseif ( count( $vetVirgula ) != 2 ) {
                return $strNumero;
            }

            $strNumero  = $vetVirgula[ 0 ];
            $strDecimal = mb_substr( $vetVirgula[ 1 ], 0, 2 );

            $acentos   = ["."];
            $resultado = str_replace( $acentos, "", $strNumero );
            $resultado = $resultado . "." . $strDecimal;

            return $resultado;

        }

        public static function formato_monetario( $value )
        {
            if ( empty( $value ) && ( $value !== 0.00 ) ) {
                return $value;
            }

            return number_format( $value, 2, ',', '.' );
        }

        public static function calcula_prazo( $datacal, $prazo )
        {
            $retorno = $datacal;

            if ( is_null( $datacal ) ) {
                return $retorno;
            }
            $I = 0;
            while ( $I < $prazo ) {
                $retorno    = date( 'Y-m-d', strtotime( $retorno . '+1 days' ) );
                $DIA_SEMANA = Utilidades::dia_semana( $retorno );

                while ( ( $DIA_SEMANA == 0 ) or ( $DIA_SEMANA == 6 ) ) {
                    $retorno = date( 'Y-m-d', strtotime( $retorno . '+1 days' ) );

                    $ACHOU = 1;

                    while ( $ACHOU == 1 ) {
                        $feriados = Calendario::where( 'cal_data', '=', $retorno )->load();
                        if ( count( $feriados ) === 0 ) {
                            $ACHOU = 0;
                        } else {
                            $retorno = date( 'Y-m-d', strtotime( $retorno . '+1 days' ) );
                        }
                    }
                    $DIA_SEMANA = Utilidades::dia_semana( $retorno );
                }

                $ACHOU = 1;

                while ( $ACHOU === 1 ) {

                    $feriados = Calendario::where( 'cal_data', '=', $retorno )->load();
                    if ( count( $feriados ) === 0 ) {
                        $ACHOU = 0;
                    } else {
                        $retorno = date( 'Y-m-d', strtotime( $retorno . '+1 days' ) );
                    }
                }
                $DIA_SEMANA = Utilidades::dia_semana( $retorno );

                if ( ( $DIA_SEMANA > 0 ) and ( $DIA_SEMANA < 6 ) ) {
                    $I++;
                }
            }

            return $retorno;
        }

        public static function dia_semana( $Data )
        {
            return date( 'w', strtotime( $Data ) );
        }

        public static function ValidaData( $dat )
        {
            $data = explode( "/", "$dat" ); // fatia a string $dat em pedados, usando / como referência
            if ( count( $data ) < 3 ) {
                return FALSE;
            }
            $d = $data[ 0 ];
            $m = $data[ 1 ];
            $y = $data[ 2 ];

            // verifica se a data é válida!
            // 1 = true (válida)
            // 0 = false (inválida)
            $res = checkdate( $m, $d, $y );
            if ( $res == 1 ) {
                return TRUE;
            } else {
                return FALSE;
            }
        }

        public static function debito_credito()
        {
            $retorno        = [];
            $retorno[ 'C' ] = 'Crédito';
            $retorno[ 'D' ] = 'Débito';

            return $retorno;
        }

        public static function pagar_receber()
        {
            $retorno        = [];
            $retorno[ 'P' ] = 'Contas a Pagar';
            $retorno[ 'R' ] = 'Contas a Receber';

            return $retorno;
        }

        public static function sim_nao()
        {
            $sim_nao        = [];
            $sim_nao[ '0' ] = 'Não';
            $sim_nao[ '1' ] = 'Sim';

            return $sim_nao;
        }

        public static function sinal()
        {
            $sinal        = [];
            $sinal[ '+' ] = '+';
            $sinal[ '-' ] = '-';

            return $sinal;
        }

        public static function tipo_pessoa()
        {
            $tipopessoa        = [];
            $tipopessoa[ '0' ] = 'Não Definido';
            $tipopessoa[ '1' ] = 'Física';
            $tipopessoa[ '2' ] = 'Jurídica';

            return $tipopessoa;
        }

        public static function uf() {
            return array(
                'AC' => 'Acre',
                'AG' => 'Argentina',
                'AL' => 'Alagoas',
                'AP' => 'Amapá',
                'AM' => 'Amazonas',
                'BA' => 'Bahia',
                'BO' => 'Bolivia',
                'CE' => 'Ceara',
                'CH' => 'Chile',
                'DF' => 'Distrito Federal',
                'ES' => 'Espírito Santo',
                'GO' => 'Goiás',
                'MA' => 'Maranhão',
                'MT' => 'Mato Grosso',
                'MS' => 'Mato Grosso do Sul',
                'MG' => 'Minas Gerais',
                'PA' => 'Pará',
                'PB' => 'Paraíba',
                'PG' => 'Paraguai',
                'PR' => 'Paraná',
                'PE' => 'Pernambuco',
                'PI' => 'Piauí',
                'RJ' => 'Rio de Janeiro',
                'RN' => 'Rio Grande do Norte',
                'RS' => 'Rio Grande do Sul',
                'RO' => 'Rondônia',
                'RR' => 'Roraima',
                'SC' => 'Santa Catarina',
                'SP' => 'São Paulo',
                'SE' => 'Sergipe',
                'TO' => 'Tocantins',
                'UR' => 'Uruguai'
            );

        }

        public static function tipo_ordem()
        {
            $tipo_ordem        = [];
            $tipo_ordem[ '1' ] = 'Por Livro';
            $tipo_ordem[ '2' ] = 'Por Data';
            $tipo_ordem[ '3' ] = 'Sequencial';

            return $tipo_ordem;
        }

        public static function periodo()
        {
            $retorno        = [];
            $retorno[ 'D' ] = 'Diário';
            $retorno[ 'S' ] = 'Semanal';
            $retorno[ 'Q' ] = 'Quinzena';
            $retorno[ 'm' ] = 'Mensal';

            return $retorno;
        }

        public static function serasa_boavista()
        {
            $retorno        = [];
            $retorno[ '1' ] = 'Serasa';
            $retorno[ '2' ] = 'Boa Vista';

            return $retorno;
        }

        public static function tipo_envio()
        {
            $retorno        = [];
            $retorno[ '0' ] = 'Ambos';
            $retorno[ '1' ] = 'Protestado';
            $retorno[ '2' ] = 'Cancelado';

            return $retorno;
        }

        public static function tipo_endosso()
        {
            $tipo_endosso        = [];
            $tipo_endosso[ ' ' ] = '';
            $tipo_endosso[ 'M' ] = 'Mandato';
            $tipo_endosso[ 'T' ] = 'Traslativo';

            return $tipo_endosso;
        }

        public static function tipo_apresentante()
        {
            $tipo        = [];
            $tipo[ '0' ] = 'Banco';
            $tipo[ '1' ] = 'Empresa';
            $tipo[ '2' ] = 'Condomínio';
            $tipo[ '3' ] = 'Governo';
            $tipo[ '4' ] = 'Particular';

            return $tipo;
        }

        public static function calculo_digito_cenprot( $ibge, $seq, $cart, $data )
        {
            $digito  = 0;
            $seq     = str_pad( $seq, 7, '0', STR_PAD_LEFT );
            $data    = date( 'ymd', strtotime( $data ) );
            $fator   = '56738457667677419511020';
            $calculo = $ibge . $seq . $digito . $cart . $data;
            $arr1    = str_split( $calculo );
            $arr2    = str_split( $fator );
            $arr3    = [];
            $total   = count( $arr1 );
            for ( $i = 0; $i < $total; $i++ ) {
                $arr3[ $i ] = $arr1[ $i ] * $arr2[ $i ];
                if ( $arr3[ $i ] > 9 ) {
                    $arr3[ $i ] = substr( $arr3[ $i ], -1 );
                }
            }
            $soma = array_sum( $arr3 );
            $soma = substr( $soma, -1 );
            if ( $soma == 0 ) {
                $retorno = 0;
            } else {
                $retorno = 10 - $soma;
            }

            $ret = $ibge . $seq . $retorno . $cart . $data;
            return $ret;
        }

        public static function tipo_certidao()
        {
            $tipo_certidao        = [];
            $tipo_certidao[ '1' ] = 'Certidão Negativa';
            $tipo_certidao[ '2' ] = 'Certidão Positiva';
            $tipo_certidao[ '3' ] = 'Certidão Cancelamento';
            $tipo_certidao[ '4' ] = 'Certidão Ocorrẽncia';

            return $tipo_certidao;
        }

        public static function arquivo_certidao()
        {
            $tipo_certidao        = [];
            $tipo_certidao[ '1' ] = 'Negativa_' . rand() . '_';
            $tipo_certidao[ '2' ] = 'Positiva_' . rand() . '_';
            $tipo_certidao[ '3' ] = 'Cancelamento_' . rand() . '_';

            return $tipo_certidao;
        }

        public static function vencto_juros()
        {
            $vencto_juros        = [];
            $vencto_juros[ '1' ] = 'Data de Entrada';
            $vencto_juros[ '2' ] = 'Data Final Triduo';

            return $vencto_juros;
        }

        public static function diferenca_entre_datas( $data_inicial, $data_final )
        {
            $diferenca = strtotime( $data_final ) - strtotime( $data_inicial );
            //Calcula a diferença em dias
            $dias = floor( $diferenca / ( 60 * 60 * 24 ) );

            return $dias;
        }

        public static function existe_no_array( $buscar, $array, $campo )
        {
            $retorno = FALSE;
            foreach ( $array as $obj ) {
                if ( is_object( $obj ) ) {
                    if ( $buscar == $obj->$campo ) {
                        $retorno = TRUE;
                        break;
                    }
                }
                if ( is_array( $obj ) ) {
                    if ( $buscar == $obj[ $campo ] ) {
                        $retorno = TRUE;
                        break;
                    }
                }

            }

            return $retorno;
        }


        // String a ser limitada
        // $string = 'Como limitar caracteres sem cortar as palavras com PHP';
        // Mostrando a string limitada em 25 caracteres.
        //print(limitarTexto($string, $limite = 25));
        public static function limitarTexto( $texto, $limite, $quebrar = TRUE )
        {
            //corta as tags do texto para evitar corte errado
            $contador = strlen( strip_tags( $texto ) );
            if ( $contador <= $limite ):
                //se o número do texto form menor ou igual o limite então retorna ele mesmo
                $newtext = $texto;
            else:
                if ( $quebrar == TRUE ): //se for maior e $quebrar for true
                    //corta o texto no limite indicado e retira o ultimo espaço branco
                    $newtext = trim( mb_substr( $texto, 0, $limite ) ) . "...";
                else:
                    //localiza ultimo espaço antes de $limite
                    $ultimo_espaço = strrpos( mb_substr( $texto, 0, $limite ), " " );
                    //corta o $texto até a posição lozalizada
                    $newtext = trim( mb_substr( $texto, 0, $ultimo_espaço ) ) . "...";
                endif;
            endif;

            return $newtext;
        }

        public static function juntar_pdf( $arquivos, $nome = "Relatorio", $apagar = TRUE )
        {

            $rand     = rand( 0, 99999 );
            $nome_pdf = $_SERVER[ 'DOCUMENT_ROOT' ] . "/tmp/" . $nome . "_" . $rand . ".pdf";
            $cmd      = "gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=$nome_pdf ";

            foreach ( $arquivos as $file ) {
                $cmd .= $file . " ";
            }

            shell_exec( $cmd );

            if ( $apagar ) {
                foreach ( $arquivos as $value ) {
                    if ( file_exists( $value ) ) {
                        unlink( $value );
                    }
                }
            }

            return $nome_pdf;
        }

        public static function montarString( $valor, $TotalDeEspaco_A_Completar, $aEsquerda, $caracterParaCompletar = ' ' )
        {
            $valor = Utilidades::removerAcento( $valor );
            if ( strlen( $valor ) > $TotalDeEspaco_A_Completar ) {
                $valor = substr( $valor, 0, $TotalDeEspaco_A_Completar );
            }
            if ( $aEsquerda ) {
                return str_pad( $valor, $TotalDeEspaco_A_Completar, $caracterParaCompletar, STR_PAD_LEFT );
            } else {
                return str_pad( $valor, $TotalDeEspaco_A_Completar, $caracterParaCompletar, STR_PAD_RIGHT );
            }
        }

        public static function removerAcento( $value )
        {
            $comAcentos = ['à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó',
                           'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë',
                           'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ü', 'Ú', '&', 'º'];
            $semAcentos = ['a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o',
                           'o', 'o', 'o', 'u', 'u', 'u', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E',
                           'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'E', ''];

            return str_replace( $comAcentos, $semAcentos, $value );
        }

        public static function onCep( $param )
        {
            $param = preg_replace( "/\D/", "", $param );

            return @file_get_contents( 'http://viacep.com.br/ws/' . urlencode( $param ) . '/json' );
        }

        public static function onCNPJ( $param )
        {
            $param = preg_replace( "/\D/", "", $param );

            return @file_get_contents( 'https://www.receitaws.com.br/v1/cnpj/' . urlencode( $param ) );
        }

        public static function mask( $val, $mask )
        {
            $maskared = '';
            $k        = 0;
            for ( $i = 0; $i <= strlen( $mask ) - 1; $i++ ) {
                if ( $mask[ $i ] == '#' ) {
                    if ( isset( $val[ $k ] ) ) {
                        $maskared .= $val[ $k++ ];
                    }
                } else {
                    if ( isset( $mask[ $i ] ) ) {
                        $maskared .= $mask[ $i ];
                    }
                }
            }

            return $maskared;
        }

        public static function format( $mask, $string )
        {
            $string = Utilidades::soNumero( $string );

            return vsprintf( $mask, str_split( $string ) );
        }

        public static function soNumero( $str )
        {
            return preg_replace( "/[^0-9]/", "", $str );
        }

        public static function Valor( $valor )
        {
            $verificaPonto = ".";
            if ( strpos( "[" . $valor . "]", "$verificaPonto" ) ):
                $valor = str_replace( '.', '', $valor );
                $valor = str_replace( ',', '.', $valor );
            else:
                $valor = str_replace( ',', '.', $valor );
            endif;

            return $valor;
        }

        public static function onEnableCertidao( $data )
        {
            if ( isset( $data[ 'certidao_negativa' ] ) and $data[ 'certidao_negativa' ] === TRUE ) {
                TButton::enableField( 'form_search_consultaParaCertidao', 'negativa' );
            } else {
                TButton::disableField( 'form_search_consultaParaCertidao', 'negativa' );
            }
            if ( isset( $data[ 'certidao_positiva' ] ) and $data[ 'certidao_positiva' ] === TRUE ) {
                TButton::enableField( 'form_search_consultaParaCertidao', 'positiva' );
            } else {
                TButton::disableField( 'form_search_consultaParaCertidao', 'positiva' );
            }
            if ( isset( $data[ 'certidao_cancelamento' ] ) and $data[ 'certidao_cancelamento' ] === TRUE ) {
                TButton::enableField( 'form_search_consultaParaCertidao', 'cancelamento' );
            } else {
                TButton::disableField( 'form_search_consultaParaCertidao', 'cancelamento' );
            }
        }
        public static function tipo_chassi() {
            $tipochassi        = array();
            $tipochassi[ '1' ] = 'Normal';
            $tipochassi[ '2' ] = 'Remarcado';

            return $tipochassi;
        }

        public static function tipo_veiculo() {
            $tipoveiculo        = array();
            $tipoveiculo[ '1' ] = 'Normal';
            $tipoveiculo[ '2' ] = 'Empresa';
            $tipoveiculo[ '3' ] = 'Despachante';
            $tipoveiculo[ '4' ] = 'Compulsório';
            $tipoveiculo[ '5' ] = 'Outros';

            return $tipoveiculo;
        }

        public static function condicao_chassi_motor() {
            $condicao        = array();
            $condicao[ '1' ] = 'Integro';
            $condicao[ '2' ] = 'Adulterado';
            $condicao[ '3' ] = 'Suprimido';
            $condicao[ '4' ] = 'Não Informado';

            return $condicao;
        }

        public static function estado_bem() {
            $estado        = array();
            $estado[ '1' ] = 'Pequena Monta';
            $estado[ '2' ] = 'Média Monta';
            $estado[ '3' ] = 'Grande Monta';
            $estado[ '4' ] = 'Sem Avarias';
            $estado[ '5' ] = 'Não Informado';

            return $estado;
        }

        public static function mostra_sessao() {
            foreach ( $_SESSION as $session => $svalor ) {
                echo "<pre>";
                foreach ( $svalor as $val1 => $val2 ) {
                    if ( is_array( $val2 ) ) {
                        print "\$_SESSION['$session'][$val1] =  ARRAY com ".count( $val2 )." elementos <br>";
                        print_r( $val2 )."<br>";
                    } else {
                        if ( is_object( $val2 ) ) {
                            print "\$_SESSION['$session'][$val1] = OBJETO com ".count( $val2 )." elementos <br>";
                            print_r( $val2 )."<br>";
                        } else {
                            print "\$_SESSION['$session'][$val1] = ".$val2."<br>";
                        }
                    }
                }
                echo "</pre>";
            }
        }

        public static function convert_date( $date_string ) {
            $date = DateTime::createFromFormat( 'Y-m-d H:i', $date_string ); //2016-10-05 16:00

            return $date;
        }

        public static function tipo_liberacao() {
            $retorno        = array();
            $retorno[ '1' ] = 'Adminsitrativa';
            $retorno[ '2' ] = 'Judicial';
            $retorno[ '3' ] = 'Internacional';

            return $retorno;
        }

        public static function onDocumentos() {
            $documentos       = array();
            $documentos[ 0 ]  = "Auto de Entrega";
            $documentos[ 1 ]  = "B.O De Apreens&atilde;o";
            $documentos[ 2 ]  = "B.O de Localiza&ccedil;&atilde;o/devolu&ccedil;&atilde;o";
            $documentos[ 3 ]  = "B.O de Roubo";
            $documentos[ 4 ]  = "Carta Laudo";
            $documentos[ 5 ]  = "Certificado de Licenciamento";
            $documentos[ 6 ]  = "D.U.T";
            $documentos[ 7 ]  = "Decalque";
            $documentos[ 8 ]  = "Extrato / Baixa";
            $documentos[ 9 ]  = "Fotos";
            $documentos[ 10 ] = "Instrumento de Libera&ccedil;&atilde;o";
            $documentos[ 11 ] = "Laudo Pericial";
            $documentos[ 12 ] = "Carta Laudo";
            $documentos[ 13 ] = "Recibo de Indeniza&ccedil;&atilde;o";
            $documentos[ 14 ] = "Vistoria";
            $documentos[ 15 ] = "Nota Fiscal Afincco (PRESTA&Ccedil;&Atilde;O DE SERVI&Ccedil;OS )";
            $documentos[ 16 ] = "Recibo de Despesas";
            $documentos[ 17 ] = "Sucata";
            $documentos[ 18 ] = "Recuperável";
            $documentos[ 19 ] = "Recuperável com baixa fora da origem";
            $documentos[ 20 ] = "Adulterado";
            $documentos[ 21 ] = "Adulterado com baixa da origem";

            return $documentos;
        }

        public static function tipo_conta() {
            $tipoconta        = array();
            $tipoconta[ '1' ] = 'Conta Corrente';
            $tipoconta[ '2' ] = 'Aplicação';
            $tipoconta[ '3' ] = 'Funcionário';
            $tipoconta[ '4' ] = 'Cliente';
            $tipoconta[ '5' ] = 'Fornecedor';

            return $tipoconta;
        }

        public static function doctos() {
            $doctos       = array();
            $doctos[ 1 ]  = 'Laudo Inmetro';
            $doctos[ 2 ]  = 'Vistoria Detran';
            $doctos[ 3 ]  = 'Copia RG/CPF';
            $doctos[ 4 ]  = 'Comprovante Endereço';
            $doctos[ 5 ]  = 'Notas das Peças';
            $doctos[ 6 ]  = 'Nota Mão-de-obra';
            $doctos[ 7 ]  = 'Cópia CRLV';
            $doctos[ 8 ]  = 'Procuração';
            $doctos[ 9 ]  = 'Termo';
            $doctos[ 10 ] = 'Débitos';
            $doctos[ 11 ] = 'Baixa FT';
            $doctos[ 12 ] = 'Bloqueio Sinistro';
            $doctos[ 13 ] = 'Bloqueio Furto';
            $doctos[ 14 ] = 'Decalque';
            $doctos[ 15 ] = 'CRV';
            $doctos[ 16 ] = 'Nota Fiscal Compra';

            return $doctos;
        }

        public static function tamanho_arquivo( $arquivo, $decimal = 2 ) {
            $sz     = 'BKMGTP';
            $factor = floor( ( strlen( $arquivo ) - 1 ) / 3 );

            return sprintf( "%.{$decimal}f", $arquivo / pow( 1024, $factor ) ).@$sz[ $factor ];
        }

        public static function status_recibo() {
            $deb_cre                = array();
            $deb_cre[ 'Ativo' ]     = 'Ativo';
            $deb_cre[ 'Cancelado' ] = 'Cancelado';

            return $deb_cre;
        }


        public static function tipo_telefone() {
            $tipofone        = array();
            $tipofone[ '1' ] = 'Residencial';
            $tipofone[ '2' ] = 'Celular';
            $tipofone[ '3' ] = 'Comercial';

            return $tipofone;
        }

        public static function quem_assina() {
            $retorno        = array();
            $retorno[ '1' ] = 'David';
            $retorno[ '2' ] = 'Debora';
            $retorno[ '3' ] = 'Rafael';

            return $retorno;
        }


        public static function geraSenha( $tamanho = 8, $maiusculas = TRUE, $numeros = TRUE, $simbolos = FALSE ) {
            $lmai       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $num        = '1234567890';
            $retorno    = '';
            $caracteres = '';
            $caracteres .= $lmai;
            $caracteres .= $num;
            $len        = strlen( $caracteres );

            for ( $n = 1; $n <= $tamanho; $n++ ) {
                $rand    = mt_rand( 1, $len );
                $retorno .= $caracteres[ $rand - 1 ];
            }

            return $retorno;
        }
        // String a ser limitada
        // $string = 'Como limitar caracteres sem cortar as palavras com PHP';
        // Mostrando a string limitada em 25 caracteres.
        //print(limitarTexto($string, $limite = 25));

        public static function Valor_Excel( $valor ) {
            $valor = str_replace( 'R$', '', $valor );
            $valor = str_replace( ',', '', $valor );

            return $valor;
        }

        public static function converte_string( $value ) {
            $atual = mb_detect_encoding($value);
            return iconv($atual, 'UTF-8', $value );
        }

        public static function unformataNumero( $varnumero ) {
            return str_replace(['.',','], ['.',''], $varnumero);
        }

        function masc_tel( $TEL ) {
            $tam = strlen( preg_replace( "/[^0-9]/", "", $TEL ) );
            if ( $tam == 13 ) { // COM CÓDIGO DE ÁREA NACIONAL E DO PAIS e 9 dígitos
                return "+".substr( $TEL, 0, $tam - 11 )."(".substr( $TEL, $tam - 11, 2 ).")".substr( $TEL, $tam - 9, 5 )."-".substr( $TEL, -4 );
            }
            if ( $tam == 12 ) { // COM CÓDIGO DE ÁREA NACIONAL E DO PAIS
                return "+".substr( $TEL, 0, $tam - 10 )."(".substr( $TEL, $tam - 10, 2 ).")".substr( $TEL, $tam - 8, 4 )."-".substr( $TEL, -4 );
            }
            if ( $tam == 11 ) { // COM CÓDIGO DE ÁREA NACIONAL e 9 dígitos
                return "(".substr( $TEL, 0, 2 ).")".substr( $TEL, 2, 5 )."-".substr( $TEL, 7, 11 );
            }
            if ( $tam == 10 ) { // COM CÓDIGO DE ÁREA NACIONAL
                return "(".substr( $TEL, 0, 2 ).")".substr( $TEL, 2, 4 )."-".substr( $TEL, 6, 10 );
            }
            if ( $tam <= 9 ) { // SEM CÓDIGO DE ÁREA
                return substr( $TEL, 0, $tam - 4 )."-".substr( $TEL, -4 );
            }
        }

    }
