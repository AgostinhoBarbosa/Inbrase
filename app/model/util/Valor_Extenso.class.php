<?php

    class Valor_Extenso
    {
        const MIN      = 0.01;
        const MAX      = 2147483647.99;
        const MOEDA    = " real ";
        const MOEDAS   = " reais ";
        const CENTAVO  = " centavo ";
        const CENTAVOS = " centavos ";
        private static $unidades      = ["um", "dois", "três", "quatro", "cinco", "seis", "sete", "oito", "nove",
                                         "dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis",
                                         "dezessete", "dezoito", "dezenove"];
        private static $dezenas       = ["dez", "vinte", "trinta", "quarenta", "cinqüenta", "sessenta", "setenta",
                                         "oitenta", "noventa"];
        private static $centenas      = ["cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos",
                                         "setecentos", "oitocentos", "novecentos"];
        private static $milhares      = [["text" => "mil", "start" => 1000, "end" => 999999, "div" => 1000],
                                         ["text" => "milhão", "start" => 1000000, "end" => 1999999,
                                          "div"  => 1000000],
                                         ["text" => "milhões", "start" => 2000000, "end" => 999999999,
                                          "div"  => 1000000],
                                         ["text" => "bilhão", "start" => 1000000000, "end" => 1999999999,
                                          "div"  => 1000000000],
                                         ["text" => "bilhões", "start" => 2000000000, "end" => 2147483647,
                                          "div"  => 1000000000]];
        private static $unidades_data = ["primeiro", "segundo", "terceiro", "quarto", "quinto", "sexto", "sétimo",
                                         "oitavo", "nono", "decimo"];
        private static $dezenas_data  = ["décimo", "vigésimo", "trigésimo", "quadragésimo", "quinquagésimo",
                                         "sexagésimo", "septuagésimo", "octogésimo", "nonagésimo"];
        private static $centenas_data = ["centésimo", "ducentésimo", "tricentésimo", "quadringentésimo",
                                         "quingentésimo", "sexcentésimo", "septingentésimo", "octingentésimo",
                                         "noningentésimo"];
        private static $milhares_data = ["milésimo"];

        static function numberToExt( $number, $moeda = TRUE )
        {
            if ( $number >= self::MIN && $number <= self::MAX ) {
                $value = self::conversionR( (int) $number );
                if ( $moeda ) {
                    if ( floor( $number ) == 1 ) {
                        $value .= self::MOEDA;
                    } else {
                        if ( floor( $number ) > 1 ) {
                            $value .= self::MOEDAS;
                        }
                    }
                }

                $decimals = self::extractDecimals( $number );

                if ( $decimals > 0.00 ) {
                    $decimals = round( $decimals * 100 );
                    $value    .= " e " . self::conversionR( $decimals );
                    if ( $moeda ) {
                        if ( $decimals == 1 ) {
                            $value .= self::CENTAVO;
                        } else {
                            if ( $decimals > 1 ) {
                                $value .= self::CENTAVOS;
                            }
                        }
                    }
                }
            }

            return trim( $value );
        }

        static function conversionR( $number )
        {
            if ( in_array( $number, range( 1, 19 ) ) ) {
                $value = self::$unidades[ $number - 1 ] . " ";
            } else {
                if ( in_array( $number, range( 20, 90, 10 ) ) ) {
                    $value = self::$dezenas[ floor( $number / 10 ) - 1 ] . " ";
                } else {
                    if ( in_array( $number, range( 21, 99 ) ) ) {
                        $value = self::$dezenas[ floor( $number / 10 ) - 1 ] . " e " . self::conversionR( $number % 10 );
                    } else {
                        if ( in_array( $number, range( 100, 900, 100 ) ) ) {
                            $value = self::$centenas[ floor( $number / 100 ) - 1 ] . " ";
                        } else {
                            if ( in_array( $number, range( 101, 199 ) ) ) {
                                $value = ' cento e ' . self::conversionR( $number % 100 );
                            } else {
                                if ( in_array( $number, range( 201, 999 ) ) ) {
                                    $value = self::$centenas[ floor( $number / 100 ) - 1 ] . " e " . self::conversionR( $number % 100 );
                                } else {
                                    foreach ( self::$milhares as $item ) {
                                        if ( $number >= $item[ 'start' ] && $number <= $item[ 'end' ] ) {
                                            $value = self::conversionR( floor( $number / $item[ 'div' ] ) ) . " " . $item[ 'text' ] . " e " . self::conversionR( $number % $item[ 'div' ] );
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return $value;
        }

        private static function extractDecimals( $number )
        {
            return $number - floor( $number );
        }

        static function dataToExt( $number )
        {
            if ( $number >= self::MIN && $number <= self::MAX ) {
                $value = self::conversionD( (int) $number );
            }

            return trim( $value );
        }

        static function conversionD( $number )
        {
            if ( in_array( $number, range( 1, 10 ) ) ) {
                $value = self::$unidades_data[ $number - 1 ];
            } else {
                if ( in_array( $number, range( 10, 90, 10 ) ) ) {
                    $value = self::$dezenas_data[ floor( $number / 10 ) - 1 ] . " ";
                } else {
                    if ( in_array( $number, range( 11, 99 ) ) ) {
                        $value = self::$dezenas_data[ floor( $number / 10 ) - 1 ] . " " . self::conversionD( $number % 10 );
                    } else {
                        if ( in_array( $number, range( 100, 900, 100 ) ) ) {
                            $value = self::$centenas_data[ floor( $number / 100 ) - 1 ] . " ";
                        } else {
                            if ( in_array( $number, range( 101, 199 ) ) ) {
                                $value = ' cento e ' . self::conversionR( $number % 100 );
                            } else {
                                if ( in_array( $number, range( 201, 999 ) ) ) {
                                    $value = self::$centenas[ floor( $number / 100 ) - 1 ] . " " . self::conversionD( $number % 100 );
                                } else {
                                    foreach ( self::$milhares as $item ) {
                                        if ( $number >= $item[ 'start' ] && $number <= $item[ 'end' ] ) {
                                            $value = self::conversionR( floor( $number / $item[ 'div' ] ) ) . " " . $item[ 'text' ] . " " . self::conversionD( $number % $item[ 'div' ] );
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return $value;
        }
    }
