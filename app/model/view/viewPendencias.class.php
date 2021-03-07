<?php
    
    class viewPendencias extends TRecord
    {
        const TABLENAME  = 'view_pendencias';
        const PRIMARYKEY = 'id';
        const IDPOLICY   = 'serial';
        
        private $user;
        private $pessoa;
        private $status;
        private $seguradora;
        private $processo;
        
        public function __construct( $id = NULL, $callObjectLoad = TRUE )
        {
            parent::__construct( $id, $callObjectLoad );
            parent::addAttribute( 'placa' );
            parent::addAttribute( 'chassi' );
            parent::addAttribute( 'motor' );
            parent::addAttribute( 'sinistro' );
            parent::addAttribute( 'marca_modelo' );
            parent::addAttribute( 'cor' );
            parent::addAttribute( 'ano' );
            parent::addAttribute( 'nome' );
            parent::addAttribute( 'statu' );
            parent::addAttribute( 'data_cadastro' );
            parent::addAttribute( 'prazo' );
            parent::addAttribute( 'representante' );
            parent::addAttribute( 'liberador' );
            parent::addAttribute( 'usuario' );
            parent::addAttribute( 'id_status' );
            parent::addAttribute( 'id_seg' );
        }
        
        public function get_email_cobranca()
        {
            if ( empty( $this->liberador ) ) {
                return $this->get_user()->email;
            }
            
            return (empty($this->get_pessoa()->email) ? 'despesas@afincco.com.br' : $this->get_pessoa()->email);
        }
        
        public function get_processo()
        {
            if ( empty( $this->processo ) ) {
                $this->processo = Processo::find( $this->id );
            }
            return $this->processo;
            
        }
        
        public function get_user()
        {
            if ( empty( $this->user ) ) {
                try {
                    TTransaction::open( 'permission' );
                    $this->user = SystemUser::find( $this->usuario );
                    TTransaction::close();
                } catch ( Exception $e ) {
                    TTransaction::rollback();
                }
            }
            return $this->user;
            
        }
        
        public function get_pessoa()
        {
            if ( empty( $this->pessoa ) ) {
                $this->pessoa = Pessoa::find( $this->liberador );
            }
            return $this->pessoa;
            
        }
        
        public function get_seguradora()
        {
            if ( empty( $this->seguradora ) ) {
                $this->seguradora = Seguradoras::find( $this->id_seg );
            }
            return $this->seguradora;
            
        }
        
        public function get_status()
        {
            if ( empty( $this->status ) ) {
                $this->status = Status::find( $this->id_status );
            }
            return $this->status;
            
        }
        
        public function get_nome_cobranca()
        {
            switch ( $this->id_status ) {
                case 7:
                case 8:
                case 25:
                case 29:
                case 30:
                case 32:
                    return 'Departamento Juridico';
                    break;
                case 12:
                case 13:
                case 14:
                case 15:
                    return 'Departamento Despesas';
                    break;
                case 16:
                case 17:
                case 18:
                case 24:
                    return 'Departamento Administrativo';
                    break;
                case 36:
                    return 'Processo Anterior Departamento Administrativo';
                    break;
            }
            
            if ( empty( $this->liberador ) ) {
                return $this->get_user()->nome;
            }
            return $this->get_pessoa()->nome;
            
        }
        
    }
