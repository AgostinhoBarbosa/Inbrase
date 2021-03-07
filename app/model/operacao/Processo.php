<?php
    
    use Adianti\Database\TRecord;
    
    class Processo extends TRecord
    {
        const TABLENAME  = 'processo';
        const PRIMARYKEY = 'id';
        const IDPOLICY   = 'serial';

        private $seguradoras;
        private $hstatus;
        private $processo_arq;
        private $comprovante;
        private $recibos;
        private $usuarios;
        private $liberadores;
        private $gestores;
        private $ocorrencias;
        private $status_atual;
        private $financeiro;
        private $tipo_servico;

        public function __construct( $id = NULL, $callObjectLoad = TRUE ) {
            parent::__construct( $id, $callObjectLoad );
            parent::addAttribute( 'id_vei' );
            parent::addAttribute( 'id_seg' );
            parent::addAttribute( 'representante' );
            parent::addAttribute( 'nome_segurado' );
            parent::addAttribute( 'uf' );
            parent::addAttribute( 'tipo' );
            parent::addAttribute( 'placa' );
            parent::addAttribute( 'chassi' );
            parent::addAttribute( 'marca_modelo' );
            parent::addAttribute( 'marca' );
            parent::addAttribute( 'ano' );
            parent::addAttribute( 'cor' );
            parent::addAttribute( 'motor' );
            parent::addAttribute( 'renavam' );
            parent::addAttribute( 'sinistro' );
            parent::addAttribute( 'apolice' );
            parent::addAttribute( 'combustivel' );
            parent::addAttribute( 'restricao' );
            parent::addAttribute( 'tipo_servico_dec' );
            parent::addAttribute( 'tipo_ocorrencia_dec' );
            parent::addAttribute( 'data_dec' );
            parent::addAttribute( 'bo_dec' );
            parent::addAttribute( 'cidade_dec' );
            parent::addAttribute( 'uf_dec' );
            parent::addAttribute( 'informante_dec' );
            parent::addAttribute( 'ddd_informante_dec' );
            parent::addAttribute( 'fone_informante_dec' );
            parent::addAttribute( 'dp_dec' );
            parent::addAttribute( 'cidade_rec' );
            parent::addAttribute( 'uf_rec' );
            parent::addAttribute( 'data_rec' );
            parent::addAttribute( 'bo_rec' );
            parent::addAttribute( 'dp_rec' );
            parent::addAttribute( 'cidade_dev' );
            parent::addAttribute( 'uf_dev' );
            parent::addAttribute( 'dp_dev' );
            parent::addAttribute( 'chassi_adulterado_dev' );
            parent::addAttribute( 'data_entrega_dev' );
            parent::addAttribute( 'bo_dev' );
            parent::addAttribute( 'telefone_dev' );
            parent::addAttribute( 'responsavel_dev' );
            parent::addAttribute( 'nota_dev' );
            parent::addAttribute( 'local_entrega_dev' );
            parent::addAttribute( 'obs_dev' );
            parent::addAttribute( 'data_cadastro' );
            parent::addAttribute( 'restricao02' );
            parent::addAttribute( 'restricao03' );
            parent::addAttribute( 'restricao04' );
            parent::addAttribute( 'municipio' );
            parent::addAttribute( 'obs_dec' );
            parent::addAttribute( 'obs_rec' );
            parent::addAttribute( 'condChassi' );
            parent::addAttribute( 'fone_informante_rec' );
            parent::addAttribute( 'ddd_informante_rec' );
            parent::addAttribute( 'responsavel_rec' );
            parent::addAttribute( 'condMotor' );
            parent::addAttribute( 'usuario' );
            parent::addAttribute( 'liberador' );
            parent::addAttribute( 'gestor' );
            parent::addAttribute( 'tipo_liberacao_dev' );
            parent::addAttribute( 'processo_origem' );
            parent::addAttribute( 'processo_reintegracao' );
            parent::addAttribute( 'placa_aplicada' );
        }

        public function get_recibos() {
            if ( empty( $this->recibos ) ) {
                $this->recibos = Recibos::where( 'processo_id', '=', $this->id )->load();
                if ( empty( $this->recibos ) ) {
                    $this->recibos[] = new Recibos();
                }
            }
            return $this->recibos;
        }

        public function get_comprovante() {
            if ( empty( $this->comprovante ) ) {
                $this->comprovante = Comprovante::where( 'id_processo', '=', $this->id )->load();
                if ( empty( $this->comprovante ) ) {
                    $this->comprovante[] = new Comprovante();
                }
            }
            return $this->comprovante;
        }

        public function get_tipo_servico() {
            if ( empty( $this->tipo_servico ) ) {
                $this->tipo_servico = Tiposervico::find( $this->tipo_servico_dec );
                if ( empty( $this->tipo_servico ) ) {
                    $this->tipo_servico = new Tiposervico();
                }
            }
            return $this->tipo_servico;
        }

        public function get_seguradoras() {
            if ( empty( $this->seguradoras ) ) {
                $this->seguradoras = Pessoa::find( $this->id_seg );
                if ( empty( $this->seguradoras ) ) {
                    $this->seguradoras       = new Pessoa();
                    $this->seguradoras->nome = "<p style='color:#ff0000;font-weight:bold;'>NÂO INFORMADO</p>";
                }
            }
            return $this->seguradoras;
        }

        public function get_ocorrencias() {
            if ( empty( $this->ocorrencias ) ) {
                $this->ocorrencias = Prococor::where( 'id_processo', '=', $this->id )->load();
                if ( empty( $this->ocorrencias ) ) {
                    $this->ocorrencias[] = new Prococor();
                }
            }
            return $this->ocorrencias;
        }

        public function get_financeiro() {
            if ( empty( $this->financeiro ) ) {
                $this->financeiro = viewFinanceiroProcesso::where( 'processo_id', '=', $this->id )->load();
                if ( empty( $this->financeiro ) ) {
                    $this->financeiro[] = new viewFinanceiroProcesso();
                }
            }
            return $this->financeiro;
        }

        public function get_hstatus() {
            if ( empty( $this->hstatus ) ) {
                $this->hstatus = Hstatus::where( 'id_processo', '=', $this->id )->orderBy( 'data_cadastro', 'desc' )->load();
                if ( empty( $this->hstatus ) ) {
                    $this->hstatus[] = new Hstatus();
                }
            }
            return $this->hstatus;
        }

        public function get_status_atual() {
            if ( empty( $this->status_atual ) ) {
                $this->status_atual = Hstatus::where( 'id_processo', '=', $this->id )->orderBy( 'data_cadastro', 'desc' )->first();
                if ( $this->status_atual ) {
                    $this->status_atual = $this->status_atual->id_status;
                }
            }
            return $this->status_atual;
        }
    
        public function valida_status_final( )
        {
            $status = $this->get_status_atual();
            if ( $status ) {
                $status = Status::find($status);
                if ($status){
                    return $status->status_final == 1;
                }
            }
            return false;
            
        }

        public function onValidaStatus( $value ) {
            return Hstatus::where( 'id_processo', '=', $this->id )->where( 'id_status', '=', $value )->count();
        }

        public function get_processo_arq() {
            if ( empty( $this->processo_arq ) ) {
                $this->processo_arq = ProcessoArq::where( 'id_processo', '=', $this->id )->orderBy( 'id_arq', 'asc' )->load();
                if ( empty( $this->processo_arq ) ) {
                    $this->processo_arq[] = new ProcessoArq();
                }
            }
            return $this->processo_arq;
        }

        public function get_usuarios() {
            if ( empty( $this->usuarios ) ) {
                TTransaction::open( 'permission' );
                $this->usuarios = SystemUser::find( $this->usuario );
                if ( empty( $this->usuarios ) ) {
                    $this->usuarios = new SystemUser();
                }
                TTransaction::close();
            }

            return $this->usuarios;
        }

        public function get_liberadores() {
            if ( empty( $this->liberadores ) ) {
                $this->liberadores = Pessoa::find( $this->liberador );
                if ( empty( $this->liberadores ) ) {
                    $this->liberadores = new Pessoa();
                }
            }

            return $this->liberadores;
        }

        public function get_gestores() {
            if ( empty( $this->gestores ) ) {
                TTransaction::open( 'permission' );
                $this->gestores = SystemUser::find( $this->gestor );
                if ( empty( $this->gestores ) ) {
                    $this->gestores = new SystemUser();
                }
                TTransaction::close();
            }

            return $this->gestores;
        }

    }

