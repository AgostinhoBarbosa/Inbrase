<?php

    class TempoSessao extends TElement
    {
        public function __construct( $param )
        {
            parent::__construct( 'span' );
            $this->{'class'} = 'tempo_sessao';

            try {
                $tempo = Utilidades::onDataAtual();

                if ( ! empty( $param[ 'theme' ] ) ) {
                    parent::add( $tempo );
                }

            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::close();
            }
        }
    }

