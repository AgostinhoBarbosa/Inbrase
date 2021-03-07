<?php
    
    use Adianti\Base\TStandardForm;
    use Adianti\Control\TAction;
    use Adianti\Core\AdiantiCoreApplication;
    use Adianti\Database\TCriteria;
    use Adianti\Database\TFilter;
    use Adianti\Database\TTransaction;
    use Adianti\Registry\TSession;
    use Adianti\Validator\TRequiredValidator;
    use Adianti\Widget\Base\TScript;
    use Adianti\Widget\Datagrid\TDataGrid;
    use Adianti\Widget\Datagrid\TDataGridAction;
    use Adianti\Widget\Dialog\TInputDialog;
    use Adianti\Widget\Dialog\TMessage;
    use Adianti\Widget\Form\TButton;
    use Adianti\Widget\Form\TCombo;
    use Adianti\Widget\Form\TForm;
    use Adianti\Widget\Form\THtmlEditor;
    use Adianti\Widget\Wrapper\TDBCheckGroup;
    use Adianti\Widget\Wrapper\TDBCombo;
    use Adianti\Widget\Wrapper\TQuickForm;
    use Adianti\Wrapper\BootstrapDatagridWrapper;
    use Adianti\Wrapper\BootstrapFormBuilder;
    
    class processoaForm extends TStandardForm
    {
        protected $gridstatus;
        protected $gridArquivos;
        protected $gridComprovante;
        protected $gridOcorrencia;
        protected $gridRecibos;
        protected $gridFinanceiro;
        protected $frame;
        protected $documentos;
        protected $mostra_recibo;
        protected $mostra_recibo_prestador;
        protected $mostra_financeiro;
        
        function __construct()
        {
            parent::__construct();
            
            $this->setDatabase( 'afincco' );
            $this->setActiveRecord( 'Processo' );
            
            $this->form = new BootstrapFormBuilder( 'form_'.__CLASS__ );
            $this->form->setFormTitle( 'Manutenção e Cadastro de Processos' );
            
            $criteria_liberador = new TCriteria();
            $filter             = new TFilter( 'liberador', '=', '1' );
            $criteria_liberador->add( $filter );
    
            $criteria_seguradora = new TCriteria();
            $filter             = new TFilter( 'seguradora', '=', '1' );
            $criteria_seguradora->add( $filter );
            
            $id                    = new TEntry( 'id' );
            $id_vei                = new TEntry( 'id_vei' );
            $id_seg                = new TDBCombo( 'id_seg', 'afincco', 'Pessoa', 'id', 'nome', 'nome', $criteria_seguradora );
            $representante         = new TEntry( 'representante' );
            $nome_segurado         = new TEntry( 'nome_segurado' );
            $uf                    = new TCombo( 'uf' );
            $tipo                  = new TEntry( 'tipo' );
            $placa                 = new TEntry( 'placa' );
            $chassi                = new TEntry( 'chassi' );
            $marca_modelo          = new TEntry( 'marca_modelo' );
            $marca                 = new TEntry( 'marca' );
            $ano                   = new TEntry( 'ano' );
            $cor                   = new TEntry( 'cor' );
            $motor                 = new TEntry( 'motor' );
            $renavam               = new TEntry( 'renavam' );
            $sinistro              = new TEntry( 'sinistro' );
            $apolice               = new TEntry( 'apolice' );
            $combustivel           = new TEntry( 'combustivel' );
            $restricao             = new TEntry( 'restricao' );
            $tipo_servico_dec      = new TDBCombo( 'tipo_servico_dec', 'afincco', 'Tiposervico', 'id', 'nome', 'nome' );
            $tipo_ocorrencia_dec   = new TEntry( 'tipo_ocorrencia_dec' );
            $data_dec              = new TDate( 'data_dec' );
            $bo_dec                = new TEntry( 'bo_dec' );
            $cidade_dec            = new TEntry( 'cidade_dec' );
            $uf_dec                = new TCombo( 'uf_dec' );
            $informante_dec        = new TEntry( 'informante_dec' );
            $ddd_informante_dec    = new TEntry( 'ddd_informante_dec' );
            $fone_informante_dec   = new TEntry( 'fone_informante_dec' );
            $dp_dec                = new TEntry( 'dp_dec' );
            $cidade_rec            = new TEntry( 'cidade_rec' );
            $uf_rec                = new TCombo( 'uf_rec' );
            $data_rec              = new TDate( 'data_rec' );
            $bo_rec                = new TEntry( 'bo_rec' );
            $dp_rec                = new TEntry( 'dp_rec' );
            $cidade_dev            = new TEntry( 'cidade_dev' );
            $uf_dev                = new TCombo( 'uf_dev' );
            $dp_dev                = new TEntry( 'dp_dev' );
            $chassi_adulterado_dev = new TEntry( 'chassi_adulterado_dev' );
            $data_entrega_dev      = new TDate( 'data_entrega_dev' );
            $bo_dev                = new TEntry( 'bo_dev' );
            $telefone_dev          = new TEntry( 'telefone_dev' );
            $responsavel_dev       = new TEntry( 'responsavel_dev' );
            $local_entrega_dev     = new TEntry( 'local_entrega_dev' );
            $obs_dev               = new THtmlEditor( 'obs_dev' );
            $data_cadastro         = new TDateTime( 'data_cadastro' );
            $obs_dec               = new THtmlEditor( 'obs_dec' );
            $obs_rec               = new THtmlEditor( 'obs_rec' );
            $condChassi            = new TCombo( 'condChassi' );
            $condChassi1           = new TCombo( 'condChassi1' );
            $fone_informante_rec   = new TEntry( 'fone_informante_rec' );
            $ddd_informante_rec    = new TEntry( 'ddd_informante_rec' );
            $responsavel_rec       = new TEntry( 'responsavel_rec' );
            $condMotor             = new TCombo( 'condMotor' );
            $condMotor1            = new TCombo( 'condMotor1' );
            $placa_aplicada        = new TEntry( 'placa_aplicada' );
            $status                = new TDBCombo( 'status', 'afincco', 'Status', 'id', 'statu', 'statu' );
            $id_arq                = new TEntry( 'id_arq' );
            $nome                  = new TEntry( 'nome' );
            $usuario               = new TDBCombo( 'usuario', 'permission', 'SystemUser', 'id', 'name', 'name' );
            $tipoarq_id            = new TDBCombo( 'tipoarq_id', 'afincco', 'Tipoarquivo', 'id', 'nome', 'nome' );
            $restricao02           = new TCombo( 'restricao02' );
            $restricao021          = new TCombo( 'restricao021' );
            $liberador             = new TDBCombo( 'liberador', 'afincco', 'Pessoa', 'id', 'nome', 'nome', $criteria_liberador );
            $gestor                = new TDBCombo( 'gestor', 'permission', 'SystemUser', 'id', 'name', 'name' );
            $processo_origem       = new TEntry( 'processo_origem' );
            $processo_reintegracao = new TEntry( 'processo_reintegracao' );
            $tipo_liberacao_dev    = new TCombo( 'tipo_liberacao_dev' );
            $texto_transporte      = new THtmlEditor( 'texto_transporte' );
            $ocorrencia            = new THtmlEditor( 'ocorrencia' );
            $total_pagar           = new TNumeric( 'total_pagar', 2, ',', '.' );
            $total_receber         = new TNumeric( 'total_receber', 2, ',', '.' );
            $total_saldo           = new TNumeric( 'total_saldo', 2, ',', '.' );
            $pendente_pagar        = new TNumeric( 'pendente_pagar', 2, ',', '.' );
            $pendente_receber      = new TNumeric( 'pendente_receber', 2, ',', '.' );
            $pendente_saldo        = new TNumeric( 'pendente_saldo', 2, ',', '.' );
            
            $status->enableSearch();
            $condChassi->addItems( Utilidades::condicao_chassi_motor() );
            $condChassi1->addItems( Utilidades::condicao_chassi_motor() );
            $condMotor->addItems( Utilidades::condicao_chassi_motor() );
            $condMotor1->addItems( Utilidades::condicao_chassi_motor() );
            $restricao02->addItems( Utilidades::estado_bem() );
            $restricao021->addItems( Utilidades::estado_bem() );
            $tipo_liberacao_dev->addItems( Utilidades::tipo_liberacao() );
            $uf->addItems( Utilidades::uf() );
            $uf_dec->addItems( Utilidades::uf() );
            $uf_dev->addItems( Utilidades::uf() );
            $uf_rec->addItems( Utilidades::uf() );
            
            $condChassi->setChangeAction( new TAction( [ $this, 'onChangeChassi' ] ) );
            $condMotor->setChangeAction( new TAction( [ $this, 'onChangeMotor' ] ) );
            $restricao02->setChangeAction( new TAction( [ $this, 'onChangeRestricao02' ] ) );
            $condChassi1->setChangeAction( new TAction( [ $this, 'onChangeChassi1' ] ) );
            $condMotor1->setChangeAction( new TAction( [ $this, 'onChangeMotor1' ] ) );
            $restricao021->setChangeAction( new TAction( [ $this, 'onChangeRestricao021' ] ) );
            
            $id->setEditable( FALSE );
            $id_vei->setEditable( FALSE );
            $id_arq->setEditable( FALSE );
            $nome->setEditable( FALSE );
            $data_cadastro->setEditable( FALSE );
            $usuario->setEditable( FALSE );
            $obs_dec->setEditable( FALSE );
            $obs_dev->setEditable( FALSE );
            $total_pagar->setEditable( FALSE );
            $total_receber->setEditable( FALSE );
            $total_saldo->setEditable( FALSE );
            $pendente_pagar->setEditable( FALSE );
            $pendente_receber->setEditable( FALSE );
            $pendente_saldo->setEditable( FALSE );
            
            $grupos = TSession::getValue( 'usergroupids' );
            
            if ( !empty( $representante ) ) {
                if ( !in_array( '1', $grupos ) ) {
                    $representante->setEditable( FALSE );
                }
            }
            
            if ( !empty( $liberador ) ) {
                if ( !in_array( '1', $grupos ) ) {
                    if ( !in_array( '2', $grupos ) ) {
                        if ( !in_array( '3', $grupos ) ) {
                            $liberador->setEditable( FALSE );
                        }
                    }
                }
            }
            
            $data_cadastro->setMask( 'dd/mm/yyyy hh:ii' );
            $data_dec->setMask( 'dd/mm/yyyy' );
            $data_entrega_dev->setMask( 'dd/mm/yyyy' );
            $data_rec->setMask( 'dd/mm/yyyy' );
            
            $data_cadastro->setDatabaseMask( 'yyyy-mm-dd hh:ii' );
            $data_dec->setDatabaseMask( 'yyyy-mm-dd' );
            $data_entrega_dev->setDatabaseMask( 'yyyy-mm-dd' );
            $data_rec->setDatabaseMask( 'yyyy-mm-dd' );
            
            $obs_dec->setSize( '100%', 200 );
            $obs_rec->setSize( '100%', 100 );
            $obs_dev->setSize( '100%', 200 );
            $texto_transporte->setSize( '100%', 400 );
            $ocorrencia->setSize( '100%', 100 );
            
            $id->style               .= ';text-align:center;color:#000080;font-weight:bold;';
            $id_vei->style           .= ';text-align:center;color:#000080;font-weight:bold;';
            $id_arq->style           .= ';text-align:center;color:#000080;font-weight:bold;';
            $data_cadastro->style    .= ';text-align:center;color:#000080;font-weight:bold;';
            $data_dec->style         .= ';text-align:center;color:#000080;font-weight:bold;';
            $placa->style            .= ';text-align:center;color:#ff0000;font-weight:bold;';
            $placa_aplicada->style   .= ';text-align:center;color:#ff0000;font-weight:bold;';
            $chassi->style           .= ';color:#ff0000;font-weight:bold;';
            $motor->style            .= ';color:#ff0000;font-weight:bold;';
            $ano->style              .= ';text-align:center;';
            $cor->style              .= ';text-align:center;';
            $restricao02->style      .= ';color:#ff0000;font-weight:bold;';
            $restricao021->style     .= ';color:#ff0000;font-weight:bold;';
            $usuario->style          .= ';background-color:#FFFEEB;color:#ff0000; font-weight:bold;';
            $liberador->style        .= ';background-color:#FFFEEB;color:#000080; font-weight:bold;';
            $gestor->style           .= ';background-color:#FFFEEB;color:#800000; font-weight:bold;';
            $total_pagar->style      .= ';background-color:#FFFEEB;color:#FF0000; font-weight:bold;';
            $total_receber->style    .= ';background-color:#FFFEEB;color:#0000FF; font-weight:bold;';
            $total_saldo->style      .= ';background-color:#D2B48C;color:#000; font-weight:bold;';
            $pendente_receber->style .= ';background-color:#FFFEEB;color:#0000FF; font-weight:bold;';
            $pendente_pagar->style   .= ';background-color:#FFFEEB;color:#FF0000; font-weight:bold;';
            $pendente_saldo->style   .= ';background-color:#D2B48C;color:#000; font-weight:bold;';
            
            $campo_id                    = [ new TLabel( 'Processo' ), $id ];
            $campo_seguradora            = [ new TLabel( 'Seguradora' ), $id_seg ];
            $campo_representante         = [ new TLabel( 'Representante' ), $representante ];
            $campo_segurado              = [ new TLabel( 'Segurado' ), $nome_segurado ];
            $campo_data_cadastro         = [ new TLabel( 'Data Abertura' ), $data_cadastro ];
            $campo_id_veic               = [ new TLabel( 'Cód. Veículo' ), $id_vei ];
            $campo_uf                    = [ new TLabel( 'UF' ), $uf ];
            $campo_tipo                  = [ new TLabel( 'Tipo' ), $tipo ];
            $campo_placa                 = [ new TLabel( 'Placa' ), $placa ];
            $campo_placa_aplicada        = [ new TLabel( 'Placa Aplicada' ), $placa_aplicada ];
            $campo_chassi                = [ new TLabel( 'Chassi' ), $chassi ];
            $campo_motor                 = [ new TLabel( 'Motor' ), $motor ];
            $campo_combustivel           = [ new TLabel( 'Combustivel' ), $combustivel ];
            $campo_marca                 = [ new TLabel( 'Marca' ), $marca ];
            $campo_marca_modelo          = [ new TLabel( 'Marca/Modelo' ), $marca_modelo ];
            $campo_cor                   = [ new TLabel( 'Cor' ), $cor ];
            $campo_ano                   = [ new TLabel( 'Ano' ), $ano ];
            $campo_renavan               = [ new TLabel( 'Renavan' ), $renavam ];
            $campo_cond_chassi           = [ new TLabel( 'Condição Chassi' ), $condChassi ];
            $campo_cond_chassi1          = [ new TLabel( 'Condição Chassi' ), $condChassi1 ];
            $campo_sinistro              = [ new TLabel( 'Sinistro' ), $sinistro ];
            $campo_apolice               = [ new TLabel( 'Apólice' ), $apolice ];
            $campo_cond_motor            = [ new TLabel( 'Condição Motor' ), $condMotor ];
            $campo_cond_motor1           = [ new TLabel( 'Condição Motor' ), $condMotor1 ];
            $campo_tipo_servico          = [ new TLabel( 'Tipo de Serviço' ), $tipo_servico_dec ];
            $campo_tipo_ocorrencia       = [ new TLabel( 'Tipo Ocorrência' ), $tipo_ocorrencia_dec ];
            $campo_data_dec              = [ new TLabel( 'Data' ), $data_dec ];
            $campo_bo_dec                = [ new TLabel( 'B.O.' ), $bo_dec ];
            $campo_cidade_dec            = [ new TLabel( 'Cidade' ), $cidade_dec ];
            $campo_uf_dec                = [ new TLabel( 'UF' ), $uf_dec ];
            $campo_informante            = [ new TLabel( 'Informante' ), $informante_dec ];
            $campo_ddd_informante        = [ new TLabel( 'DDD' ), $ddd_informante_dec ];
            $campo_fone_informante       = [ new TLabel( 'Telefone' ), $fone_informante_dec ];
            $campo_dp_dec                = [ new TLabel( 'D.P.' ), $dp_dec ];
            $campo_obs_dec               = [ new TLabel( 'Observação' ), $obs_dec ];
            $campo_cidade_rec            = [ new TLabel( 'Cidade' ), $cidade_rec ];
            $campo_uf_rec                = [ new TLabel( 'UF' ), $uf_rec ];
            $campo_data_rec              = [ new TLabel( 'Data' ), $data_rec ];
            $campo_bo_rec                = [ new TLabel( 'B.O.' ), $bo_rec ];
            $campo_dp_rec                = [ new TLabel( 'D.P.' ), $dp_rec ];
            $campo_responsavel_rec       = [ new TLabel( 'Responsavel' ), $responsavel_rec ];
            $campo_ddd_infor_rec         = [ new TLabel( 'DDD' ), $ddd_informante_rec ];
            $campo_fone_infor_rec        = [ new TLabel( 'Fone' ), $fone_informante_rec ];
            $campo_endereco_rec          = [ new TLabel( 'Endereço' ), $obs_rec ];
            $campo_cidade_dev            = [ new TLabel( 'Cidade' ), $cidade_dev ];
            $campo_uf_dev                = [ new TLabel( 'UF' ), $uf_dev ];
            $campo_dp_dev                = [ new TLabel( 'D.P.' ), $dp_dev ];
            $campo_chassi_adulterado     = [ new TLabel( 'Chassi Adulterado' ), $chassi_adulterado_dev ];
            $campo_data_dev              = [ new TLabel( 'Data Entrega' ), $data_entrega_dev ];
            $campo_bo_dev                = [ new TLabel( 'B.O.' ), $bo_dev ];
            $campo_telefone_dev          = [ new TLabel( 'Telefone' ), $telefone_dev ];
            $campo_responsavel_dev       = [ new TLabel( 'Responsavel' ), $responsavel_dev ];
            $campo_local_entrega_dev     = [ new TLabel( 'Local Entrega' ), $local_entrega_dev ];
            $campo_obs_dev               = [ new TLabel( 'Endereço Entrega' ), $obs_dev ];
            $campo_status                = [ new TLabel( 'Status' ), $status ];
            $campo_id_arq                = [ new TLabel( 'Código' ), $id_arq ];
            $campo_nome_arq              = [ new TLabel( 'Arquivo' ), $nome ];
            $campo_usuario               = [ new TLabel( 'Usuario' ), $usuario ];
            $campo_liberador             = [ new TLabel( 'Liberador' ), $liberador ];
            $campo_gestor                = [ new TLabel( 'Gestor' ), $gestor ];
            $campo_tipoarq               = [ new TLabel( 'Tipo Arquivo' ), $tipoarq_id ];
            $campo_restricao02           = [ new TLabel( 'Estado do Bem' ), $restricao02 ];
            $campo_restricao021          = [ new TLabel( 'Estado do Bem' ), $restricao021 ];
            $campo_processo_origem       = [ new TLabel( 'N. Processo Origem' ), $processo_origem ];
            $campo_processo_reintegracao = [ new TLabel( 'N. Processo Reintegração' ), $processo_reintegracao ];
            $campo_tipo_liberacao_dev    = [ new TLabel( 'Tipo de Liberação' ), $tipo_liberacao_dev ];
            $campo_texto_transporte      = [ new TLabel( 'Texto Corpo E-Mail' ), $texto_transporte ];
            $campo_ocorrencia            = [ new TLabel( 'Nova Ocorrenência' ), $ocorrencia ];
            
            $buscaVeiculo = new TAction( [ $this, 'onBuscaVeiculo' ] );
            $placa->setExitAction( $buscaVeiculo );
            $chassi->setExitAction( $buscaVeiculo );
            $motor->setExitAction( $buscaVeiculo );
            
            $label1        = new TLabel( 'Dados Iniciais', '#7D78B6', 12, 'bi' );
            $label1->style = 'text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
            $this->form->appendPage( 'Dados Principais' );
            $this->form->addContent( [ $label1 ] );
            
            $row         = $this->form->addFields( $campo_id, $campo_seguradora, $campo_representante, $campo_segurado );
            $row->layout = [ 'col-md-1', 'col-md-4', 'col-md-3', 'col-md-4' ];
            
            $row         = $this->form->addFields( $campo_data_cadastro, $campo_sinistro, $campo_apolice );
            $row->layout = [ 'col-md-2', 'col-md-5', 'col-md-5' ];
            
            $row         = $this->form->addFields( $campo_usuario, $campo_gestor, $campo_liberador );
            $row->layout = [ 'col-md-4', 'col-md-4', 'col-md-4' ];
            
            $label2        = new TLabel( 'Dados do Veículo', '#7D78B6', 12, 'bi' );
            $label2->style = 'text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
            $this->form->addContent( [ $label2 ] );
            
            $row         = $this->form->addFields( $campo_id_veic, $campo_placa, $campo_uf, $campo_tipo, $campo_marca, $campo_marca_modelo );
            $row->layout = [ 'col-md-1', 'col-md-1', 'col-md-2', 'col-md-2', 'col-md-3', 'col-md-3' ];
            
            $row         = $this->form->addFields( $campo_cor, $campo_combustivel, $campo_chassi, $campo_motor, $campo_ano, $campo_renavan );
            $row->layout = [ 'col-md-1', 'col-md-2', 'col-md-3', 'col-md-3', 'col-md-1', 'col-md-2' ];
            
            $this->form->appendPage( 'Declaração' );
            $row         = $this->form->addFields( $campo_tipo_servico, $campo_tipo_ocorrencia, $campo_data_dec, $campo_bo_dec, $campo_cidade_dec, $campo_uf_dec, $campo_dp_dec );
            $row->layout = [ 'col-md-2', 'col-md-2', 'col-md-1', 'col-md-1', 'col-md-2', 'col-md-2', 'col-md-1' ];
            
            $this->form->addFields( $campo_obs_dec )->layout = [ 'col-md-12' ];
            
            $this->form->appendPage( 'Recuperação' );
            $row         = $this->form->addFields( $campo_cidade_rec, $campo_uf_rec, $campo_dp_rec, $campo_data_rec, $campo_bo_rec );
            $row->layout = [ 'col-md-3', 'col-md-2', 'col-md-3', 'col-md-2', 'col-md-2' ];
            
            $label5        = new TLabel( 'Dados - Veículo', '#7D78B6', 12, 'bi' );
            $label5->style = 'text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
            $this->form->addContent( [ $label5 ] );
            $row         = $this->form->addFields( $campo_cond_chassi, $campo_cond_motor, $campo_restricao02, $campo_placa_aplicada );
            $row->layout = [ 'col-md-2', 'col-md-2', 'col-md-2', 'col-md-2' ];
            
            $label5        = new TLabel( 'Dados - Informante', '#7D78B6', 12, 'bi' );
            $label5->style = 'text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
            $this->form->addContent( [ $label5 ] );
            
            $row         = $this->form->addFields( $campo_responsavel_rec, $campo_ddd_infor_rec, $campo_fone_infor_rec );
            $row->layout = [ 'col-md-4', 'col-md-1', 'col-md-2' ];
            
            $label51        = new TLabel( 'Dados - Processo Judicial', '#7D78B6', 12, 'bi' );
            $label51->style = 'text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
            $this->form->addContent( [ $label51 ] );
            $row         = $this->form->addFields( $campo_processo_origem, $campo_processo_reintegracao );
            $row->layout = [ 'col-md-4', 'col-md-4' ];
            
            $this->form->addFields( $campo_endereco_rec );
            
            $label6        = new TLabel( 'Dados - Devolução', '#7D78B6', 12, 'bi' );
            $label6->style = 'text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
            $this->form->appendPage( 'Devolução' );
            $this->form->addContent( [ $label6 ] );
            
            $row         = $this->form->addFields( $campo_cidade_dev, $campo_uf_dev, $campo_dp_dev, $campo_bo_dev, $campo_data_dev );
            $row->layout = [ 'col-md-2', 'col-md-2', 'col-md-2', 'col-md-2', 'col-md-2' ];
            $row         = $this->form->addFields( $campo_responsavel_dev, $campo_telefone_dev, $campo_local_entrega_dev, $campo_tipo_liberacao_dev );
            $row->layout = [ 'col-md-2', 'col-md-2', 'col-md-2', 'col-md-2', 'col-md-1' ];
            
            $label5        = new TLabel( 'Dados - Veículo', '#7D78B6', 12, 'bi' );
            $label5->style = 'text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
            $this->form->addContent( [ $label5 ] );
            $this->form->addFields( $campo_cond_chassi1, $campo_cond_motor1, $campo_restricao021 );
            
            $this->form->addFields( $campo_obs_dev );
            
            $this->gridstatus = new BootstrapDatagridWrapper( new TDataGrid() );
            
            $column_id            = new TDataGridColumn( 'id', 'Código', 'center', '10%' );
            $column_data_cadastro = new TDataGridColumn( 'data_cadastro', 'Data', 'center', '10%' );
            $column_representante = new TDataGridColumn( 'representante', 'Usuário', 'center', '10%' );
            $column_status        = new TDataGridColumn( 'status->statu', 'Status', 'left', '70%' );
            
            $column_data_cadastro->setTransformer( function( $value, $object, $row ) {
                $dtcadastro = new DateTime( $value );
                
                return "<p style='color:#00008B;'><b>".$dtcadastro->format( 'd/m/Y H:i:s' )."</b></p>";
            } );
            
            $column_representante->setTransformer( function( $value, $object, $row ) {
                return "<p style='color:#ff0000;'><b>".$value."</b></p>";
            } );
            
            $this->gridstatus->addColumn( $column_id );
            $this->gridstatus->addColumn( $column_data_cadastro );
            $this->gridstatus->addColumn( $column_representante );
            $this->gridstatus->addColumn( $column_status );
            
            $this->gridstatus->createModel();
            
            $this->form->appendPage( 'Status' );
            
            $novo_status        = new TButton( 'novo_status' );
            $action_novo_status = new TAction( [ $this, 'onAddStatus' ] );
            $novo_status->setAction( $action_novo_status, 'Adicionar' );
            $novo_status->setImage( 'fas:plus-circle #8B7500 fa-2x' );
            $novo_status->style = 'margin-top:28px;font-weight:900;color:#8B7500;';
            
            $row         = $this->form->addFields( $campo_status, [ $novo_status ] );
            $row->layout = [ 'col-md-6', 'col-md-2' ];
            
            $this->form->addContent( [ $this->gridstatus ] );
            
            $this->form->appendPage( 'Arquivos' );
            
            $this->gridArquivos = new BootstrapDatagridWrapper( new TDataGrid() );
            
            $column_arq_id            = new TDataGridColumn( 'id_arq', 'Código', 'center', '5%' );
            $column_arq_data_cadastro = new TDataGridColumn( 'data_arq', 'Data', 'center', '10%' );
            $column_arq_usuario       = new TDataGridColumn( 'usuario', 'Usuário', 'center', '20%' );
            $column_arq_nome          = new TDataGridColumn( 'nome', 'Nome Arquivo', 'left', '20%' );
            $column_arq_tipo          = new TDataGridColumn( 'Tipoarquivo->nome', 'Tipo de Arquivo', 'center', '20%' );
            $column_arq_tamanho       = new TDataGridColumn( 'nome', 'Tamanho', 'center', '10%' );
            $column_arq_assinado      = new TDataGridColumn( 'assinado', 'Assinado', 'center', '5%' );
            
            $column_arq_assinado->setTransformer( function( $value, $object, $row ) {
                if ( $value == '1' ) {
                    $div        = new TElement( 'span' );
                    $div->class = "label label-success";
                    $div->style = "text-shadow:none; font-size:12px";
                    $div->add( Utilidades::sim_nao()[ $value ] );
                    return $div;
                } else {
                    $div        = new TElement( 'span' );
                    $div->class = "label label-danger";
                    $div->value = 'Não';
                    $div->style = "text-shadow:none; font-size:12px";
                    $div->add( Utilidades::sim_nao()[ $value ] );
                    return $div;
                }
                
                return $image;
            } );
            
            $column_arq_data_cadastro->setTransformer( function( $value, $object, $row ) {
                $dtcadastro = new DateTime( $value );
                
                return "<p style='color:#00008B;'><b>".$dtcadastro->format( 'd/m/Y H:i:s' )."</b></p>";
            } );
            
            $column_arq_tamanho->setTransformer( function( $value, $object, $row ) {
                $source_file = 'app/arquivos/'.$object->id_processo.'/'.$object->nome;
                if ( !file_exists( $source_file ) ) {
                    $source_file = 'app/arquivos/'.$object->nome;
                }
                if ( file_exists( $source_file ) ) {
                    $tamanho = filesize( $source_file );
                    if ( $tamanho > 1000000 ) {
                        return "<p style='color:#FF0000;'><b>".Utilidades::tamanho_arquivo( $tamanho )."</b></p>";
                    } else {
                        return "<p style='color:#00008B;'><b>".Utilidades::tamanho_arquivo( $tamanho )."</b></p>";
                    }
                } else {
                    return '';
                }
            } );
            
            $this->gridArquivos->addColumn( $column_arq_id );
            $this->gridArquivos->addColumn( $column_arq_data_cadastro );
            $this->gridArquivos->addColumn( $column_arq_usuario );
            $this->gridArquivos->addColumn( $column_arq_nome );
            $this->gridArquivos->addColumn( $column_arq_tamanho );
            $this->gridArquivos->addColumn( $column_arq_tipo );
            $this->gridArquivos->addColumn( $column_arq_assinado );
            
            $action_edit = new TDataGridAction( [ $this, 'onEditarArquivo' ], [ 'key'            => '{id_arq}',
                                                                                'register_state' => 'false' ] );
            $action_del  = new TDataGridAction( [ $this, 'onExcluirArquivo' ], [ 'key'            => '{id_arq}',
                                                                                 'register_state' => 'false' ] );
            $action_env  = new TDataGridAction( [ $this, 'onEnviarEmailArquivo' ], [ 'key'            => '{id_arq}',
                                                                                     'register_state' => 'false' ] );
            $action_car  = new TDataGridAction( [ 'procuracaoCarimboForm', 'onEdit' ], [ 'key'            => '{id_arq}',
                                                                                         'register_state' => 'false' ] );
            
            $action_del->setDisplayCondition( [ $this, 'displayExcluirArquivo' ] );
            $action_car->setDisplayCondition( [ $this, 'displayCarimbo' ] );
            
            $this->gridArquivos->addAction( $action_edit, _t( 'Edit' ), 'far:edit blue fa-lg' );
            $this->gridArquivos->addAction( $action_del, _t( 'Delete' ), 'far:trash-alt red fa-lg' );
            $this->gridArquivos->addAction( $action_env, 'E-Mail', 'far:envelope brown  red fa-lg' );
            $this->gridArquivos->addAction( $action_car, 'Assinar', 'far:clock green fa-lg' );
            
            $this->gridArquivos->createModel();
            
            $novo_arquivo    = new TButton( 'novo_arquivo' );
            $action_arq_novo = new TAction( [ $this, 'onInputDialog' ] );
            $novo_arquivo->setAction( $action_arq_novo, 'Novo Arquivo' );
            $novo_arquivo->setImage( 'fas:plus-circle blue fa-2x' );
            $novo_arquivo->style = 'margin-top:28px;font-weight:900;color: blue';
            
            $row         = $this->form->addFields( $campo_id_arq, $campo_nome_arq, $campo_tipoarq, [ $novo_arquivo ] );
            $row->layout = [ 'col-md-1', 'col-md-2', 'col-md-5', 'col-md-1' ];
            
            $label8        = new TLabel( 'Arquivos do Processo', '#7D78B6', 12, 'bi' );
            $label8->style = 'text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
            $this->form->addContent( [ $label8 ] );
            
            $this->frame        = new TElement( 'div' );
            $this->frame->id    = 'arq_frame';
            $this->frame->style = 'width:100%;height:100%;border:2px solid red;padding:4px;';
            
            $table1 = new TTable();
            $table1->addRow()->addCell( $this->gridArquivos );
            
            $table2 = new TTable();
            $table2->addRow()->addCell( $this->frame );
            
            $hbox                        = new THBox();
            $hbox->add( $table1 )->style .= 'vertical-align:top; max-width:45% !important; height:100% !important';
            $hbox->add( $table2 )->style .= 'vertical-align:top; max-width:43% !important; height:100% !important';
            
            $this->form->addContent( [ $hbox ] )->layout = [ 'col-md-12' ];
            
            $grupos = TSession::getValue( 'usergroupids' );
            
            $this->mostra_recibo           = FALSE;
            $this->mostra_financeiro       = FALSE;
            $this->mostra_recibo_prestador = FALSE;
            
            if ( in_array( 1, $grupos ) ) {
                $this->mostra_recibo           = TRUE;
                $this->mostra_financeiro       = TRUE;
                $this->mostra_recibo_prestador = TRUE;
            }
            if ( in_array( 2, $grupos ) ) {
                $this->mostra_recibo           = TRUE;
                $this->mostra_recibo_prestador = TRUE;
            }
            if ( in_array( 3, $grupos ) ) {
                $this->mostra_recibo           = TRUE;
                $this->mostra_financeiro       = TRUE;
                $this->mostra_recibo_prestador = TRUE;
            }
            
            if ( in_array( 5, $grupos ) ) {
                $this->mostra_recibo_prestador = TRUE;
            }
            
            $this->gridComprovante = new BootstrapDatagridWrapper( new TDataGrid() );
            
            if ( $this->mostra_recibo ) {
                //SOLICITAR REEMBOLSO
                $this->form->appendPage( 'Recibos Reembolso' );
                
                $column_comp_id               = new TDataGridColumn( 'IdComprovante', 'Código', 'center', '10%' );
                $column_comp_data_atualizacao = new TDataGridColumn( 'Data_Atualizao', 'Data', 'center', '20%' );
                $column_comp_seguradora       = new TDataGridColumn( 'seguradora->nome', 'Seguradora', 'left', '40%' );
                $column_comp_valor_total      = new TDataGridColumn( 'ValorTotal', 'Valor Total', 'right', '20%' );
                $column_comp_status           = new TDataGridColumn( 'Status', 'Status', 'left', '10%' );
                
                $column_comp_data_atualizacao->setTransformer( function( $value, $object, $row ) {
                    $dtcadastro = new DateTime( $value );
                    
                    return "<b style='color:#00008B;'>".$dtcadastro->format( 'd/m/Y H:i:s' )."</b>";
                } );
                
                $column_comp_valor_total->setTransformer( function( $value, $object, $row ) {
                    if ( is_numeric( $value ) ) {
                        return "<b style='color:#ff0000;'>".number_format( $value, 2, ',', '.' )."</b>";
                    } else {
                        return $value;
                    }
                    
                } );
                
                $this->gridComprovante->addColumn( $column_comp_id );
                $this->gridComprovante->addColumn( $column_comp_data_atualizacao );
                $this->gridComprovante->addColumn( $column_comp_seguradora );
                $this->gridComprovante->addColumn( $column_comp_valor_total );
                $this->gridComprovante->addColumn( $column_comp_status );
                
                $action_comp = new TDataGridAction( [ 'comprovanteForm', 'onEdit' ], [ 'key'            => '{IdComprovante}',
                                                                                       'register_state' => 'false' ] );
                $action_cdel = new TDataGridAction( [ $this, 'onDeleteComprovante' ], [ 'key'            => '{IdComprovante}',
                                                                                        'register_state' => 'false' ] );
                
                //$action_comp->setDisplayCondition( [ $this, 'displayColumn' ] );
                $action_cdel->setDisplayCondition( [ $this, 'displayColumn' ] );
                
                $this->gridComprovante->addAction( $action_comp, _t( 'Edit' ), 'far:edit blue fa-lg' );
                $this->gridComprovante->addAction( $action_cdel, _t( 'Delete' ), 'far:trash-alt red fa-lg' );
                
                $this->gridComprovante->createModel();
                
                $label7        = new TLabel( 'Recibos', '#7D78B6', 12, 'bi' );
                $label7->style = 'text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
                $this->form->addContent( [ $label7 ] );
                
                $novo_recibo     = new TButton( 'novo_recibo' );
                $action_rec_novo = new TAction( [ $this, 'onChamarRecibo' ] );
                $novo_recibo->setAction( $action_rec_novo, 'Novo Recibo' );
                $novo_recibo->setImage( 'fas:plus-circle green fa-lg' );
                $novo_recibo->style = 'color: green; font-weight:bold;';
                
                $this->form->addFields( [ $novo_recibo ] );
                
                $this->form->addContent( [ $this->gridComprovante ] );
                
                TSession::setValue( 'chamador_comprovante', __CLASS__ );
            }
            
            //Transpoorte
            $email_transporte  = new TButton( 'email_transporte' );
            $action_transporte = new TAction( [ $this, 'onEmailTransporte' ] );
            $email_transporte->setAction( $action_transporte, 'Enviar E-Mail' );
            $email_transporte->setImage( 'fas:plus-circle red fa-2x' );
            $email_transporte->style = 'color:#ff0000; font-weight:bold;';
            
            $this->form->appendPage( 'Transporte' );
            $this->form->addFields( [ $email_transporte ] );
            $this->form->addFields( $campo_texto_transporte )->layout = [ 'col-md-12' ];
            
            //OCORRENCIAS
            $this->form->appendPage( 'Ocorrências' );
            
            $this->gridOcorrencia = new BootstrapDatagridWrapper( new TDataGrid() );
            
            $column_ocor_id        = new TDataGridColumn( 'id', 'Código', 'center', '50' );
            $column_ocor_data      = new TDataGridColumn( 'data_ocor', 'Data', 'center', '150' );
            $column_ocor_usuario   = new TDataGridColumn( 'usuario', 'Usuário', 'center', '150' );
            $column_ocor_historico = new TDataGridColumn( 'historico', 'Histórico', 'left' );
            
            $column_ocor_data->setTransformer( function( $value, $object, $row ) {
                $dtcadastro = new DateTime( $value );
                
                return "<p style='color:#00008B;'><b>".$dtcadastro->format( 'd/m/Y H:i:s' )."</b></p>";
            } );
            
            $this->gridOcorrencia->addColumn( $column_ocor_id );
            $this->gridOcorrencia->addColumn( $column_ocor_data );
            $this->gridOcorrencia->addColumn( $column_ocor_usuario );
            $this->gridOcorrencia->addColumn( $column_ocor_historico );
            
            $this->gridOcorrencia->createModel();
            
            $salvar_ocor      = new TButton( 'salvar_ocor' );
            $action_save_ocor = new TAction( [ $this, 'onSalvarOcor' ] );
            $salvar_ocor->setAction( $action_save_ocor, 'salvar Ocorrência' );
            $salvar_ocor->setImage( 'fas:plus-circle green fa-lg' );
            $salvar_ocor->style = 'color:green; font-weight:bold;margin-top:24px';
            
            $this->form->addFields( $campo_ocorrencia, [ $salvar_ocor ] )->layout = [ 'col-md-8', 'col-md-1' ];
            
            $this->form->addContent( [ $this->gridOcorrencia ] );
            
            //grid recibo prestador
            $this->gridRecibos = new BootstrapDatagridWrapper( new TDataGrid() );
            
            if ( $this->mostra_recibo_prestador ) {
                //SOLICITAR REEMBOLSO
                $this->form->appendPage( 'Recibos Prestador' );
                
                $column_rec_id           = new TDataGridColumn( 'id', 'Código', 'center', '10%' );
                $column_rec_data_emissao = new TDataGridColumn( 'data_emissao', 'Data Emissão', 'center', '10%' );
                $column_rec_prestador    = new TDataGridColumn( 'pessoa->nome', 'Prestador', 'left', '60%' );
                $column_rec_valor_total  = new TDataGridColumn( 'valor_recibo', 'Valor Total', 'center', '10%' );
                $column_rec_status       = new TDataGridColumn( 'status', 'Status', 'left', '10%' );
                
                $column_rec_data_emissao->setTransformer( function( $value, $object, $row ) {
                    return "<p style='color:#00008B;'><b>".TDate::date2br( $value )."</b></p>";
                } );
                
                $column_rec_valor_total->setTransformer( function( $value, $object, $row ) {
                    if ( is_numeric( $value ) ) {
                        return "<p style='color:#ff0000;'><b>".number_format( $value, 2, ',', '.' )."</b></p>";
                    } else {
                        return $value;
                    }
                    
                } );
                
                $this->gridRecibos->addColumn( $column_rec_id );
                $this->gridRecibos->addColumn( $column_rec_data_emissao );
                $this->gridRecibos->addColumn( $column_rec_prestador );
                $this->gridRecibos->addColumn( $column_rec_valor_total );
                $this->gridRecibos->addColumn( $column_rec_status );
                
                $action_recedit = new TDataGridAction( [ 'recibosForm', 'onEdit' ], [ 'key'            => '{id}',
                                                                                      'register_state' => 'false' ] );
                $action_recdel  = new TDataGridAction( [ $this, 'onDeleteRecibos' ], [ 'key'            => '{id}',
                                                                                       'register_state' => 'false' ] );
                
                $action_recedit->setDisplayCondition( [ $this, 'displayColumn' ] );
                $action_recdel->setDisplayCondition( [ $this, 'displayColumn' ] );
                
                $this->gridRecibos->addAction( $action_recedit, _t( 'Edit' ), 'far:edit blue fa-lg' );
                $this->gridRecibos->addAction( $action_recdel, _t( 'Delete' ), 'far:trash-alt red fa-lg' );
                
                $this->gridRecibos->createModel();
                
                $label7        = new TLabel( 'Recibos', '#7D78B6', 12, 'bi' );
                $label7->style = 'text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
                $this->form->addContent( [ $label7 ] );
                
                $novo_recibos     = new TButton( 'novo_recibos' );
                $action_recs_novo = new TAction( [ $this,
                                                   'onChamarRecibos_Prestador' ], [ 'register_state' => 'false' ] );
                $novo_recibos->setAction( $action_recs_novo, 'Novo Recibo' );
                $novo_recibos->setImage( 'fas:plus-circle red fa-lg' );
                $novo_recibos->style = 'color:#ff0000; font-weight:bold;';
                
                $this->form->addFields( [ $novo_recibos ] );
                
                $this->form->addContent( [ $this->gridRecibos ] );
                
                TSession::setValue( 'chamador_recibos', __CLASS__ );
            }
            
            //grid financeiro da OS
            $this->gridFinanceiro = new BootstrapDatagridWrapper( new TDataGrid() );
            
            if ( $this->mostra_financeiro ) {
                //SOLICITAR REEMBOLSO
                $this->form->appendPage( 'Financeiro' );
                
                $column_fin_id         = new TDataGridColumn( 'titulo_id', 'Código', 'center', '5%' );
                $column_fin_pessoa     = new TDataGridColumn( 'pessoa->nome', 'Cliente/Fornecedor', 'left', '40%' );
                $column_fin_tipolan    = new TDataGridColumn( 'tipolancamento->nome', 'Tipo Lançamento', 'left', '30%' );
                $column_fin_emissao    = new TDataGridColumn( 'data_emissao', 'Data Emissão', 'center', '10%' );
                $column_fin_vencimento = new TDataGridColumn( 'data_vencimento', 'Data Vencimento', 'center', '10%' );
                $column_fin_numero     = new TDataGridColumn( 'numero', 'Numero', 'center', '10%' );
                $column_fin_pr         = new TDataGridColumn( 'pagar_receber', 'Pagar/Receber', 'center', '10%' );
                $column_fin_dc         = new TDataGridColumn( 'dc', 'D/C', 'center', '10%' );
                $column_fin_valor      = new TDataGridColumn( 'valor', 'Valor', 'right', '10%' );
                $column_fin_saldo      = new TDataGridColumn( 'saldo', 'Saldo', 'right', '10%' );
                $column_fin_conta      = new TDataGridColumn( 'contacorrente->', 'Conta Corrente', 'left', '30%' );
                
                $column_fin_emissao->setTransformer( function( $value, $object, $row ) {
                    return "<p style='color:#00008B;'><b>".TDate::date2br( $value )."</b></p>";
                } );
                $column_fin_vencimento->setTransformer( function( $value, $object, $row ) {
                    return "<p style='color:#00008B;'><b>".TDate::date2br( $value )."</b></p>";
                } );
                
                $column_fin_valor->setTransformer( function( $value, $object, $row ) {
                    if ( $object->pagar_receber == 'P' ) {
                        return "<p style='color:#ff0000;'><b>".number_format( $value, 2, ',', '.' )."</b></p>";
                    } else {
                        return "<p style='color:#0000ff;'><b>".number_format( $value, 2, ',', '.' )."</b></p>";
                    }
                } );
                
                $column_fin_saldo->setTransformer( function( $value, $object, $row ) {
                    if ( $value == 0 ) {
                        return "";
                    } else {
                        return number_format( $value, 2, ',', '.' );
                    }
                } );
                
                $this->gridFinanceiro->addColumn( $column_fin_id );
                $this->gridFinanceiro->addColumn( $column_fin_pessoa );
                $this->gridFinanceiro->addColumn( $column_fin_tipolan );
                $this->gridFinanceiro->addColumn( $column_fin_emissao );
                $this->gridFinanceiro->addColumn( $column_fin_vencimento );
                $this->gridFinanceiro->addColumn( $column_fin_numero );
                $this->gridFinanceiro->addColumn( $column_fin_pr );
                $this->gridFinanceiro->addColumn( $column_fin_valor );
                $this->gridFinanceiro->addColumn( $column_fin_saldo );
                $this->gridFinanceiro->addColumn( $column_fin_conta );
                
                $this->gridFinanceiro->createModel();
                
                $label7        = new TLabel( 'Dados Financeiros', '#7D78B6', 12, 'bi' );
                $label7->style = 'text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
                $this->form->addContent( [ $label7 ] );
                
                $this->form->addContent( [ $this->gridFinanceiro ] );
                
                $label8        = new TLabel( 'Totalização dos Dados Financeiros', '#7D78B6', 12, 'bi' );
                $label8->style = 'text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
                $this->form->addContent( [ $label8 ] );
                
                $campo_total_receber    = [ new TLabel( 'Total a Receber' ), $total_receber ];
                $campo_total_pagar      = [ new TLabel( 'Total a Pagar' ), $total_pagar ];
                $campo_total_saldo      = [ new TLabel( 'Saldo (Receber - Pagar)' ), $total_saldo ];
                $campo_pendente_pagar   = [ new TLabel( 'Pendente Pagamento' ), $pendente_pagar ];
                $campo_pendente_receber = [ new TLabel( 'Pendente Recebimento' ), $pendente_receber ];
                
                $row         = $this->form->addFields( $campo_total_receber, $campo_total_pagar, $campo_total_saldo );
                $row->layout = [ 'col-md-2', 'col-md-2', 'col-md-2' ];
                $row         = $this->form->addFields( $campo_pendente_receber, $campo_pendente_pagar );
                $row->layout = [ 'col-md-2', 'col-md-2', 'col-md-2' ];
            }
            
            $this->form->addAction( _t( 'Save' ), new TAction( [ $this, 'onSave' ] ), 'far:save' );
            $this->form->addAction( _t( 'New' ), new TAction( [ $this, 'onEdit' ] ), 'fas:plus green' );
            $this->form->addAction( 'Voltar', new TAction( [ 'processoaList',
                                                             'onReload' ] ), 'fa: fa-table blue' )->style           = 'margin-left: 100px';
            $this->form->addAction( 'E-Mail Seguradora', new TAction( [ $this,
                                                                        'onEmail' ] ), 'far:envelope blue' )->style = 'margin-left: 10px';
            //$this->form->addAction( 'Imprimir', new TAction( [ $this, 'onImprimir' ] ), 'fa: fa-print red' )->style        = 'margin-left: 100px';
            $this->form->addAction( 'Avaliação', new TAction( [ $this, 'onAvaliacao' ] ), 'fa: fa-print green' );
            $this->form->addAction( 'Capa Processo', new TAction( [ $this, 'onCapaProcesso' ] ), 'fa: fa-print blue' );
            //$this->form->addAction( 'Envelope', new TAction( [ $this, 'onEnvelopeProcesso' ] ), 'fa: fa-print blue' );
            $this->form->addAction( 'Laudo', new TAction( [ $this, 'onLaudoProcesso' ] ), 'fa: fa-print green' );
            $this->form->addAction( 'Decalque', new TAction( [ $this, 'onDecalqueProcesso' ] ), 'fa: fa-print red' );
            $this->form->addAction( 'Procuração', new TAction( [ $this, 'onProcuracao' ] ), 'fas:file-powerpoint blue' );
            $this->form->addAction( 'Carta devolução', new TAction( [ $this, 'onDevolucao' ] ), 'fas:file-pdf red' );
            
            $script = "$('ul li').delegate('a','click', function() {
                        __adianti_ajax_exec('class=processoaForm&method=onTabClick&current_page='+$(this).parent('li').index())
                        });";
            TScript::create( $script );
            
            $container        = new TVBox();
            $container->style = 'width: 100%';
            $container->add( $this->form );
            
            $container->adianti_target_container = 'processo';
            $container->adianti_target_title     = 'Processos';
            
            parent::add( $container );
        }
        
        public static function onChamarRecibo( $param )
        {
            if ( empty( $param[ 'cidade_dev' ] ) or empty( $param[ 'uf_dev' ] ) or empty( $param[ 'dp_dev' ] ) or empty( $param[ 'restricao021' ] ) or empty( $param[ 'data_entrega_dev' ] ) or empty( $param[ 'bo_dev' ] ) or empty( $param[ 'telefone_dev' ] ) or empty( $param[ 'responsavel_dev' ] ) or empty( $param[ 'local_entrega_dev' ] ) or empty( $param[ 'tipo_liberacao_dev' ] ) or empty( $param[ 'condChassi1' ] ) or empty( $param[ 'condMotor1' ] ) ) {
                new TMessage( 'error', 'Por favor informe todos os dados da ABA DEVOLUÇÃO' );
            } else {
                $envio                     = [];
                $envio[ 'id' ]             = $param[ 'id' ];
                $envio[ 'id_seg' ]         = $param[ 'id_seg' ];
                $envio[ 'placa' ]          = $param[ 'placa' ];
                $envio[ 'data_cadastro' ]  = $param[ 'data_cadastro' ];
                $envio[ 'register_state' ] = FALSE;
                
                AdiantiCoreApplication::loadPage( 'comprovanteForm', 'onEdit', $envio );
            }
        }
        
        public static function onChamarRecibos_Prestador( $param )
        {
            if ( empty( $param[ 'cidade_dev' ] ) or empty( $param[ 'uf_dev' ] ) or empty( $param[ 'dp_dev' ] ) or empty( $param[ 'restricao021' ] ) or empty( $param[ 'data_entrega_dev' ] ) or empty( $param[ 'bo_dev' ] ) or empty( $param[ 'telefone_dev' ] ) or empty( $param[ 'responsavel_dev' ] ) or empty( $param[ 'local_entrega_dev' ] ) or empty( $param[ 'tipo_liberacao_dev' ] ) or empty( $param[ 'condChassi1' ] ) or empty( $param[ 'condMotor1' ] ) ) {
                new TMessage( 'error', 'Por favor informe todos os dados da ABA DEVOLUÇÃO' );
            } else {
                if ( !TSession::getValue( 'Processoa_liberador' ) ) {
                    new TMessage( 'error', 'Por favor informe o LIBERADOR, salve e tente novamente.' );
                } else {
                    $envio                  = [];
                    $envio[ 'id' ]          = $param[ 'id' ];
                    $envio[ 'processo_id' ] = $param[ 'id' ];
                    
                    TApplication::loadPage( 'recibosForm', 'onEdit', $envio );
                }
            }
        }
        
        public static function onChangeChassi( $param )
        {
            if ( $param[ 'condChassi' ] !== $param[ 'condChassi1' ] ) {
                $obj              = new StdClass();
                $obj->condChassi1 = $param[ 'condChassi' ];
                TForm::sendData( 'form_'.__CLASS__, $obj );
            }
        }
        
        public static function onChangeMotor( $param )
        {
            if ( $param[ 'condMotor' ] !== $param[ 'condMotor1' ] ) {
                $obj             = new StdClass();
                $obj->condMotor1 = $param[ 'condMotor' ];
                TForm::sendData( 'form_'.__CLASS__, $obj );
            }
        }
        
        public static function onChangeRestricao02( $param )
        {
            if ( $param[ 'restricao02' ] !== $param[ 'restricao021' ] ) {
                $obj               = new StdClass();
                $obj->restricao021 = $param[ 'restricao02' ];
                TForm::sendData( 'form_'.__CLASS__, $obj );
            }
        }
        
        public static function onChangeChassi1( $param )
        {
            if ( $param[ 'condChassi' ] !== $param[ 'condChassi1' ] ) {
                $obj             = new StdClass();
                $obj->condChassi = $param[ 'condChassi1' ];
                TForm::sendData( 'form_'.__CLASS__, $obj );
            }
        }
        
        public static function onChangeMotor1( $param )
        {
            if ( $param[ 'condMotor' ] !== $param[ 'condMotor1' ] ) {
                $obj            = new StdClass();
                $obj->condMotor = $param[ 'condMotor1' ];
                TForm::sendData( 'form_'.__CLASS__, $obj );
            }
        }
        
        public static function onChangeRestricao021( $param )
        {
            if ( $param[ 'restricao02' ] !== $param[ 'restricao021' ] ) {
                $obj              = new StdClass();
                $obj->restricao02 = $param[ 'restricao021' ];
                TForm::sendData( 'form_'.__CLASS__, $obj );
            }
        }
        
        public static function onTabClick( $param )
        {
            TSession::setValue( 'Processoa_current_page', $param[ 'current_page' ] );
        }
        
        public static function onProcuracao( $param )
        {
            try {
                TTransaction::open( 'afincco' );
                $object = Processo::find( $param[ 'id' ] );
                if ( $object ) {
                    if ( empty( $object->liberador ) ) {
                        new TMessage( 'Erro', '<b>Liberador </b> não informado' );
                    } else {
                        $relatorio = new procuracao( $object );
                        if ( $relatorio ) {
                            parent::openFile( $relatorio->get_arquivo() );
                        }
                    }
                }
                TTransaction::close();
                TSession::setValue( 'Processoa_current_page', '5' );
                AdiantiCoreApplication::loadPage( 'processoaForm', 'onEdit', TSession::getValue( 'Processoa_param' ) );
            } catch ( Exception $e ) {
                new TMessage( 'Erro', '<b>Error:</b> '.$e->getMessage() );
            }
        }
        
        public static function onEmail( $param )
        {
            try {
                TTransaction::open( 'afincco' );
                
                $object = Processo::find( $param[ 'id' ] );
                $object->seguradoras;
                new emailEntrada( $object, FALSE );
                
                TTransaction::close();
                
            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();
            }
        }
        
        public static function onEmailTransporte( $param )
        {
            try {
                TTransaction::open( 'afincco' );
                
                $object = Processo::find( $param[ 'id' ] );
                $object->seguradoras;
                $texto_email              = new emailTransporte( $object, FALSE, TRUE );
                $object->texto_transporte = $texto_email->gerarEmail( FALSE, $param[ 'texto_transporte' ] );
                
                $sts_sessao = TSession::getValue( 'status' );
                
                $object                = new Hstatus();
                $object->id_status     = 10;
                $object->id_processo   = $param[ 'id' ];
                $object->representante = TSession::getValue( 'login' );
                $object->data_cadastro = date( 'Y-m-d H:i:s' );
                $object->store();
                
                $key                = (int)$object->id;
                $sts_sessao[ $key ] = [ 'id'            => $object->id, 'id_status' => $object->id_status,
                                        'id_processo'   => $object->id_processo,
                                        'representante' => $object->representante,
                                        'data_cadastro' => $object->data_cadastro,
                                        'nome'          => $object->get_status()->statu ];
                
                TSession::setValue( 'status', $sts_sessao );
                
                TTransaction::close();
                TSession::setValue( 'Processoa_current_page', '5' );
                AdiantiCoreApplication::loadPage( 'processoaForm', 'onEdit', TSession::getValue( 'Processoa_param' ) );
                
            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();
            }
        }
        
        public static function onBuscaVeiculo( $param )
        {
            try {
                TTransaction::open( 'afincco' );
                $key      = $param[ 'id' ];
                $placa    = trim( $param[ 'placa' ] );
                $chassi   = trim( $param[ 'chassi' ] );
                $motor    = trim( $param[ 'motor' ] );
                $liberado = TRUE;
                if ( empty( $key ) ) {
                    if (!empty($placa)) {
                        $verifica = Processo::where( 'placa', '=', $placa )->load();
                    }else {
                        if ( !empty( $chasssi ) ) {
                            $verifica = Processo::where( 'chassi', '=', $chassi )->load();
                        }else {
                            if ( !empty( $motor ) ) {
                                $verifica = Processo::where( 'motor', '=', $chassi )->load();
                            }
                        }
                    }
                    if ( $verifica ) {
                        $liberado = FALSE;
                        foreach ( $verifica as $objeto ) {
                            if ( $objeto->onValidaStatus( 19 ) ) {
                                $liberado = TRUE;
                            }
                        }
                    }
                }
                if ( $liberado ) {
                    $object  = veiculos::where( 'placa', '=', $placa )->load();
                    $retorno = new StdClass();
                    if ( !$object ) {
                        $object = new veiculos();
                        $object->Importar_Veiculo( $placa, $chassi, $motor );
                    } else {
                        $object = $object[ 0 ];
                    }
                    $retorno->id_veic      = $object->id;
                    $retorno->uf           = $object->uf;
                    $retorno->tipo         = $object->tipo;
                    $retorno->chassi       = $object->chassi;
                    $retorno->motor        = $object->motor;
                    $retorno->combustivel  = $object->combustivel;
                    $retorno->marca        = $object->marca;
                    $retorno->marca_modelo = $object->marca_modelo;
                    $retorno->cor          = $object->cor;
                    $retorno->ano          = $object->ano;
                    $retorno->renavam      = $object->renavam;
                    
                    TForm::sendData( 'form_'.__CLASS__, $retorno );
                } else {
                    new TMessage( 'erro', 'Placa ja possui processo em aberto, pesquise !!' );
                }
                TTransaction::close();
            } catch ( Exception $e ) {
                new TMessage( 'Erro', $e->getMessage() );
            }
        }
        
        public static function onImprimir( $param )
        {
            try {
                TTransaction::open( 'afincco' );
                $object = Processo::find( $param[ 'id' ] );
                if ( $object ) {
                    $relatorio = new imprimirProcesso( $object );
                    if ( $relatorio ) {
                        parent::openFile( $relatorio->get_arquivo() );
                    }
                }
                TTransaction::close();
            } catch ( Exception $e ) {
                new TMessage( 'Erro', $e->getMessage() );
            }
        }
        
        public static function onCapaProcesso( $param )
        {
            try {
                TTransaction::open( 'afincco' );
                $object = Processo::find( $param[ 'id' ] );
                if ( $object ) {
                    $relatorio = new capaProcesso( $object );
                    if ( $relatorio ) {
                        parent::openFile( $relatorio->get_arquivo() );
                    }
                }
                TTransaction::close();
            } catch ( Exception $e ) {
                new TMessage( 'Erro', $e->getMessage() );
            }
        }
        
        public static function onEnvelopeProcesso( $param )
        {
            try {
                TTransaction::open( 'afincco' );
                $object = Processo::find( $param[ 'id' ] );
                if ( $object ) {
                    $relatorio = new envelopeProcesso( $object );
                    if ( $relatorio ) {
                        parent::openFile( $relatorio->get_arquivo() );
                    }
                }
                TTransaction::close();
            } catch ( Exception $e ) {
                new TMessage( 'Erro', $e->getMessage() );
            }
        }
        
        public static function onLaudoProcesso( $param )
        {
            try {
                TTransaction::open( 'afincco' );
                $object = Processo::find( $param[ 'id' ] );
                if ( $object ) {
                    $relatorio = new laudoProcesso( $object );
                    if ( $relatorio ) {
                        parent::openFile( $relatorio->get_arquivo() );
                    }
                }
                TTransaction::close();
            } catch ( Exception $e ) {
                new TMessage( 'Erro', $e->getMessage() );
            }
        }
        
        public static function onDecalqueProcesso( $param )
        {
            try {
                TTransaction::open( 'afincco' );
                $object = Processo::find( $param[ 'id' ] );
                if ( $object ) {
                    $relatorio = new decalqueProcesso( $object );
                    if ( $relatorio ) {
                        parent::openFile( $relatorio->get_arquivo() );
                    }
                }
                TTransaction::close();
            } catch ( Exception $e ) {
                new TMessage( 'Erro', $e->getMessage() );
            }
        }
        
        public static function onDevolucao( $param )
        {
            $form = new BootstrapFormBuilder( 'form_devolucao_'.__CLASS__ );
            
            $motivo = new TDBCombo( 'motivo', 'afincco', 'TextoDevolucao', 'id', 'nome', 'nome' );
            $texto  = new THtmlEditor( 'texto' );
            
            $actTexto = new TAction( [ __CLASS__, 'onSelecionaTexto' ], ['key' => $motivo] );
            $motivo->setChangeAction( $actTexto );
            
            $texto->setSize( 1024, 400 );
            
            $form->addFields( [ new TLabel( 'Textos de Modelo' ), $motivo ] );
            $form->addFields( [ new TLabel( 'Texto' ), $texto ] );
            
            $form->addAction( 'Gerar Carta', new TAction( [ __CLASS__, 'onGerarDevolucao' ], ['key' => $param['id']] ), 'fa:save green' );
            $form->addAction( 'Voltar', new TAction( [ __CLASS__, 'onSairDevolucao' ] ), 'far:check-circle blue' );
            
            // show the input dialog
            new TInputDialog( 'Gerar Carta Devolução', $form );
        }
        
        public static function onGerarDevolucao( $param )
        {
            try {
                TTransaction::open( 'afincco' );
                $object = Processo::find( $param[ 'key' ] );
                if ( $object ) {
                    $relatorio = new cartaDevolucao( $object, $param['texto'] );
                    if ( $relatorio ) {
                        parent::openFile( $relatorio->get_arquivo() );
                    }
                }
                TTransaction::close();
            } catch ( Exception $e ) {
                new TMessage( 'Erro', $e->getMessage() );
            }
        
        }
        
        public static function onSairDevolucao( $param )
        {
        
        }
        
        public static function onSelecionaTexto( $param )
        {
            if ( $param[ 'key' ] ) {
                try {
                    TTransaction::Open( 'afincco' );
                    $texto = TextoDevolucao::find( $param[ 'key' ] );
                    if ( $texto ) {
                        $data        = new StdClass();
                        $data->texto = str_replace( "\r\n", "", $param[ 'texto' ] );
                        $data->texto = str_replace( "<p>", "", $param[ 'texto' ] );
                        $data->texto = str_replace( "</p>", "", $param[ 'texto' ] );
                        if (strlen($data->texto) > 0) {
                            $data->texto .= '<br>';
                        }
                        $data->texto .= $texto->texto;
                        $data->motivo = ' ';
                        TForm::sendData( 'form_devolucao_'.__CLASS__, $data );
                    }
                    TTransaction::close();
                } catch ( Exception $e ) {
                    TTransaction::rollback();
                }
            }
        }
        
        public function displayColumn( $object )
        {
            if ( isset( $object->IdComprovante ) ) {
                TTransaction::open( 'afincco' );
                $teste = comprovante::find( $object->IdComprovante );
                if ( $teste ) {
                    if ( $teste->onVerificaTitulo() ) {
                        TTransaction::close();
                        return FALSE;
                    }
                    TTransaction::close();
                    return TRUE;
                }
            }
            if ( isset( $object->id ) ) {
                TTransaction::open( 'afincco' );
                $teste = recibos::find( $object->id );
                if ( $teste ) {
                    if ( $teste->onVerificaTitulo() ) {
                        TTransaction::close();
                        return FALSE;
                    }
                    TTransaction::close();
                    return TRUE;
                }
            }
            
            return TRUE;
        }
        
        public function displayCarimbo( $object )
        {
            if ( $object->tipoarq_id == '3' ) {
                if ( $object->assinado === '1' ) {
                    return FALSE;
                }
                return TRUE;
            }
            
            return FALSE;
        }
        
        public function displayExcluirArquivo( $object )
        {
            if ( $object->assinado === '1' ) {
                return FALSE;
            }
            
            return TRUE;
        }
        
        public function onEnviarEmailArquivo( $param )
        {
            $data = $this->form->getData();
            $this->form->setData( $data );
            AdiantiCoreApplication::loadPage( 'processoArqForm', 'onEdit', $param );
        }
        
        public function onAvaliacao( $param )
        {
            $this->form->setData( TSession::getValue( 'data' ) );
            $form        = new TQuickForm( 'input_form' );
            $form->style = 'padding:20px;width:900px;';
            $criteria    = new TCriteria();
            $filter      = new TFilter( 'liberacao', 'IS NOT', NULL );
            $criteria->add( $filter );
            
            $documentos = new TDBCheckGroup( 'documentos', 'afincco', 'tipoarquivo', 'liberacao', 'liberacao', 'liberacao', $criteria );
            
            $form->addQuickField( '', $documentos, 800 );
            
            $form->addQuickAction( 'Fechar', new TAction( [ $this, 'onSairAvaliacao' ] ), 'ico_apply.png' );
            
            new TInputDialog( 'Geração de Relatório de Avaliação', $form );
        }
        
        public function onSairAvaliacao( $param )
        {
            $data = TSession::getValue( 'Processoa_data' );
            $key  = $data->id;
            $this->form->setData( TSession::getValue( 'Processoa_data' ) );
            $this->documentos = $param[ 'documentos' ];
            try {
                TTransaction::open( 'afincco' );
                $object = Processo::find( $key );
                if ( $object ) {
                    $relatorio = new avaliacaoProcesso( $object, $this->documentos );
                    if ( $relatorio->gerarTermo() ) {
                        parent::openFile( $relatorio->get_arquivo() );
                    }
                }
                TTransaction::close();
            } catch ( Exception $e ) {
                new TMessage( 'Erro', $e->getMessage() );
            }
        }
        
        public function onDeleteComprovante( $param )
        {
            $action = new TAction( [ $this, 'DeleteComprovante' ] );
            $action->setParameters( $param );
            $this->onEdit( TSession::getValue( 'Processoa_param' ) );
            new TQuestion( AdiantiCoreTranslator::translate( 'Do you really want to delete ?' ), $action );
        }
        
        public function onEdit( $param )
        {
            
            TSession::setValue( 'Processoa_param', $param );
            TSession::delValue( 'Processoa_status' );
            TSession::delValue( 'Processoa_arquivos' );
            TSession::delValue( 'Processoa_comprovante' );
            TSession::delValue( 'Processoa_recibos' );
            TSession::delValue( 'Processoa_ocorrencias' );
            TSession::delValue( 'Processoa_financeiro' );
            TSession::delValue( 'Processoa_liberador' );
            TSession::delValue( 'Processoa_current_page' );
            
            try {
                if ( empty( $this->database ) ) {
                    throw new Exception( AdiantiCoreTranslator::translate( '^1 was not defined. You must call ^2 in ^3', AdiantiCoreTranslator::translate( 'Database' ), 'setDatabase()', AdiantiCoreTranslator::translate( 'Constructor' ) ) );
                }
                
                if ( empty( $this->activeRecord ) ) {
                    throw new Exception( AdiantiCoreTranslator::translate( '^1 was not defined. You must call ^2 in ^3', 'Active Record', 'setActiveRecord()', AdiantiCoreTranslator::translate( 'Constructor' ) ) );
                }
                
                if ( isset( $param[ 'key' ] ) ) {
                    
                    $key    = $param[ 'key' ];
                    $grupos = TSession::getValue( 'usergroupids' );
                    
                    TTransaction::open( $this->database );
                    
                    $grava_log = TRUE;
                    
                    if ( TSession::getValue( 'Processoa_ultimo_log' ) ) {
                        if ( TSession::getValue( 'Processoa_ultimo_log' ) == $param[ 'key' ] ) {
                            $grava_log = FALSE;
                        }
                    }
                    
                    /*
                    if ( $grava_log ) {
                        $ocorrencia = new Prococor();
                        
                        $ocorrencia->id_processo = $key;
                        $ocorrencia->usuario     = TSession::getValue( 'login' );
                        $ocorrencia->data_ocor   = date( 'Y-m-d H:i:s' );
                        $ocorrencia->historico   = 'Acessou consulta do processo';
                        $ocorrencia->store();
                        TSession::setValue( 'Processoa_ultimo_log', $param[ 'key' ] );
                    }
                    */
                    
                    $class            = $this->activeRecord;
                    $email_transporte = FALSE;
                    
                    $object               = new $class( $key );
                    $object->condChassi1  = $object->condChassi;
                    $object->condMotor1   = $object->condMotor;
                    $object->restricao021 = $object->restricao02;
                    
                    if ( in_array( '5', $grupos ) ) {
                        $codUsuario = TSession::getValue( 'userid' );
                        if ( $codUsuario !== $object->usuario ) {
                            if ( $object->liberadores->usuario !== $codUsuario ) {
                                throw new Exception( 'Processo não pertence a esse usuário, operação cancelada', $key );
                            }
                        }
                    }
                    
                    $sessao_status    = [];
                    $libera_devolucao = FALSE;
                    foreach ( $object->hstatus as $arquivo ) {
                        if ( $arquivo->id_status == 9 ) {
                            $libera_devolucao = TRUE;
                        }
                        $sessao_status[ $arquivo->id ] = $arquivo->toArray();
                    }
                    
                    $sessao_arquivos = [];
                    foreach ( $object->processo_arq as $arquivo ) {
                        if ( isset( $arquivo->id_arq ) ) {
                            $libera = FALSE;
                            if ( in_array( '5', $grupos ) ) {
                                if ( $arquivo->tipoarq_id < 16 ) {
                                    $libera = TRUE;
                                }
                                if ( $arquivo->tipoarq_id == 22 ) {
                                    $libera = TRUE;
                                }
                            } else {
                                if ( $arquivo->tipoarq_id == 2 ) {
                                    if ( !in_array( '4', $grupos ) ) {
                                        $libera = TRUE;
                                    }
                                } else {
                                    $libera = TRUE;
                                }
                            }
                            
                            if ( $libera ) {
                                $sessao_arquivos[ $arquivo->id_arq ] = $arquivo->toArray();
                                if ( $arquivo->tipoarq_id == 14 ) {
                                    $email_transporte = $arquivo->Tipoarquivo->nome;
                                }
                            }
                        }
                    }
                    
                    $sessao_comprovantes = [];
                    foreach ( $object->comprovante as $arquivo ) {
                        if ( isset( $arquivo->IdComprovante ) ) {
                            $sessao_comprovantes[ $arquivo->IdComprovante ] = $arquivo->toArray();
                        }
                    }
                    
                    $sessao_ocorrencias = [];
                    foreach ( $object->ocorrencias as $ocorrencia ) {
                        if ( isset( $ocorrencia->id ) ) {
                            $sessao_ocorrencias[ $ocorrencia->id ] = $ocorrencia->toArray();
                        }
                    }
                    
                    $sessao_recibos = [];
                    foreach ( $object->recibos as $recibo ) {
                        if ( isset( $recibo->id ) ) {
                            $libera = FALSE;
                            if ( in_array( '5', $grupos ) ) {
                                if ( $recibo->pessoa_id == TSession::getValue( 'LIBERADOR' ) ) {
                                    $libera = TRUE;
                                }
                            } else {
                                $libera = TRUE;
                            }
                            
                            if ( $libera ) {
                                $sessao_recibos[ $recibo->id ]                  = $recibo->toArray();
                                $sessao_recibos[ $recibo->id ][ 'pessoa_nome' ] = $recibo->pessoa->nome;
                            }
                        }
                    }
                    
                    $sessao_financeiro = [];
                    foreach ( $object->financeiro as $financeiro ) {
                        if ( isset( $financeiro->titulo_id ) ) {
                            if ( $this->mostra_financeiro ) {
                                $sessao_financeiro[ $financeiro->titulo_id ] = $financeiro->toArray();
                                if ( $financeiro->pagar_receber == 'P' ) {
                                    $object->total_pagar    += $financeiro->valor;
                                    $object->pendente_pagar += $financeiro->saldo;
                                }
                                if ( $financeiro->pagar_receber == 'R' ) {
                                    $object->total_receber    += $financeiro->valor;
                                    $object->pendente_receber += $financeiro->saldo;
                                }
                                $object->total_saldo    = $object->total_receber - $object->total_pagar;
                                $object->pendente_saldo = $object->pendente_receber - $object->pendente_pagar;
                            }
                        }
                    }
                    
                    TSession::setValue( 'Processoa_status', $sessao_status );
                    TSession::setValue( 'Processoa_arquivos', $sessao_arquivos );
                    TSession::setValue( 'Processoa_comprovante', $sessao_comprovantes );
                    TSession::setValue( 'Processoa_ocorrencias', $sessao_ocorrencias );
                    TSession::setValue( 'Processoa_recibos', $sessao_recibos );
                    TSession::setValue( 'Processoa_financeiro', $sessao_financeiro );
                    
                    if ( !$email_transporte ) {
                        $texto_email              = new emailTransporte( $object, FALSE, TRUE );
                        $object->texto_transporte = $texto_email->gerarEmail( TRUE );
                    } else {
                        $object->texto_transporte = 'E-Mail ja gerado, verifique na ABA arquivos....';
                    }
                    
                    if ( $object->liberador > 0 ) {
                        TSession::setValue( 'Processoa_liberador', $object->liberador );
                        if ( !in_array( '1', $grupos ) ) {
                            if ( !in_array( '2', $grupos ) ) {
                                if ( !in_array( '3', $grupos ) ) {
                                    TScript::create( "window.setTimeout(function(){
                            $('select[name=\"liberador\"]').attr('disabled',true);
                         },100);" );
                                }
                            }
                        }
                    }
                    
                    $this->form->setData( $object );
                    
                    TSession::setValue( 'Processoa_data', $object );
                    
                    if ( !$libera_devolucao ) {
                        TScript::create( '$(\'#tab_3\').hide();' );
                    }
                    if ( $object->valida_status_final() ) {
                        TButton::disableField( 'form_'.__CLASS__, 'btn_salvar' );
                        TButton::disableField( 'form_'.__CLASS__, 'novo_recibo' );
                        TButton::disableField( 'form_'.__CLASS__, 'novo_recibos' );
                    }
                    $this->onReload( $param );
                    return $object;
                } else {
                    TSession::delValue( 'Processoa_liberador' );
                    $this->form->clear();
                    $object                     = new StdClass();
                    $object->data_cadastro      = date( 'd/m/Y H:i:s' );
                    $object->usuario            = TSession::getValue( 'userid' );
                    $object->tipo_liberacao_dev = 1;
                    $this->form->setData( $object );
                    TSession::setValue( 'data', $object );
                }
            } catch ( Exception $e ) {
                TSession::delValue( 'liberador' );
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();
            } finally {
                TTransaction::close();
            }
        }
        
        function onReload( $param )
        {
            $arquivos     = TSession::getValue( 'Processoa_arquivos' );
            $status       = TSession::getValue( 'Processoa_status' );
            $comprovantes = TSession::getValue( 'Processoa_comprovante' );
            $ocorrencias  = TSession::getValue( 'Processoa_ocorrencias' );
            $recibos      = TSession::getValue( 'Processoa_recibos' );
            $financeiro   = TSession::getValue( 'Processoa_financeiro' );
            
            $this->gridArquivos->clear();
            $this->gridstatus->clear();
            $this->gridComprovante->clear();
            $this->gridOcorrencia->clear();
            $this->gridRecibos->clear();
            $this->gridFinanceiro->clear();
            
            if ( $arquivos ) {
                
                foreach ( $arquivos as $cod_arq => $arquivo ) {
                    $item = new ProcessoArq();
                    $item->fromArray( $arquivo );
                    
                    $row              = $this->gridArquivos->addItem( $item );
                    $row->onmouseover = '';
                    $row->onmouseout  = '';
                }
            }
            
            if ( $status ) {
                foreach ( $status as $cod_status => $status ) {
                    $item = new Hstatus();
                    $item->fromArray( $status );
                    
                    $row              = $this->gridstatus->addItem( $item );
                    $row->onmouseover = '';
                    $row->onmouseout  = '';
                }
            }
            
            if ( $comprovantes && $this->mostra_recibo ) {
                foreach ( $comprovantes as $cod_comprovante => $comprovante ) {
                    $item = new comprovante();
                    $item->fromArray( $comprovante );
                    
                    $row              = $this->gridComprovante->addItem( $item );
                    $row->onmouseover = '';
                    $row->onmouseout  = '';
                }
            }
            
            if ( $ocorrencias ) {
                arsort( $ocorrencias );
                foreach ( $ocorrencias as $cod_ocorrencia => $ocorrencia ) {
                    $item = new prococor();
                    $item->fromArray( $ocorrencia );
                    
                    $row              = $this->gridOcorrencia->addItem( $item );
                    $row->onmouseover = '';
                    $row->onmouseout  = '';
                }
            }
            
            if ( $recibos && $this->mostra_recibo_prestador ) {
                foreach ( $recibos as $cod_recibo => $recibo ) {
                    $item = new recibos();
                    $item->fromArray( $recibo );
                    
                    $row              = $this->gridRecibos->addItem( $item );
                    $row->onmouseover = '';
                    $row->onmouseout  = '';
                }
            }
            if ( $financeiro && $this->mostra_financeiro ) {
                foreach ( $financeiro as $cod_financeiro => $dados ) {
                    $item = new viewFinanceiroProcesso();
                    $item->fromArray( $dados );
                    
                    $row              = $this->gridFinanceiro->addItem( $item );
                    $row->onmouseover = '';
                    $row->onmouseout  = '';
                }
            }
            
            $this->form->setData( TSession::getValue( 'Processoa_data' ) );
            
            if ( TSession::getValue( 'Processoa_current_page' ) ) {
                $this->form->setCurrentPage( TSession::getValue( 'Processoa_current_page' ) );
            }
            $this->loaded = TRUE;
        }
        
        public function onSave()
        {
            try {
                if ( empty( $this->database ) ) {
                    throw new Exception( AdiantiCoreTranslator::translate( '^1 was not defined. You must call ^2 in ^3', AdiantiCoreTranslator::translate( 'Database' ), 'setDatabase()', AdiantiCoreTranslator::translate( 'Constructor' ) ) );
                }
                
                if ( empty( $this->activeRecord ) ) {
                    throw new Exception( AdiantiCoreTranslator::translate( '^1 was not defined. You must call ^2 in ^3', 'Active Record', 'setActiveRecord()', AdiantiCoreTranslator::translate( 'Constructor' ) ) );
                }
                
                $grupos = TSession::getValue( 'usergroupids' );
                
                TTransaction::open( $this->database );
                
                $object = $this->form->getData( $this->activeRecord );
                
                if ( TSession::getValue( 'Processoa_liberador' ) ) {
                    if ( !in_array( '1', $grupos ) ) {
                        if ( !in_array( '2', $grupos ) ) {
                            if ( !in_array( '3', $grupos ) ) {
                                $object->liberador = TSession::getValue( 'Processoa_liberador' );
                            }
                        }
                    }
                }
                
                $valida_devolucao = FALSE;
                
                if ( !empty( $object->cidade_dev ) ) {
                    $valida_devolucao = TRUE;
                }
                if ( !empty( $object->uf_dev ) ) {
                    $valida_devolucao = TRUE;
                }
                if ( !empty( $object->dp_dev ) ) {
                    $valida_devolucao = TRUE;
                }
                if ( !empty( $object->chassi_adulterado_dev ) ) {
                    $valida_devolucao = TRUE;
                }
                if ( !empty( $object->data_entrega_dev ) ) {
                    $valida_devolucao = TRUE;
                }
                if ( !empty( $object->bo_dev ) ) {
                    $valida_devolucao = TRUE;
                }
                if ( !empty( $object->telefone_dev ) ) {
                    $valida_devolucao = TRUE;
                }
                if ( !empty( $object->responsavel_dev ) ) {
                    $valida_devolucao = TRUE;
                }
                if ( !empty( $object->local_entrega_dev ) ) {
                    $valida_devolucao = TRUE;
                }
                if ( !empty( $object->obs_dev ) ) {
                    $valida_devolucao = TRUE;
                }
                
                if ( $valida_devolucao ) {
                    $valida1 = new TRequiredValidator();
                    $valida1->validate( 'Condição do Motor', $object->condMotor );
                    $valida1->validate( 'Condição do Chassi', $object->condChassi );
                    $valida1->validate( 'Estado do Bem', $object->restricao02 );
                }
                
                $this->form->validate();
                
                $enviar_email = FALSE;
                
                if ( empty( $object->id ) ) {
                    $enviar_email          = TRUE;
                    $object->representante = TSession::getValue( 'username' );
                } else {
                    $object->gestor = TSession::getValue( 'userid' );
                }
                
                if ( empty( $object->usuario ) ) {
                    $object->usuario = TSession::getValue( 'userid' );
                }
                if ( empty( $object->representante ) ) {
                    $object->representante = TSession::getValue( 'username' );
                }
                
                $object->store();
                
                $this->form->setData( $object );
                $object->seguradoras;
                
                new TMessage( 'info', AdiantiCoreTranslator::translate( 'Record saved' ) );
                
                if ( $enviar_email ) {
                    $enviado = new emailEntrada( $object, TRUE );
                }
                
                if ( $object->liberador > 0 ) {
                    if ( !in_array( '1', $grupos ) ) {
                        if ( !in_array( '2', $grupos ) ) {
                            if ( !in_array( '3', $grupos ) ) {
                                TScript::create( "window.setTimeout(function(){
                            $('select[name=\"liberador\"]').attr('disabled',true);
                         },100);" );
                                TSession::setValue( 'liberador', $object->liberador );
                            }
                        }
                    }
                }
                
                return $object;
            } catch ( Exception $e ) {
                $object = $this->form->getData();
                
                $this->form->setData( $object );
                
                new TMessage( 'error', $e->getMessage() );
                
                TTransaction::rollback();
            } finally {
                TTransaction::close();
            }
        }
        
        public function onDeleteRecibos( $param )
        {
            $action = new TAction( [ $this, 'DeleteRecibos' ] );
            $action->setParameters( $param );
            $this->onEdit( TSession::getValue( 'Processoa_param' ) );
            new TQuestion( AdiantiCoreTranslator::translate( 'Do you really want to delete ?' ), $action );
        }
        
        public function DeleteComprovante( $param )
        {
            try {
                $data = TSession::getValue( 'Processoa_data' );
                
                $key = $param[ 'key' ];
                TTransaction::open( 'afincco' );
                Despesa::where( 'IdComprovante', '=', $key )->delete();
                Comprovante::find( $key )->delete();
                
                $comprovantes = TSession::getValue( 'Processoa_comprovante' );
                unset( $comprovantes[ (int)$param[ 'key' ] ] );
                TSession::setValue( 'Processoa_comprovante', $comprovantes );
                
                $param          = [];
                $param[ 'key' ] = $data->id;
                $param[ 'id' ]  = $data->id;
                $this->onReload( $param );
                
                $this->form->setCurrentPage( 6 );
                
                new TMessage( 'info', AdiantiCoreTranslator::translate( 'Record deleted' ) );
            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();
            } finally {
                TTransaction::close();
            }
        }
        
        public function DeleteRecibos( $param )
        {
            try {
                $data = TSession::getValue( 'Processoa_data' );
                
                $key = $param[ 'key' ];
                TTransaction::open( 'afincco' );
                Recibos::find( $key )->delete();
                
                $recibos = TSession::getValue( 'Processoa_recibos' );
                unset( $recibos[ (int)$param[ 'key' ] ] );
                TSession::setValue( 'Processoa_recibos', $recibos );
                
                $param          = [];
                $param[ 'key' ] = $data->id;
                $param[ 'id' ]  = $data->id;
                $this->onReload( $param );
                
                $this->form->setCurrentPage( 9 );
                
                new TMessage( 'info', AdiantiCoreTranslator::translate( 'Record deleted' ) );
            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();
            } finally {
                TTransaction::close();
            }
        }
        
        public function onExcluirArquivo( $param )
        {
            try {
                TTransaction::open( 'afincco' );
                $data = TSession::getValue( 'Processoa_data' );
                
                $arquivo = ProcessoArq::find( $param[ 'id_arq' ] );
                
                if ( $arquivo->tipoarq_id == 3 ) {
                    $object                 = Processo::find( $data->id );
                    $texto_email            = new emailTransporte( $object, FALSE, TRUE );
                    $data->texto_transporte = $texto_email->gerarEmail( TRUE );
                    
                    $sts_sessao = TSession::getValue( 'Processoa_status' );
                    
                    foreach ( $sts_sessao as $status ) {
                        if ( $status[ 'id_status' ] == 10 ) {
                            unset( $sts_sessao[ $status[ 'id' ] ] );
                            $sts = new Hstatus();
                            $sts->delete( $status[ 'id' ] );
                        }
                    }
                    
                    TSession::setValue( 'Processoa_status', $sts_sessao );
                }
                
                $arquivo->delete();
                
                $arquivos = TSession::getValue( 'Processoa_arquivos' );
                unset( $arquivos[ (int)$param[ 'id_arq' ] ] );
                TSession::setValue( 'Processoa_arquivos', $arquivos );
                
                $this->form->setData( $data );
                
                $param          = [];
                $param[ 'key' ] = $data->id;
                $param[ 'id' ]  = $data->id;
                $this->onReload( $param );
                
                $this->form->setCurrentPage( 5 );
                
            } catch ( Exception $e ) {
                $this->form->setData( $this->form->getData() );
                new TMessage( 'Erro', $e->getMessage() );
            } finally {
                TTransaction::close();
            }
        }
        
        public function onEditarArquivo( $param )
        {
            try {
                TTransaction::open( 'afincco' );
                $data     = TSession::getValue( 'Processoa_data' );
                $arquivos = TSession::getValue( 'Processoa_arquivos' );
                
                if ( !$data ) {
                    $data = new StdClass();
                }
                
                $arquivo = $arquivos[ (int)$param[ 'id_arq' ] ];
                
                $data->id_arq     = $arquivo[ 'id_arq' ];
                $data->nome       = $arquivo[ 'nome' ];
                $data->tipoarq_id = $arquivo[ 'tipoarq_id' ];
                $origem           = "app/arquivos/".$arquivo[ 'id_processo' ]."/".$data->nome;
                
                if ( !file_exists( $origem ) ) {
                    $origem = "app/arquivos/".$data->nome;
                }
                
                if ( strtoupper( substr( $data->nome, -3 ) ) == "PDF" ) {
                    TScript::create( "$('#arq_frame').html('')" );
                    TScript::create( "$('#arq_frame').append(\"<object type='application/pdf'  data='$origem'  width='528' height='600' ></object>\")" );
                } else {
                    TScript::create( "$('#arq_frame').html('')" );
                    TScript::create( "$('#arq_frame').append(\"<img style='width:528px;height:480px;' src='$origem'>\");" );
                }
                
                TSession::setValue( 'Processoa_data', $data );
                $this->form->setData( $data );
                TSession::setValue( 'Processoa_current_page', '5' );
                $param          = [];
                $param[ 'key' ] = $data->id;
                $param[ 'id' ]  = $data->id;
                $this->onReload( $param );
                $this->form->setCurrentPage( 5 );
            } catch ( Exception $e ) {
                new TMessage( 'error', 'Erro ao Abrir arquivo - '.$e->getMessage() );
            } finally {
                TTransaction::close();
            }
        }
        
        public function onInputDialog( $param )
        {
            $data = $this->form->getData();
            
            TSession::setValue( 'data', $data );
            
            $form_arq        = new TQuickForm( 'input_form' );
            $form_arq->style = 'padding:20px';
            
            $processo   = new TEntry( 'processo' );
            $aviso      = new TLabel( 'Os Arquivos a serem importados não devem conter espaços em branco no nome.' );
            $tipoarq_id = new TDBCombo( 'tipoarq_id', 'afincco', 'Tipoarquivo', 'id', 'nome', 'nome' );
            $Arquivo    = new TMultiFile( 'Arquivo' );
            $Arquivo->setAllowedExtensions( [ 'jpg', 'jpeg', 'pdf', 'png' ] );
            
            $processo->setEditable( FALSE );
            $processo->setValue( $data->id );
            
            $aviso->style = 'color:red;text-align:justify;';
            
            $form_arq->addQuickField( 'Processo', $processo );
            $form_arq->addQuickField( 'Tipo de Arquivo', $tipoarq_id );
            $form_arq->addQuickField( 'Arquivo', $Arquivo );
            $form_arq->addQuickField( '', $aviso );
            
            TScript::create( "$('.close').hide();" );
            
            $form_arq->addQuickAction( 'Salvar', new TAction( [ $this, 'onConfirm1' ] ), 'fa:save blue' );
            $form_arq->addQuickAction( 'Cancelar', new TAction( [ $this, 'onReload' ] ), 'fas:times-circle red' );
            
            new TInputDialog( 'Importar Novos Arquivos', $form_arq );
            
            $this->form->setData( $data );
            $this->form->setCurrentPage( 5 );
        }
        
        public function onConfirm1( $param )
        {
            $arquivos = TSession::getValue( 'Processoa_arquivos' );
            $tipoarq  = $param[ 'tipoarq_id' ];
            
            foreach ( $param as $chave => $obj ) {
                
                if ( $chave == "processo" ) {
                    $codigo_processo = $obj;
                }
                if ( $chave == "Arquivo" ) {
                    
                    TTransaction::open( 'afincco' );
                    try {
                        foreach ( $obj as $arq => $valor ) {
                            if ( !empty( $valor ) ) {
                                $source_file = 'tmp/'.$valor;
                                $destino     = str_replace( ' ', '', $source_file );
                                if ( $destino !== $source_file ) {
                                    rename( $source_file, $destino );
                                    $source_file = $destino;
                                }
                                
                                $final        = explode( ".", $source_file );
                                $extensao     = strtolower( end( $final ) );
                                $novo_arquivo = date( 'Ymd' ).'_'.rand( 1, 99999 ).'_'.$codigo_processo.".".$extensao;
                                $destino      = 'app/arquivos';
                                if ( !file_exists( $destino ) ) {
                                    if ( !mkdir( $destino, 0777 ) && !is_dir( $destino ) ) {
                                        throw new RuntimeException( sprintf( 'Diretorio "%s" não pode ser criado', $destino ) );
                                    }
                                }
                                $destino .= '/'.$codigo_processo."/";
                                if ( !file_exists( $destino ) ) {
                                    if ( !mkdir( $destino, 0777 ) && !is_dir( $destino ) ) {
                                        throw new RuntimeException( sprintf( 'Diretorio "%s" não pode ser criado', $destino ) );
                                    }
                                }
                                
                                if ( $extensao == "pdf" ) {
                                    $retorno = Utilidades::juntar_pdf( [ $source_file ] );
                                    rename( $retorno, $source_file );
                                }
                                
                                $target_file = $destino.$novo_arquivo;
                                
                                if ( copy( $source_file, $target_file ) ) {
                                    $arquivo              = new ProcessoArq();
                                    $arquivo->id_processo = $codigo_processo;
                                    $arquivo->nome        = $novo_arquivo;
                                    $arquivo->data_arq    = date( 'Y-m-d H:i:s' );
                                    $arquivo->usuario     = TSession::getValue( 'login' );
                                    $arquivo->tipoarq_id  = $tipoarq;
                                    $arquivo->assinado    = 0;
                                    $arquivo->token       = NULL;
                                    $arquivo->hash        = NULL;
                                    
                                    $arquivo->store();
                                    $arquivo->get_Tipoarquivo();
                                    
                                    $arquivos[ $arquivo->id_arq ] = $arquivo->toArray();
                                    
                                    if ( file_exists( $target_file ) ) {
                                        if ( preg_match( "/(png|jpg|jpeg)$/i", $target_file ) ) {
                                            $f1 = new Imagick();
                                            $f1->setOption( 'jpeg:size', '640x480' );
                                            $f1->readImage( $target_file );
                                            $f1->setImageCompressionQuality( 50 );
                                            $f1->writeImage( $target_file );
                                        }
                                    }
                                } else {
                                    new TMessage( 'error', 'de'.$source_file.' para'.$target_file );
                                }
                            }
                        }
                        TSession::setValue( 'Processoa_arquivos', $arquivos );
                        $param          = [];
                        $param[ 'key' ] = $codigo_processo;
                        $param[ 'id' ]  = $codigo_processo;
                        $this->onReload( $param );
                        $this->form->setCurrentPage( 5 );
                    } catch ( Exception $e ) {
                        new TMessage( 'error', $e->getMessage() );
                        TTransaction::rollback();
                    } finally {
                        TTransaction::close();
                    }
                }
            }
        }
        
        public function onAddStatus( $param )
        {
            $data = $this->form->getData();
            if ( $param[ 'status' ] == 9 or $param[ 'status' ] == 10 ) {
                if ( empty( $param[ 'cidade_dev' ] ) or empty( $param[ 'uf_dev' ] ) or empty( $param[ 'dp_dev' ] ) or empty( $param[ 'restricao021' ] ) or empty( $param[ 'data_entrega_dev' ] ) or empty( $param[ 'bo_dev' ] ) or empty( $param[ 'telefone_dev' ] ) or empty( $param[ 'responsavel_dev' ] ) or empty( $param[ 'local_entrega_dev' ] ) or empty( $param[ 'tipo_liberacao_dev' ] ) or empty( $param[ 'condChassi1' ] ) or empty( $param[ 'condMotor1' ] ) ) {
                    new TMessage( 'error', 'Por favor informe todos os dados da ABA DEVOLUÇÃO' );
                    $this->form->setData( $data );
                    $this->form->setCurrentPage( 3 );
                    return FALSE;
                }
            }
            
            try {
                $codigo_processo = $param[ 'id' ];
                $id              = $param[ 'status' ];
                $data            = $this->form->getData();
                $sts_sessao      = TSession::getValue( 'Processoa_status' );
                
                if ( !empty( $id ) ) {
                    TTransaction::open( 'afincco' );
                    
                    $object                = new Hstatus();
                    $object->id_status     = $id;
                    $object->id_processo   = $param[ 'id' ];
                    $object->representante = TSession::getValue( 'login' );
                    $object->data_cadastro = date( 'Y-m-d H:i:s' );
                    $object->store();
                    
                    $key                = (int)$object->id;
                    $sts_sessao[ $key ] = [ 'id'            => $object->id, 'id_status' => $object->id_status,
                                            'id_processo'   => $object->id_processo,
                                            'representante' => $object->representante,
                                            'data_cadastro' => $object->data_cadastro,
                                            'nome'          => $object->get_status()->statu ];
                    
                    TSession::setValue( 'Processoa_status', $sts_sessao );
                    TSession::setValue( 'Processoa_current_page', '4' );
                    
                    $data->status = ' ';
                    $this->form->setData( $data );
                    $this->form->setCurrentPage( 4 );
                    
                    if ( $id == '07' ) {
                        $this->onEmail_Juridico( $param );
                    }
                    if ( $id == '09' ) {
                        $this->onEmail_Liberacao( $param );
                    }
                    if ( $id == '16' ) {
                        $this->onEmail_Pagamento( $param );
                    }
                    
                    if ( $id == '17' ) {
                        $this->onEmail_Pagamento( $param );
                    }
                    if ( $id == '18' ) {
                        $this->onEmail_Pagamento( $param );
                    }
                    if ( $object->get_status()->status_final == 1 ) {
                        TButton::disableField( 'form_'.__CLASS__, 'btn_salvar' );
                        TButton::disableField( 'form_'.__CLASS__, 'novo_recibo' );
                        TButton::disableField( 'form_'.__CLASS__, 'novo_recibos' );
                    }
                    
                    
                    $param          = [];
                    $param[ 'key' ] = $codigo_processo;
                    $param[ 'id' ]  = $codigo_processo;
                    $this->onReload( $param );
                }
                
            } catch ( Exception $e ) {
                TTransaction::rollback();
                new TMessage( 'error', $e->getMessage() );
            } finally {
                TTransaction::close();
            }
        }
        
        public static function onEmail_Juridico( $param )
        {
            try {
                TTransaction::open( 'afincco' );
                $object = Processo::find( $param[ 'id' ] );
                $object->seguradoras;
                new emailJuridico( $object, $param[ 'status' ] );
            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();
            } finally {
                TTransaction::close();
            }
        }
        
        public static function onEmail_Liberacao( $param )
        {
            try {
                TTransaction::open( 'afincco' );
                $object = Processo::find( $param[ 'id' ] );
                $object->seguradoras;
                new emailLiberado( $object );
            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();
            } finally {
                TTransaction::close();
            }
        }
        
        public static function onEmail_Pagamento( $param )
        {
            try {
                TTransaction::open( 'afincco' );
                $object = Processo::find( $param[ 'id' ] );
                $object->seguradoras;
                new emailPagamento( $object, $param[ 'status' ] );
            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();
            } finally {
                TTransaction::close();
            }
        }
        
        public function onSalvarOcor( $param )
        {
            try {
                $data            = $this->form->getData();
                $codigo_processo = $param[ 'id' ];
                
                if ( !empty( $codigo_processo ) ) {
                    TTransaction::open( 'afincco' );
                    
                    $object              = new Prococor();
                    $object->id_processo = $codigo_processo;
                    $object->usuario     = TSession::getValue( 'login' );
                    $object->data_ocor   = date( 'Y-m-d H:i:s' );
                    $object->historico   = $param[ 'ocorrencia' ];
                    $object->store();
                    
                    $ocorrencias = TSession::getValue( 'Processoa_ocorrencias' );
                    
                    $key                 = (int)$object->id;
                    $ocorrencias[ $key ] = [ 'id'        => $object->id, 'id_processo' => $object->id_processo,
                                             'usuario'   => $object->usuario, 'data_ocor' => $object->data_ocor,
                                             'historico' => $object->historico ];
                    
                    TSession::setValue( 'Processoa_ocorrencias', $ocorrencias );
                    TSession::setValue( 'Processoa_current_page', '8' );
                    
                    $data->ocorrencia = ' ';
                    $this->form->setData( $data );
                    $this->form->setCurrentPage( 8 );
                    
                    $param          = [];
                    $param[ 'key' ] = $codigo_processo;
                    $param[ 'id' ]  = $codigo_processo;
                    $this->onReload( $param );
                }
                
            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );
            } finally {
                TTransaction::close();
            }
        }
    }
