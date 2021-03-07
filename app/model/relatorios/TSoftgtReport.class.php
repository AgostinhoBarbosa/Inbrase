<?php

    use Adianti\Wrapper\AdiantiPDFDesigner;

    class TSoftgtReport extends AdiantiPDFDesigner
    {
        protected $logo;
        protected $titulo;
        protected $nomerel;
        protected $orientacao = "P";
        protected $livro;
        protected $angle      = 0;
        protected $marca      = FALSE;
        protected $rodape     = '';

        protected $NewPageGroup;
        protected $PageGroups;
        protected $CurrPageGroup;

        public function __construct( $orientation = 'P', $format = 'a4', $unit = 'pt' )
        {
            parent::__construct( $orientation, $format, $unit );
            define( 'FPDF_FONTPATH', 'app/lib/pdf/font/' );
            $this->setFont( 'helvetica' );
            $this->SetLeftMargin( 30 );
            $this->SetRightMargin( 30 );
            $this->AliasNbPages();
            $this->orientacao = $orientation;

        }

        public function set_logo( $logo )
        {
            $this->logo = "app/images/" . $logo;
        }

        public function set_titulo( $titulo )
        {
            $this->titulo = utf8_decode( $titulo );
        }

        public function set_rodape( $value )
        {
            $this->rodape = utf8_decode( $value );
        }

        public function set_nomerel( $nomerel )
        {
            $this->nomerel = utf8_decode( $nomerel );
        }

        public function set_orientacao( $value )
        {
            $this->orientacao = $value;
        }

        public function set_livro( $value )
        {
            $this->livro = $value;
        }

        public function set_marca( $value )
        {
            $this->marca = $value;
        }

        function RotatedImage( $file, $x, $y, $w, $h, $angle )
        {
            $this->Rotate( $angle, $x, $y );
            $this->Image( $file, $x, $y, $w, $h );
            $this->Rotate( 0 );
        }

        public function Footer()
        {
            $this->SetY( -15 );
            $this->SetFont( 'helvetica', 'I', 8 );
            if ( $this->livro ) {
                $this->Cell( 0, 10, $this->rodape, 'T', 0, 'C' );
            } else {
                $data = strftime( "%d/%m/%Y as %T" );
                $this->Cell( 300, 10, "Gerado em: " . utf8_decode( $data ), 'T', 0, 'C' );
                if ( $this->NewPageGroup ) {
                    $this->Cell( 402, 10, utf8_decode( 'PÁGINA ' ) . $this->GroupPageNo() . ' de ' . $this->PageGroupAlias(), 'T', 0, 'C' );
                } else {
                    $this->Cell( 402, 10, utf8_decode( 'PÁGINA ' ) . $this->PageNo() . ' de ' . '{nb}', 'T', 0, 'C' );
                }
            }
            $this->SetXY( -10, -5 );
        }

        public function Header()
        {
            if ( $this->orientacao == "L" ) {
                $this->SetY( 20 );
                $this->SetFont( 'Arial', 'B', 16 );
                $this->Image( $this->logo, 6, 1, 0, 57 );
                $this->Ln( 8 );
            } else {
                $this->SetFont( 'Arial', 'B', 12 );
                $this->Image( $this->logo, 40, 20, 0, 100 );
                $this->Ln( 100 );
            }

            if ( $this->marca ) {
                $this->SetFont( 'Arial', 'B', 80 );
                $this->SetTextColor( 245, 245, 245 );
                $this->RotatedText( 90, 700, MARCADAGUA, 50 );
            }

            if (!empty($this->nomerel)){
                $this->Ln( 20 );
                $this->Cell( 0, 10, $this->nomerel, '', 0, 'C' );
            }
            if (!empty($this->titulo)){
                $this->Ln( 10 );
                $this->Cell( 0, 10, $this->titulo, 'B', 0, 'C' );
                $this->Ln( 10 );
            }
        }

        function _beginpage( $orientation, $format, $rotation )
        {
            parent::_beginpage( $orientation, $format, $rotation );
            if ( $this->NewPageGroup ) {
                $n                          = sizeof( (array) $this->PageGroups );
                $alias                      = "{nb$n}";
                $this->PageGroups[ $alias ] = 1;
                $this->CurrPageGroup        = $alias;
                $this->NewPageGroup         = FALSE;
            } elseif ( $this->CurrPageGroup )
                $this->PageGroups[ $this->CurrPageGroup ]++;
        }

        function _endpage()
        {
            if ( $this->angle != 0 ) {
                $this->angle = 0;
                $this->_out( 'Q' );
            }
            parent::_endpage();
        }

        function _putpages()
        {
            $nb = $this->page;
            if ( ! empty( $this->PageGroups ) ) {
                foreach ( $this->PageGroups as $k => $v ) {
                    for ( $n = 1; $n <= $nb; $n++ ) {
                        $this->pages[ $n ] = str_replace( $k, $v, $this->pages[ $n ] );
                    }
                }
            }
            parent::_putpages();
        }

        function RotatedText( $x, $y, $txt, $angle )
        {
            $this->Rotate( $angle, $x, $y );
            $this->Text( $x, $y, $txt );
            $this->Rotate( 0 );
        }

        function Rotate( $angle, $x = -1, $y = -1 )
        {
            if ( $x == -1 )
                $x = $this->x;
            if ( $y == -1 )
                $y = $this->y;
            if ( $this->angle != 0 )
                $this->_out( 'Q' );
            $this->angle = $angle;
            if ( $angle != 0 ) {
                $angle *= M_PI / 180;
                $c     = cos( $angle );
                $s     = sin( $angle );
                $cx    = $x * $this->k;
                $cy    = ( $this->h - $y ) * $this->k;
                $this->_out( sprintf( 'q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy ) );
            }
        }

        function GroupPageNo()
        {
            return $this->PageGroups[ $this->CurrPageGroup ];
        }

        function PageGroupAlias()
        {
            return $this->CurrPageGroup;
        }

        function StartPageGroup()
        {
            $this->NewPageGroup = TRUE;
        }
    }
