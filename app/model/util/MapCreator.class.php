<?php

    //Exemplo de como usar
    //$MapCreator = new MapCreator(array("app/control/","app/model/"));
    //$MapCreator->getMap();

    class MapCreator
    {
        private $pastas  = [];
        private $classes = [];

        function __construct( array $pastas )
        {
            $this->pastas = $pastas;
        }

        public function getMap()
        {
            $this->getClassFromPath( $this->pastas );
            $content = "<?php\n";
            foreach ( $this->classes as $key => $classe ) {
                $content .= "AdiantiCoreLoader::setClassPath('{$classe->nome}','{$classe->caminho}');\n";
            }
            file_put_contents( "map.php", $content );
            highlight_string( $content );
        }

        private function getClassFromPath( array $paths )
        {
            foreach ( $paths as $key => $path ) {

                $files = scandir( $path );
                foreach ( $files as $key => $file ) {
                    if ( $file != "." && $file != ".." ) {
                        if ( is_file( $path . $file ) ) {
                            if ( end( explode( ".", $file ) ) == "php" ) {
                                $classe          = new stdClass();
                                $classe->nome    = explode( ".", $file )[ 0 ];
                                $classe->caminho = $path . $file;
                                $this->classes[] = $classe;
                            }
                        } else if ( is_dir( $path . $file ) ) {
                            $this->getClassFromPath( [$path . $file . "/"] );
                        }
                    }
                }
            }
        }
    }
