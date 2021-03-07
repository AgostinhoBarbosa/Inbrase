<?php
    
    use Adianti\Database\TRecord;
    use Adianti\Database\TTransaction;
    
    class MovimentoTitulo extends TRecord
    {
        const TABLENAME  = 'movimentotitulo';
        const PRIMARYKEY = 'id';
        const IDPOLICY   = 'serial';

        private $tipolancamento;
        private $titulo;
        private $cheque;
        private $caixa;

        public function __construct( $id = NULL, $callObjectLoad = TRUE ) {
            parent::__construct( $id, $callObjectLoad );
            parent::addAttribute( 'id' );
            parent::addAttribute( 'data_movimento' );
            parent::addAttribute( 'titulo_id' );
            parent::addAttribute( 'caixa_id' );
            parent::addAttribute( 'cheque_id' );
            parent::addAttribute( 'tipolancamento_id' );
            parent::addAttribute( 'dc' );
            parent::addAttribute( 'valor' );
            parent::addAttribute( 'processo_id' );
            parent::addAttribute( 'usuario' );
            parent::addAttribute( 'observacao' );
        }
    
        public function onBeforeStore($object)
        {
            if ($object->valor == 0.00){
                throw new Exception('Valor não pode ser zero');
            }
        }
        
        public function onAtualizaCaixa( $object ) {
            $conn = TTransaction::get();

            $query       = "select dc, sum(valor) as valor from movimentotitulo where caixa_id = ".$object->caixa_id;
            $valor_caixa = $this->get_caixa()->valor;
            $debito      = 0;
            $credito     = 0;

            $result = $conn->query( $query );

            foreach ( $result as $row ) {
                if ( $row[ 'dc' ] == 'C' ) {
                    $credito = $row[ 'valor' ];
                }
                if ( $row[ 'dc' ] == 'D' ) {
                    $debito = $row[ 'valor' ];
                }
            }

            $this->caixa->saldo = $valor_caixa + ( $credito - $debito );
            $this->caixa->store();

            return TRUE;
        }

        public function onAtualizaTitulo( $object ) {
            $conn = TTransaction::get();

            $query        = "select dc, sum(valor) as valor from movimentotitulo where titulo_id = ".$object->titulo_id;
            $valor_titulo = $this->get_titulo()->valor;
            $debito       = 0;
            $credito      = 0;

            $result = $conn->query( $query );

            foreach ( $result as $row ) {
                if ( $row[ 'dc' ] == 'C' ) {
                    $credito = $row[ 'valor' ];
                }
                if ( $row[ 'dc' ] == 'D' ) {
                    $debito = $row[ 'valor' ];
                }
            }
            $this->titulo->saldo = $valor_titulo + ( $credito - $debito );
            $this->titulo->store();

            return TRUE;
        }

        public function get_caixa() {
            if ( empty( $this->caixa ) ) {
                $this->caixa = new Caixa( $this->caixa_id );
            }

            return $this->caixa;
        }

        public function get_titulo() {
            if ( empty( $this->titulo ) ) {
                $this->titulo = new Titulo( $this->titulo_id );
            }

            return $this->titulo;
        }

        public function get_tipolancamento() {
            if ( empty( $this->tipolancamento ) ) {
                $this->tipolancamento = new TipoLancamento( $this->tipolancamento_id );
            }

            return $this->tipolancamento;
        }

        public function get_cheque() {
            if ( empty( $this->cheque ) ) {
                $this->cheque = new Cheque( $this->cheque_id );
            }

            return $this->cheque;
        }

        public function get_saldo_titulo( $titulo ) {
            $objects = MovimentoTitulo::where( 'titulo_id', '=', $titulo )->load();
            $saldo   = 0.00;
            foreach ( $objects as $object ) {
                if ( $object->dc == 'C' ) {
                    $saldo += (float)$object->valor;
                }
                if ( $object->dc == 'D' ) {
                    $saldo -= (float)$object->valor;
                }
            }
            return $saldo;
        }
    }
