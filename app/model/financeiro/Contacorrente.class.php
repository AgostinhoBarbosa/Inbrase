<?php

    use Adianti\Database\TTransaction;

    class Contacorrente extends TRecord
    {
        const TABLENAME  = 'contacorrente';
        const PRIMARYKEY = 'id';
        const IDPOLICY   = 'serial';

        private $banco;
        private $saldo;

        public function __construct( $id = NULL, $callObjectLoad = TRUE ) {
            parent::__construct( $id, $callObjectLoad );
            parent::addAttribute( 'id' );
            parent::addAttribute( 'banco_id' );
            parent::addAttribute( 'nome' );
            parent::addAttribute( 'agencia' );
            parent::addAttribute( 'numero' );
            parent::addAttribute( 'tipo' );
            parent::addAttribute( 'chave' );
            parent::addAttribute( 'debito' );
            parent::addAttribute( 'credito' );
            parent::addAttribute( 'ativo' );
            parent::addAttribute( 'data_fechamento' );
        }

        public function get_banco() {
            if ( empty( $this->banco ) )
                $this->banco = new Banco( $this->banco_id );

            return $this->banco;
        }

        public function get_saldo() {
            $this->saldo = $this->credito - $this->debito;

            return number_format( $this->saldo, 2, ',', '.' );
        }

        public function getcaixa() {
            $criteria = new TCriteria();
            $criteria->add( new TFilter( 'contacorrente_id', '=', $this->id ) );
            return caixa::getObjects( $criteria );
        }

        public function get_tipo_conta() {
            return Utilidades::tipo_conta()[ $this->tipo ];
        }

        public function get_tipo_ativo() {
            return Utilidades::sim_nao()[ $this->ativo ];
        }

        public function onAtualizaContaCorrente() {
            $conn = TTransaction::get();

            $query   = "select dc, sum(valor) as valor from caixa where contacorrente_id = ".$this->id." group by dc";
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
            $this->debito  = $debito;
            $this->credito = $credito;
            $this->store();

            return TRUE;
        }

    }
