<?php

    use Adianti\Database\TTransaction;

    class LimpezaService
    {
        public static function onExportar()
        {
            try {
                ini_set( 'max_execution_time', 0 );
                TTransaction::open( 'operacao' );
                //TTransaction::setLogger(new TLoggerSTD());
                $repository = new TRepository( 'Processo' );
                $objects    = $repository->load();

                if ( $objects ) {
                    foreach ( $objects as $object ) {
                        switch ( $object->get_status_atual() ){
                            case 32:
                            case 35:
                            case 40: $verifica = true; break;
                            default: $verifica = false; break;
                        }
                        if ( $verifica ) {
                            $ret_arq                  = TRUE;
                            $ret_comprovante          = TRUE;
                            $ret_despesa              = TRUE;
                            $ret_status               = TRUE;
                            $processo_ant             = $object->id;
                            $object                   = self::onRepassaUsuario($object, 'U'); //Usuario
                            $object                   = self::onRepassaUsuario($object, 'G'); //Gestor
                            $object->tipo_servico_dec = self::onTipoServico( $object->tipo_servico_dec );
                            $processo_atu             = self::onExport( 'Processo', $object );
                            if ( $processo_atu > 0 ) {
                                $fp1 = fopen('arqdel.txt', 'a+');
                                $comando = "delete from processoa where id =  ".$processo_ant." \n";
                                fwrite($fp1, $comando);
                                fclose($fp1);

                                $proc_arqs = ProcessoArq::where( 'id_processo', '=', $processo_ant )->load();
                                $fp = fopen('arqmov.txt', 'a+');

                                foreach ( $proc_arqs as $obj ) {
                                    $obj->id_arq      = NULL;
                                    $obj->id_processo = $processo_atu;
                                    $ret_arq          = self::onExport( 'ProcessoArq', $obj );
                                    if ( $ret_arq ) {
                                        $target_file = 'app/arquivos/' . $processo_atu . "/";
                                        $source_file = '/var/www/sistema18.afincco.com.br/web/app/arquivos/' . $processo_ant.'/';
                                        $comando = "mv ".$source_file." ".$target_file." \n";
                                        fwrite($fp, $comando);
                                    }
                                }
                                fclose($fp);

                                if ( $ret_arq ) {
                                    $proc_status = hstatus::where( 'id_processo', '=', $processo_ant )->orderBy( 'data_cadastro','desc' )->take( 1 )->load();
                                    foreach ( $proc_status as $status ) {
                                        if ( empty( $status->representante ) ) {
                                            $status->representante = 'Não Informado';
                                        }
                                        $status->id_processo = $processo_atu;

                                        if ($status->id_status == 32){
                                            $status->id_status = 5;
                                        }
                                        if ($status->id_status == 35){
                                            $status->id_status = 6;
                                        }
                                        if ($status->id_status == 40){
                                            $status->id_status = 7;
                                        }

                                        $ret_status          = self::onExport( 'Hstatus', $status );
                                    }
                                }
                                if ( $ret_arq and $ret_status ) {
                                    $proc_comprovantes = comprovante::where( 'id_processo', '=', $processo_ant )->load();
                                    foreach ( $proc_comprovantes as $obj ) {
                                        if ( empty( $obj->PlacaVeiculo ) ) {
                                            $obj->PlacaVeiculo = ' ';
                                        }

                                        $obj->id_processo = $processo_atu;
                                        $comprovante_ant  = $obj->IdComprovante;
                                        $ret_comprovante  = self::onExport( 'Comprovante', $obj );
                                        if ( !$ret_comprovante ) {
                                            break;
                                        } else {
                                            $despesas = despesa::where( 'IdComprovante', '=', $comprovante_ant )->load();
                                            foreach ( $despesas as $despesa ) {
                                                $despesa->IdComprovante = $ret_comprovante;
                                                self::onExport( 'Despesa', $despesa );
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                TTransaction::close();
            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();
            }

        }

        public static function onRepassaUsuario( Processo $object, $tipo = 'U')
        {
            if ($tipo === 'U') {
                switch ( $object->usuario ) {
                    case 11:
                        $object->usuario = 10;
                        break;
                    case 14:
                        $object->usuario = 5;
                        break;
                    case 15:
                        $object->usuario = 4;
                        break;
                    case 17:
                        $object->usuario = 11;
                        break;
                    case 18:
                        $object->usuario = 12;
                        break;
                    case 19:
                        $object->usuario = 13;
                        break;
                    case 20:
                        $object->usuario = 3;
                        break;
                    case 23:
                        $object->usuario = 8;
                        break;
                    case 27:
                        $object->usuario = 14;
                        break;
                    case 29:
                        $object->usuario = 23;
                        break;
                    case 31:
                        $object->usuario = 6;
                        break;
                    case 51:
                        $object->usuario = 17;
                        break;
                    default:
                        $object->usuario = 2;
                        break;
                }
            }
            if ($tipo === 'G') {
                switch ( $object->gestor ) {
                    case 11:
                        $object->gestor = 10;
                        break;
                    case 14:
                        $object->gestor = 5;
                        break;
                    case 15:
                        $object->gestor = 4;
                        break;
                    case 17:
                        $object->gestor = 11;
                        break;
                    case 18:
                        $object->gestor = 12;
                        break;
                    case 19:
                        $object->gestor = 13;
                        break;
                    case 20:
                        $object->gestor = 3;
                        break;
                    case 23:
                        $object->gestor = 8;
                        break;
                    case 27:
                        $object->gestor = 14;
                        break;
                    case 29:
                        $object->gestor = 23;
                        break;
                    case 31:
                        $object->gestor = 6;
                        break;
                    case 51:
                        $object->gestor = 17;
                        break;
                    default:
                        $object->gestor = 2;
                        break;
                }
            }

            return $object;
        }

        public static function onUsuario()
        {
            try {
                ini_set( 'max_execution_time', 0 );
                TTransaction::open( 'operacao' );

                $usu_sai = 14;
                $usu_entra = 5;
                $objects = Processo::where( 'usuario', '=', $usu_sai)->load();
                if ( $objects ) {
                    foreach ( $objects as $object ) {
                        self::onSalvaUsuario($object, $usu_sai, $usu_entra);
                    }
                }
                TTransaction::close();
            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();
            }
        }

        public static function onSalvaUsuario( $processo, $usu_sai, $usu_entra )
        {
            try {
                TTransaction::open( 'afincco' );
                $retorno = Processo::where( 'placa', '=', $processo->placa)
                                   ->where('sinistro', '=', $processo->sinistro)
                                   ->where('usuario', '=', $usu_sai)->load();
                if ($retorno){
                    foreach ($retorno as $ret){
                        $ret->usuario = $usu_entra;
                        $ret->store();
                    }
                }
                TTransaction::close();
                return TRUE;
            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();

                return FALSE;
            }
        }

        public static function onTipoServico( $tipo )
        {
            try {
                TTransaction::open( 'afincco' );
                $conn = TTransaction::get();

                $sql = "select id from tiposervico where nome = '" . $tipo . "'";

                $sth     = $conn->query( $sql );
                $retorno = 1;
                foreach ( $sth as $row ) {
                    $retorno = $row[ 'id' ];
                    break;
                }
                TTransaction::close();

                return $retorno;
            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();
                return FALSE;
            }
        }

        public static function onExport( $table, $object )
        {
            try {
                TTransaction::open( 'afincco' );
                switch ( $table ) {
                    case 'ProcessoArq' :
                        $object->id_arq = NULL;
                        break;
                    case 'Comprovante' :
                        $object->IdComprovante = NULL;
                        break;
                    case 'Despesa'     :
                        $object->IdDespesa = NULL;
                        break;
                    default            :
                        $object->id = NULL;
                        break;
                }
                $obj = new $table;
                $obj->mergeObject( $object );
                $data = $obj->toArray();
                foreach ( $data as $key => $value ) {
                    if ( $value == NULL ) {
                        $data[ $key ] = '';
                    }
                }
                $obj->fromArray($data);

                if (isset($obj->NomeDespesa1)){
                    if (empty($obj->NomeDespesa1)){
                        $obj->NomeDespesa1 = '';
                    }
                }

                $obj->store();
                TTransaction::close();
                switch ( $table ) {
                    case 'ProcessoArq' :
                        $retorno = $obj->id_arq;
                        break;
                    case 'Comprovante' :
                        $retorno = $obj->IdComprovante;
                        break;
                    case 'Despesa'     :
                        $retorno = $obj->IdDespesa;
                        break;
                    default            :
                        $retorno = $obj->id;
                        break;
                }
                return $retorno;
            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();

                return FALSE;
            }
        }

        public static function onVerifica( $object )
        {
            try {
                TTransaction::open( 'afincco' );
                $obj = Processo::where( 'placa', '=', $object->placa )->where( 'id', '=', $object->id )->load();
                if ( $obj ) {
                    $retorno = TRUE;
                } else {
                    $retorno = FALSE;
                }
                TTransaction::close();

                return $retorno;
            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();

                return FALSE;
            }

        }

        public static function onNovoID( $tabela )
        {
            try {
                // open a transaction with database
                TTransaction::open( 'afincco' );
                $conn = TTransaction::get();

                $sql = "select (a.id -1) as id from " . $tabela . " a where a.id <> 1 and not exists (select id from " . $tabela . " b where b.id = (a.id-1) )";

                $sth     = $conn->query( $sql );
                $retorno = NULL;
                foreach ( $sth as $row ) {
                    $retorno = $row[ 'id' ];
                    break;
                }
                TTransaction::close();

                return $retorno;
            } catch ( Exception $e ) // in case of exception
            {
                // shows the exception error message
                new TMessage( 'error', $e->getMessage() );
                // undo all pending operations
                TTransaction::rollback();

                return FALSE;
            }
        }

        public static function onValidaRecibo()
        {
            try {
                // open a transaction with database
                TTransaction::open( 'afincco' );
                $recibos = comprovante::where( 'Status', '=', 'Ativo' )->load();
                foreach ( $recibos as $recibo ) {
                    $titulos = titulo::where( 'processo_id', '=', $recibo->id_processo )->where( 'valor', '=', $recibo->ValorTotal )->where( 'tipolancamento_id', '=', 36 )->load();
                    if ( $titulos ) {
                        $achou = 0;

                        foreach ( $titulos as $titulo ) {
                            if ( $achou === 0 ) {
                                if ( $titulo->valor !== $titulo->saldo ) {
                                    $achou          = $titulo->id;
                                    $titulo->numero = $recibo->IdComprovante;
                                    $titulo->store();
                                }
                            } else {
                                $titulo->delete();
                            }
                        }
                        if ( $achou === 0 ) {
                            foreach ( $titulos as $titulo ) {
                                if ( $achou === 0 ) {
                                    $achou          = $titulo->id;
                                    $titulo->numero = $recibo->IdComprovante;
                                    $titulo->store();
                                } else {
                                    $titulo->delete();
                                }
                            }
                        }
                    }

                }
                TTransaction::close();

                return TRUE;
            } catch ( Exception $e ) // in case of exception
            {
                // shows the exception error message
                new TMessage( 'error', $e->getMessage() );
                // undo all pending operations
                TTransaction::rollback();

                return FALSE;
            }

        }

        public static function onImportarRecibos() {
            try {
                set_time_limit( 0 );
                TTransaction::open( 'afincco' );

                $repository = new TRepository( 'comprovante' );
                $criteria   = new TCriteria;

                if ( empty( $param[ 'order' ] ) ) {
                    $param[ 'order' ]     = 'IdComprovante';
                    $param[ 'direction' ] = 'asc';
                }

                $objects = $repository->load();

                if ( $objects ) {
                    foreach ( $objects as $object ) {
                        if ($object->onVerificaStatus()) {
                            $observacao = $object->despesa->observacao;
                            $seguradora = $object->seguradora->nome;
                            $posicao    = stripos( $seguradora, 'CNPJ' );
                            if ( $posicao > 0 ) {
                                $posicao += 5;
                                $cnpj    = substr( $seguradora, $posicao );
                                $titulo  = titulo::where( 'numero', '=', $object->IdComprovante )
                                    ->where( 'tipolancamento_id', '=', 36 )->load();
                                if ( ! $titulo ) {
                                    $pessoa = pessoa::where( 'documento', '=', $cnpj )->load();
                                    if ( count( $pessoa ) > 0 ) {
                                        $titulo                    = new titulo();
                                        $titulo->numero            = $object->IdComprovante;
                                        $titulo->pessoa_id         = $pessoa[ 0 ]->id;
                                        $titulo->tipolancamento_id = 36;
                                        $titulo->data_entrada      = $object->Data_processo;
                                        $titulo->data_emissao      = $object->Data_processo;
                                        $data_entrada              = new DateTime( $object->Data_processo );
                                        $data_entrada->add( new DateInterval( 'P30D' ) );
                                        $titulo->data_vencimento = $data_entrada->format( 'Y-m-d' );
                                        $titulo->valor           = $object->ValorTotal;
                                        $titulo->saldo           = $object->ValorTotal;
                                        $titulo->parcela         = 1;
                                        $titulo->pagar_receber   = 'R';
                                        $titulo->processo_id     = $object->id_processo;
                                        $titulo->dc              = 'C';
                                        $titulo->observacao      = $observacao;
                                        $titulo->store();
                                    }
                                }else{
                                    $titulo                  = $titulo[0];
                                    $titulo->processo_id     = $object->id_processo;
                                    $titulo->store();
                                }
                            }
                        }
                    }
                }
                TTransaction::close();
            } catch ( Exception $e )
            {
                TTransaction::rollback();
            }
        }


    }
