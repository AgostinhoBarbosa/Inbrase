<?php

	class procuracaoCarimboForm extends TWindow {
		protected $form; // form

		public function __construct( $param ) {
			parent::__construct();
			parent::setTitle( 'Assinatura Digital' );
			parent::setSize( 0.5, NULL );

			// creates the form
			$this->form        = new TQuickForm( 'form_assina' );
			$this->form->class = 'tform'; // change CSS class
			$this->form        = new BootstrapFormWrapper( $this->form );
			$this->form->style = 'display: table;width:100%'; // change style

			// define the form title
			$this->form->setFormTitle( 'Assinatura Digital' );

			// create the form fields
			$nome        = new TEntry( 'nome' );
			$id_arq      = new THidden( 'id_arq' );
			$id_processo = new THidden( 'id_processo' );
			$data_arq    = new THidden( 'data_arq' );
			$usuario     = new THidden( 'usuario' );
			$tipoarq_id  = new THidden( 'tipoarq_id' );
			$assinado    = new THidden( 'assinado' );
			$token       = new THidden( 'token' );
			$quemAssina  = new TCombo( 'quemAssina' );

			$quemAssina->addItems(Utilidades::quem_assina());

			// add the fields
			$this->form->addQuickField( 'Arquivo', $nome, 300 );
			$this->form->addQuickField( 'Código', $id_arq, 100 );
			$this->form->addQuickField( 'Processo', $id_processo, 100 );
			$this->form->addQuickField( 'Data', $data_arq, 100 );
			$this->form->addQuickField( 'Usuario', $usuario, 100 );
			$this->form->addQuickField( 'Tipo Arquivo', $tipoarq_id, 100 );
			$this->form->addQuickField( 'Token', $token, 100 );
			$this->form->addQuickField( 'Carimbado', $assinado, 100 );
			$this->form->addQuickField( 'Quem Assina', $quemAssina, 100 );

			$id_arq->setEditable( FALSE );

			// create the form actions
			$this->form->addQuickAction( 'Carimbar', new TAction( array( $this, 'onSave' ) ), 'fa:floppy-o' );
			$this->form->addQuickAction( 'Retorna', new TAction( array( $this, 'onRetorna' ) ), 'fa:table blue' );

			// vertical box container
			$container        = new TVBox;
			$container->style = 'width: 100%';
			$container->add( TPanelGroup::pack( '', $this->form ) );
			parent::add( $container );
		}

        public function onRetorna($param) {
            parent::closeWindow();
            $retorno = [];
            $retorno['key'] = $param['id_processo'];
            TApplication::loadPage('processoaForm', 'onReload', $retorno);
        }

        public function onSave( $param ) {
			try {

				$data = $this->form->getData();

				if ( $data->assinado == 0 ) {
					if ( strtoupper( substr( $data->nome, - 3 ) ) == "PDF" ) {
						TTransaction::open( 'afincco' );
						$objeto  = processoArq::find( $data->id_arq );
						$origem  = "app/arquivos/" . $data->id_processo . "/" . $data->nome;
						//$destino = "app/arquivos/" . $data->id_processo . "/car_" . $data->nome;
						TSession::setValue( 'arquivo_carimbo', $_SERVER[ 'DOCUMENT_ROOT' ] . "/".$origem );
						TSession::setValue( 'quem_assina', $data->quemAssina );
						include_once( 'app/lib/lacuna/pades-signature-server-key.php' );

						$objeto->assinado = 1;

						//if ( rename( $origem, $destino ) ) {
						//	$objeto->nome = "car_" . $data->nome;
						//}

						$objeto->store();
						$data->assinado = $objeto->assinado;
						TTransaction::close();
						new TMessage( 'info', 'Documento assinado digitalmente, processo finalizado.' );
					} else {
						new TMessage( 'error', 'Somente arquivo PDF por der assinado digitalmente.' );
					}
				} else {
					new TMessage( 'error', 'Documento ja foi assinado digitalmente.' );
				}
				$this->form->setData( $data );
			} catch ( Exception $e ) // in case of exception
			{
				new TMessage( 'error', $e->getMessage() ); // shows the exception error message
				TTransaction::rollback(); // undo all pending operations
			}
		}

		/**
		 * Clear form data
		 *
		 * @param $param Request
		 */
		public function onClear( $param ) {
			$this->form->clear( TRUE );
		}

		/**
		 * Load object to form data
		 *
		 * @param $param Request
		 */
		public function onEdit( $param ) {
			try {
				if ( isset( $param[ 'key' ] ) ) {
					$key = $param[ 'key' ];  // get the parameter $key
					TTransaction::open( 'afincco' ); // open a transaction
					$object = new processoArq( $key ); // instantiates the Active Record
					$this->form->setData( $object ); // fill the form
					TTransaction::close(); // close the transaction
				} else {
					$this->form->clear( TRUE );
				}
			} catch ( Exception $e ) // in case of exception
			{
				new TMessage( 'error', $e->getMessage() ); // shows the exception error message
				TTransaction::rollback(); // undo all pending operations
			}
		}
	}
