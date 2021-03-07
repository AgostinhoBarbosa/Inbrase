<?php

    use Adianti\Database\TTransaction;

    class Caixa extends TRecord
    {
        const TABLENAME  = 'caixa';
        const PRIMARYKEY = 'id';
        const IDPOLICY   = 'serial';

        private $contacorrente;
        private $tipolancamento;

        public function __construct( $id = NULL, $callObjectLoad = TRUE ) {
            parent::__construct( $id, $callObjectLoad );
            parent::addAttribute( 'contacorrente_id' );
            parent::addAttribute( 'tipolancamento_id' );
            parent::addAttribute( 'data_movimento' );
            parent::addAttribute( 'dc' );
            parent::addAttribute( 'valor' );
            parent::addAttribute( 'saldo' );
            parent::addAttribute( 'compensado' );
            parent::addAttribute( 'controle' );
            parent::addAttribute( 'historico' );
            parent::addAttribute( 'usuario' );
            parent::addAttribute( 'operacao_id' );
            parent::addAttribute( 'pessoa_id' );
        }

        public function get_tipolancamento() {
            if ( empty( $this->tipolancamento ) ) {
                $this->tipolancamento = new TipoLancamento( $this->tipolancamento_id );
            }

            return $this->tipolancamento;
        }

        public function get_saldo_anterior() {
            $repositorio = new TRepository( 'Caixa' );

            $criteria = new TCriteria();

            $param[ 'order' ]     = 'data_movimento, dc, id';
            $param[ 'direction' ] = 'asc';

            $criteria->setProperties( $param );

            if ( TSession::getValue( 'caixaList_filter_contacorrente_id' ) ) {
                $criteria->add( TSession::getValue( 'caixaList_filter_contacorrente_id' ) );
            }

            $objetos        = $repositorio->load( $criteria, FALSE );
            $saldo_anterior = 0.00;

            foreach ( $objetos as $objeto ) {
                if ( $objeto->id == $this->id ) {
                    break;
                }

                $valor = (double)$objeto->valor;

                if ( $objeto->dc == 'C' ) {
                    $saldo_anterior = $saldo_anterior + $valor;
                } else {
                    $saldo_anterior = $saldo_anterior - $valor;
                }
            }

            return $saldo_anterior;

        }

        public function onBeforeStore( $object ) {
            $object->usuario = TSession::getValue( 'login' );
            if ( $this->get_contacorrente()->data_fechamento ) {
                if ( $object->data_movimento <= $this->get_contacorrente()->data_fechamento ) {
                    new TMessage( 'error', 'Conta Corrente esta com data de fechamento menor ou igual a data do movimento' );

                    return FALSE;
                }
            }

            return $object;
        }

        public function get_contacorrente() {
            if ( empty( $this->contacorrente ) ) {
                $this->contacorrente = new Contacorrente( $this->contacorrente_id );
            }

            return $this->contacorrente;
        }

        public function onAfterStore( $object ) {
            $this->onAtualizaContaCorrente( $object );
        }

        public function onAtualizaContaCorrente( $object ) {
            try {
                $conn = TTransaction::get();

                $query   = "select dc, sum(valor) as valor from caixa where contacorrente_id = ".$object->contacorrente_id." group by dc";
                $debito  = 0;
                $credito = 0;

                $result = $conn->query( $query );

                foreach ( $result as $row ) {
                    if ( $row[ 'dc' ] == 'C' ) {
                        $credito = $row[ 'valor' ];
                    }
                    if ( $row[ 'dc' ] == 'D' ) {
                        $debito = $row[ 'valor' ];
                    }
                }
                $this->get_contacorrente()->debito  = $debito;
                $this->get_contacorrente()->credito = $credito;
                $this->get_contacorrente()->store();

                return TRUE;
            } catch ( Exception $e ) {
                throw new Exception ( "Erro ao atualizar saldo da conta corrente - ".$e->getMessage() );

            }
        }

        public function onAfterDelete( $object ) {
            $this->onAtualizaContaCorrente( $object );
        }
    }
