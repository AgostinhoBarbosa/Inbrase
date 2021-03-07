<?php
    
    class CalendarioForm extends TPage
    {
        private $fc;
        
        public function __construct()
        {
            parent::__construct();
            $this->fc = new TFullCalendar( date( 'Y-m-d' ), 'month' );
            $this->fc->setTimeRange( '06:00:00', '20:00:00' );
            $this->fc->enablePopover( 'Title {title}', '<b>{title}</b> <br> <i class="fa fa-user" aria-hidden="true"></i> {person} <br> {description}' );
            $this->fc->setReloadAction( new TAction( [ $this, 'getEvents' ] ) );
            $this->fc->setDayClickAction( new TAction( [ 'CalendarioEventForm', 'onStartEdit' ] ) );
            $this->fc->setEventClickAction( new TAction( [ 'CalendarioEventForm', 'onEdit' ] ) );
            $this->fc->setEventUpdateAction( new TAction( [ 'CalendarioEventForm', 'onUpdateEvent' ] ) );
            $this->fc->adianti_target_container = 'calendario';
            $this->fc->adianti_target_title     = 'Calendário';
            
            parent::add( $this->fc );
        }
        
        /**
         * Output events as an json
         */
        public static function getEvents( $param = NULL )
        {
            $return = [];
            try {
                TTransaction::open( 'afincco' );
                
                $events = Calendario::where( 'hora_inicio', '>=', $param[ 'start' ] )
                                    ->where( 'hora_final', '<=', $param[ 'end' ] )->load();
                
                if ( $events ) {
                    foreach ( $events as $event ) {
                        $event_array            = $event->toArray();
                        $event_array[ 'start' ] = str_replace( ' ', 'T', $event_array[ 'hora_inicio' ] );
                        $event_array[ 'end' ]   = str_replace( ' ', 'T', $event_array[ 'hora_final' ] );
                        $return[]               = $event_array;
                    }
                }
                TTransaction::close();
                echo json_encode( $return );
            } catch ( Exception $e ) {
                new TMessage( 'error', $e->getMessage() );
            }
        }
        
        /**
         * Reconfigure the callendar
         */
        public function onReload( $param = NULL )
        {
            if ( isset( $param[ 'view' ] ) ) {
                $this->fc->setCurrentView( $param[ 'view' ] );
            }
            
            if ( isset( $param[ 'date' ] ) ) {
                $this->fc->setCurrentDate( $param[ 'date' ] );
            }
        }
    }

