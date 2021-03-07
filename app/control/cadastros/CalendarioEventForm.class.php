<?php
    
    use Adianti\Base\TStandardForm;
    use Adianti\Widget\Form\TColor;
    use Adianti\Widget\Form\TCombo;
    use Adianti\Widget\Form\TDate;
    use Adianti\Widget\Form\TEntry;
    use Adianti\Widget\Form\THidden;
    use Adianti\Widget\Form\TText;
    use Adianti\Wrapper\BootstrapFormBuilder;
    
    class CalendarioEventForm extends TStandardForm
    {
        public function __construct() {
            parent::__construct();

            parent::setTargetContainer( 'adianti_right_panel' );

            $this->setDatabase( 'afincco' );
            $this->setActiveRecord( 'Calendario' );

            $this->form = new BootstrapFormBuilder( 'form_'.__CLASS__ );
            $this->form->setFieldSizes( '100%' );
            $this->form->setFormTitle( 'Calendário' );

            $view         = new THidden( 'view' );
            $id           = new TEntry( 'id' );
            $color        = new TColor( 'color' );
            $start_date   = new TDate( 'start_date' );
            $hora_inicio  = new TCombo( 'hora_inicio' );
            $start_minute = new TCombo( 'start_minute' );
            $end_date     = new TDate( 'end_date' );
            $hora_final   = new TCombo( 'hora_final' );
            $end_minute   = new TCombo( 'end_minute' );
            $title        = new TEntry( 'title' );
            $description  = new TText( 'description' );

            $color->setValue( '#3a87ad' );

            $start_date->setMask( 'dd/mm/yyyy' );
            $end_date->setMask( 'dd/mm/yyyy' );
            $start_date->setDatabaseMask( 'yyyy-mm-dd' );
            $end_date->setDatabaseMask( 'yyyy-mm-dd' );
            
            $hours   = [];
            $minutes = [];
            for ( $n = 0; $n < 24; $n++ ) {
                $hours[ $n ] = str_pad( $n, 2, '0', STR_PAD_LEFT );
            }

            for ( $n = 0; $n <= 55; $n += 5 ) {
                $minutes[ $n ] = str_pad( $n, 2, '0', STR_PAD_LEFT );
            }

            $hora_inicio->addItems( $hours );
            $start_minute->addItems( $minutes );
            $hora_final->addItems( $hours );
            $end_minute->addItems( $minutes );

            $id->setEditable( FALSE );

            $hora_inicio->setChangeAction( new TAction( [ $this, 'onChangeStartHour' ] ) );
            $hora_final->setChangeAction( new TAction( [ $this, 'onChangeEndHour' ] ) );
            $start_date->setExitAction( new TAction( [ $this, 'onChangeStartDate' ] ) );
            $end_date->setExitAction( new TAction( [ $this, 'onChangeEndDate' ] ) );

            $campo_codigo     = [ new TLabel( 'ID: ' ), $id ];
            $campo_cor        = [ new TLabel( 'Cor: ' ), $color ];
            $campo_data_ini   = [ new TLabel( 'Data Inicial: ' ), $start_date ];
            $campo_hora_ini   = [ new TLabel( 'Hora Inicial: ' ), $hora_inicio ];
            $campo_minuto_ini = [ new TLabel( 'Minuto Inicial: ' ), $start_minute ];
            $campo_data_fim   = [ new TLabel( 'Data Final: ' ), $end_date ];
            $campo_hora_fim   = [ new TLabel( 'Hora Final: ' ), $hora_final ];
            $campo_minuto_fim = [ new TLabel( 'Minuto Final: ' ), $end_minute ];
            $campo_titulo     = [ new TLabel( 'Titulo: ' ), $title ];
            $campo_descr      = [ new TLabel( 'Descrição: ' ), $description ];

            $this->form->addFields( $campo_codigo, $campo_cor, $campo_titulo )->layout            = [ 'col-sm-1',
                                                                                                      'col-sm-3',
                                                                                                      'col-sm-8' ];
            $this->form->addFields( $campo_data_ini, $campo_hora_ini, $campo_minuto_ini )->layout = [ 'col-sm-4',
                                                                                                      'col-sm-4',
                                                                                                      'col-sm-4' ];
            $this->form->addFields( $campo_data_fim, $campo_hora_fim, $campo_minuto_fim )->layout = [ 'col-sm-4',
                                                                                                      'col-sm-4',
                                                                                                      'col-sm-4' ];
            $this->form->addFields( $campo_descr );

            /* BOTÕES */
            $this->form->addAction( _t( 'Save' ), new TAction( [ $this, 'onSave' ] ), 'far:save blue' );
            $this->form->addActionLink( _t( 'New' ), new TAction( [ $this, 'onClear' ] ), 'fa:eraser red' );
            $this->form->addActionLink( 'Excluir', new TAction( [ $this, 'onDelete' ] ), 'far: trash-alt red' );
            $this->form->addActionLink( 'Retornar', new TAction( [ $this,
                                                                   'onFechaRightPanel' ] ), 'fa: fa-times green' );
            /* BOTÕES */


            $container        = new TVBox();
            $container->style = 'width: 100%';
            $container->add( $this->form );
            parent::add( $container );
        }

        public static function onChangeStartHour( $param = NULL ) {
            $obj = new stdClass();
            if ( empty( $param[ 'start_minute' ] ) ) {
                $obj->start_minute = '0';
                TForm::sendData( 'form_event', $obj );
            }

            if ( empty( $param[ 'hora_final' ] ) AND empty( $param[ 'end_minute' ] ) ) {
                $obj->hora_final = $param[ 'hora_inicio' ] + 1;
                $obj->end_minute = '0';
                TForm::sendData( 'form_event', $obj );
            }
        }

        public static function onChangeEndHour( $param = NULL ) {
            if ( empty( $param[ 'end_minute' ] ) ) {
                $obj             = new stdClass();
                $obj->end_minute = '0';
                TForm::sendData( 'form_event', $obj );
            }
        }

        public static function onChangeStartDate( $param = NULL ) {
            if ( empty( $param[ 'end_date' ] ) AND !empty( $param[ 'start_date' ] ) ) {
                $obj           = new stdClass();
                $obj->end_date = $param[ 'start_date' ];
                TForm::sendData( 'form_event', $obj );
            }
        }

        public static function onChangeEndDate( $param = NULL ) {
            if ( empty( $param[ 'hora_final' ] ) AND empty( $param[ 'end_minute' ] ) AND !empty( $param[ 'hora_inicio' ] ) ) {
                $obj             = new stdClass();
                $obj->hora_final = min( $param[ 'hora_inicio' ], 22 ) + 1;
                $obj->end_minute = '0';
                TForm::sendData( 'form_event', $obj );
            }
        }

        public static function onDelete( $param ) {
            $action = new TAction( [ 'CalendarioForm', 'Delete' ] );
            $action->setParameters( $param );

            new TQuestion( AdiantiCoreTranslator::translate( 'Do you really want to delete ?' ), $action );
        }

        public static function Delete( $param ) {
            try {
                $key = $param[ 'id' ];
                TTransaction::open( 'afincco' );

                $object = new Calendario( $key, FALSE );

                $object->delete();

                TTransaction::close();

                $posAction = new TAction( [ 'CalendarioForm', 'onReload' ] );
                $posAction->setParameter( 'view', $param[ 'view' ] );
                $posAction->setParameter( 'date', $param[ 'start_date' ] );

                new TMessage( 'info', AdiantiCoreTranslator::translate( 'Recolord deleted' ), $posAction );
            } catch ( Exception $e )
            {
                new TMessage( 'error', $e->getMessage() );
                TTransaction::rollback();
            }
        }

        public static function onUpdateEvent( $param ) {
            try {
                if ( isset( $param[ 'id' ] ) ) {
                    $key = $param[ 'id' ];
                    TTransaction::open( 'afincco' );
                    $object              = new Calendario( $key );
                    $object->hora_inicio = str_replace( 'T', ' ', $param[ 'hora_inicio' ] );
                    $object->hora_final  = str_replace( 'T', ' ', $param[ 'hora_final' ] );
                    $object->store();

                    TTransaction::close();
                }
            } catch ( Exception $e )
            {
                new TMessage( 'error', '<b>Error</b> '.$e->getMessage() );
                TTransaction::rollback();
            }
        }

        public function onEdit( $param ) {
            try {
                if ( isset( $param[ 'key' ] ) ) {
                    $key = $param[ 'key' ];

                    TTransaction::open( 'afincco' );

                    $object             = new Calendario( $key );
                    $data               = new stdClass();
                    $data->id           = $object->id;
                    $data->color        = $object->color;
                    $data->title        = $object->title;
                    $data->description  = $object->description;
                    $data->start_date   = substr( $object->hora_inicio, 0, 10 );
                    $data->hora_inicio  = substr( $object->hora_inicio, 11, 2 );
                    $data->start_minute = substr( $object->hora_inicio, 14, 2 );
                    $data->end_date     = substr( $object->hora_final, 0, 10 );
                    $data->hora_final   = substr( $object->hora_final, 11, 2 );
                    $data->end_minute   = substr( $object->hora_final, 14, 2 );
                    $data->view         = $param[ 'view' ];

                    $this->form->setData( $data );

                    TTransaction::close();
                } else {
                    $this->form->clear();
                }
            } catch ( Exception $e )
            {
                new TMessage( 'error', $e->getMessage() );

                TTransaction::rollback();
            }
        }

        public function onSave() {
            try {
                TTransaction::open( 'afincco' );

                $this->form->validate();

                $data = $this->form->getData();

                $object              = new Calendario();
                $object->color       = $data->color;
                $object->id          = $data->id;
                $object->title       = $data->title;
                $object->description = $data->description;
                $object->hora_inicio = $data->start_date.' '.str_pad( $data->hora_inicio, 2, '0', STR_PAD_LEFT ).':'.str_pad( $data->start_minute, 2, '0', STR_PAD_LEFT ).':00';
                $object->hora_final  = $data->end_date.' '.str_pad( $data->hora_final, 2, '0', STR_PAD_LEFT ).':'.str_pad( $data->end_minute, 2, '0', STR_PAD_LEFT ).':00';

                $object->store();

                $data->id = $object->id;
                $this->form->setData( $data );

                TTransaction::close();
                $posAction = new TAction( [ 'CalendarioForm', 'onReload' ] );
                $posAction->setParameter( 'view', $data->view );
                $posAction->setParameter( 'date', $data->start_date );

                new TMessage( 'info', TAdiantiCoreTranslator::translate( 'Record saved' ), $posAction );
            } catch ( Exception $e )
            {
                new TMessage( 'error', $e->getMessage() );
                $this->form->setData( $this->form->getData() );
                TTransaction::rollback();
            }
        }

        public function onStartEdit( $param ) {
            $this->form->clear();
            $data        = new stdClass();
            $data->view  = $param[ 'view' ];
            $data->color = '#3a87ad';

            if ( $param[ 'date' ] ) {
                if ( strlen( $param[ 'date' ] ) == 10 ) {
                    $data->start_date = $param[ 'date' ];
                    $data->end_date   = $param[ 'date' ];
                }
                if ( strlen( $param[ 'date' ] ) == 19 ) {
                    $data->start_date   = substr( $param[ 'date' ], 0, 10 );
                    $data->hora_inicio  = substr( $param[ 'date' ], 11, 2 );
                    $data->start_minute = substr( $param[ 'date' ], 14, 2 );

                    $data->end_date   = substr( $param[ 'date' ], 0, 10 );
                    $data->hora_final = substr( $param[ 'date' ], 11, 2 ) + 1;
                    $data->end_minute = substr( $param[ 'date' ], 14, 2 );
                }
                $this->form->setData( $data );
            }
        }
    }

