<?php

    use Adianti\Database\TCriteria;
    use Adianti\Database\TRepository;
    use Adianti\Database\TTransaction;
    use Adianti\Log\TLoggerSTD;

    class PendenciaService
    {
        public static function onPendencias()
        {
            try {
                ini_set( 'max_execution_time', 0 );
                TTransaction::open( 'afincco' );
                
                $criteria = new TCriteria();
                $criteria->setProperty( 'order', 'usuario' );
                $criteria->setProperty( 'direction', 'ASC' );
                $filter = new TFilter('id_status', '>', '0');
                $criteria->add($filter);

                $repository = new TRepository( 'viewPendencias');
                $objects    = $repository->load($criteria);

                if ( $objects )
                {
                    foreach ( $objects as $object ) {
                        if ($object->id_status > 0) {
                            emailPendencias::enviar( $object );
                        }
                    }
                }

                TTransaction::close();

            } catch ( Exception $e )
            {
                echo $e->getMessage();
                TTransaction::rollback();
            }

        }
    }
