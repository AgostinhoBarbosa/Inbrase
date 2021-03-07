<?php

    class Pessoa extends TRecord
    {
        const TABLENAME  = 'pessoa';
        const PRIMARYKEY = 'id';
        const IDPOLICY   = 'serial';

        private $telefones;
        private $imagens;
        private $servicos;

        public function __construct( $id = NULL, $callObjectLoad = TRUE ) {
            parent::__construct( $id, $callObjectLoad );
            parent::addAttribute( 'tipo_pessoa' );
            parent::addAttribute( 'documento' );
            parent::addAttribute( 'rg_ie' );
            parent::addAttribute( 'nome' );
            parent::addAttribute( 'email' );
            parent::addAttribute( 'data_nascimento' );
            parent::addAttribute( 'rua' );
            parent::addAttribute( 'numero' );
            parent::addAttribute( 'complemento' );
            parent::addAttribute( 'bairro' );
            parent::addAttribute( 'cep' );
            parent::addAttribute( 'cidade' );
            parent::addAttribute( 'uf' );
            parent::addAttribute( 'agenda' );
            parent::addAttribute( 'observacao' );
            parent::addAttribute( 'contato' );
            parent::addAttribute( 'liberador' );
            parent::addAttribute( 'usuario' );
            parent::addAttribute( 'apelido' );
            parent::addAttribute( 'seguradora' );
        }

        public function get_servicos() {
            if ( empty( $this->servicos ) ) {
                $this->servicos = servicos::where( 'pessoa-id', '=', $this->id )->load();
            }
            return $this->servicos;
        }

        public function addTelefones( pessoa_fone $fone ) {
            $this->telefones[] = $fone;
        }

        public function get_telefones() {
            if ( empty( $this->telefones ) ) {
                $this->telefones = Pessoa_fone::where( 'fone_pessoa_id ', '=', $this->id )->load();
            }

            if ( !isset( $this->telefones ) ) {
                $this->LimparTelefones;
            }
            return $this->telefones;
        }

        public function addImagens( pessoa_imagens $imagem ) {
            $this->imagens[] = $imagem;
        }

        public function get_imagens() {
            if ( !isset( $this->imagens ) ) {
                $this->LimparImagens;
            }
            return $this->imagens;
        }

        public function LimparImagens() {
            $this->imagens = [];
        }

        public function get_endereco_completo( $cidade = TRUE ) {
            $retorno = $this->rua;
            if ( !empty( $this->numero ) ) {
                $retorno .= ", ".$this->numero;
            }
            if ( !empty( $this->complemento ) ) {
                $retorno .= ", ".$this->complemento;
            }
            if ( !empty( $this->bairro ) ) {
                $retorno .= ", ".$this->bairro;
            }
            if ( !empty( $this->cep ) ) {
                $retorno .= ", CEP ".$this->cep;
            }
            if ( $cidade ) {
                if ( !empty( $this->cidade ) ) {
                    $retorno .= ", ".$this->cidade;
                }
                if ( !empty( $this->uf ) ) {
                    $retorno .= "(".$this->uf.")";
                }
            }
            return $retorno;
        }

        public function delete( $id = NULL ) {
            $id = isset( $id ) ? $id : $this->id;
            pessoa_fone::where( 'fone_pessoa_id', '=', $id )->delete();
            pessoa_imagens::where( 'arquivo_pessoa_id', '=', $id )->delete();
            parent::delete( $id );
        }
    }
