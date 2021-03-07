<?php
//======================================================================================================//
//Classe de Mapeamento ObjetoxBanco de Dados - OOP
//Autor: Agostinho Francisco Babosa
//Banco de Dados: MariaDB
//Drive de Acesso: PDO
//Data da Geração: 22/05/2017 11:40:37
//======================================================================================================//
    class Comprovante extends TRecord
    {
        const TABLENAME  = 'comprovante';
        const PRIMARYKEY = 'id';
        const IDPOLICY   = 'serial';

        private $pessoa;
        private $processo;
        private $despesa;
        private $arquivo;

        public function __construct( $id = NULL, $callObjectLoad = TRUE ) {
            parent::__construct( $id, $callObjectLoad );
            parent::addAttribute( 'id_processo' );
            parent::addAttribute( 'id_seg' );
            parent::addAttribute( 'PlacaVeiculo' );
            parent::addAttribute( 'ValorTotal' );
            parent::addAttribute( 'Status' );
            parent::addAttribute( 'Data_processo' );
            parent::addAttribute( 'Data_Atualizao' );
        }

        public function get_processo() {
            if ( empty( $this->processo ) )
                $this->processo = new Processo( $this->id_processo );

            return $this->processo;
        }

        public function get_pessoa() {
            if ( empty( $this->pessoa ) ) {
                $this->pessoa = Pessoa::find( $this->id_seg );
                if ( empty( $this->pessoa ) ) {
                    $this->pessoa = new pessoa();
                }
            }

            return $this->pessoa;
        }

        public function get_despesa() {
            if ( empty( $this->despesa ) ) {
                $retorno = Despesa::where( 'id_comprovante', '=', $this->id )->load();
                if ( $retorno ) {
                    $this->despesa = $retorno;
                } else {
                    $this->despesa = new Despesa();
                }
            }
            return $this->despesa;
        }

        public function get_arquivo() {
            if ( empty( $this->arquivo ) ) {
                $this->arquivo = ProcessoArq::where( 'id_processo', '=', $this->id_processo )->where( 'tipoarq_id', '=', 2 )->first();
            }
            return $this->arquivo;
        }

        public function onVerificaTitulo() {
            $count = Titulo::where( 'numero', '=', $this->id )->where( 'tipolancamento_id', 'IN', [36, 91, 94] )->count();

            if ( $count > 0 ) {
                return TRUE;
            } else {
                return FALSE;
            }
        }

        public function onVerificaStatus() {
            $count = Hstatus::where( 'id_processo', '=', $this->id_processo )->where( 'id_status', '=', 14 )->count();

            if ( $count > 0 ) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
    
    }
