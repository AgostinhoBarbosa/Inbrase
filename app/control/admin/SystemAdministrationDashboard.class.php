<?php
    
    use Adianti\Database\TTransaction;
    
    /**
     * SystemAdministrationDashboard
     *
     * @version    1.0
     * @package    control
     * @subpackage log
     * @author     Pablo Dall'Oglio
     * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
     * @license    http://www.adianti.com.br/framework-license
     */
    class SystemAdministrationDashboard extends TPage
    {
        /**
         * Class constructor
         * Creates the page
         */
        function __construct()
        {
            parent::__construct();

            try {
                $html = new THtmlRenderer( 'app/resources/system_admin_dashboard.html' );

                TTransaction::open( 'afincco' );

                $indicator1 = new THtmlRenderer( 'app/resources/info-box.html' );
                $indicator2 = new THtmlRenderer( 'app/resources/info-box.html' );

                $indicator1->enableSection( 'main', ['title' => 'Clientes', 'icon' => 'users', 'background' => 'blue', 'value' => number_format( Pessoa::count(), 0, '', '.' )] );
                $indicator2->enableSection( 'main', ['title' => 'Processos', 'icon' => 'university', 'background' => 'purple', 'value' => number_format( Processo::count(), 0, '', '.' )] );

                $chart1  = new THtmlRenderer( 'app/resources/google_pie_chart.html' );
                $data1   = [];
                $data1[] = ['Seguradora', 'Processo'];

                $stats1 = Processo::groupBy( 'id_seg' )->orderBy( 'count', 'desc')->countBy( 'id_seg', 'count');
                if ( $stats1 ) {
                    foreach ( $stats1 as $row ) {
                        $total   = number_format( $row->count, 0, '', '.' );
                        $seguradora = Seguradoras::find( $row->id_seg );
                        if ($seguradora){
                            $data1[] = [$seguradora->get_nomeLimpo()."(". $total . ")", (int) $row->count];
                        }else{
                            $data1[] = ["SEM SEGURADORA (" . $total . ")".$row->id_seg, (int) $row->count];
                        }
                    }
                }
                // replace the main section variables
                $chart1->enableSection( 'main', ['data'   => json_encode( $data1 ),
                                                 'width'  => '100%',
                                                 'height' => '300px',
                                                 'title'  => ( 'Processos por Seguradora' ),
                                                 'ytitle' => 'Seguradora',
                                                 'xtitle' => _t( 'Count' ),
                                                 'uniqid' => uniqid()] );

                $chart2  = new THtmlRenderer( 'app/resources/google_pie_chart.html' );
                $data2   = [];
                $data2[] = ['Situação', 'Processo'];

                $stats2 = Processo::groupBy( 'liberador' )->orderBy( 'count', 'desc')->countBy( 'liberador', 'count' );
                if ( $stats2 ) {
                    foreach ( $stats2 as $row ) {
                        $total   = number_format( $row->count, 0, '', '.' );
                        $pessoa = Pessoa::find($row->liberador);
                        if ($pessoa) {
                            $data2[] = [ $pessoa->nome."(".$total.")", (int)$row->count ];
                        }else {
                            $data2[] = [ "SEM LIBERADOR (".$total.")", (int)$row->count ];
                        }
                    }
                }
                // replace the main section variables
                $chart2->enableSection( 'main', ['data'   => json_encode( $data2 ),
                                                 'width'  => '100%',
                                                 'height' => '300px',
                                                 'title'  => 'Processos por Liberador',
                                                 'ytitle' => 'Liberador',
                                                 'xtitle' => _t( 'Count' ),
                                                 'uniqid' => uniqid()] );

                $chart3  = new THtmlRenderer( 'app/resources/google_pie_chart.html' );
                $data3   = [];
                $data3[] = ['Situação', 'Processo'];

                $stats3 = ViewStatusProcesso::orderBy( 'total', 'desc' )->load();
                if ( $stats3 ) {
                    foreach ( $stats3 as $row ) {
                        $total   = number_format( $row->total, 0, '', '.' );
                        $status = explode('-',$row->status->statu);
                        $data3[] = [ $status[0]."(".$total.")", (int)$row->total ];
                    }
                }
                $chart3->enableSection( 'main', ['data'   => json_encode( $data3 ),
                                                 'width'  => '100%',
                                                 'height' => '300px',
                                                 'title'  => 'Processos por Situação',
                                                 'ytitle' => 'Situação',
                                                 'xtitle' => _t( 'Count' ),
                                                 'uniqid' => uniqid()] );



                $chart4  = new THtmlRenderer( 'app/resources/google_column_chart.html' );
                $data4   = [];
                $data4[] = ['Apontamento', 'Mês'];

                $data_inicio = date( 'Y-m-d', strtotime( '-6 months', strtotime( date( 'm/01/Y' ) ) ) );
                $stats4      = Processo::where( 'data_cadastro', '>', $data_inicio )->groupBy( "date_format(data_cadastro, '%m-%Y')" )->orderBy( 'data_cadastro' )->countBy( "date_format(data_cadastro, '%m-%Y')", 'count' );
                if ( $stats4 ) {
                    foreach ( $stats4 as $row ) {
                        $total = number_format( $row->count, 0, '', '.' );
                        $nome  = 'Mes';
                        foreach ( $row as $key => $value ) {
                            if ( $key == 0 ) {
                                $nome = str_replace( '-', '/', $value );
                                break;
                            }
                        }
                        $data4[] = [$nome . "(" . $total . ")", (int) $row->count];
                    }
                }
                // replace the main section variables
                $chart4->enableSection( 'main', ['data'   => json_encode( $data4 ),
                                                 'width'  => '100%',
                                                 'height' => '300px',
                                                 'title'  => 'Processos por mês',
                                                 'ytitle' => 'Processos',
                                                 'xtitle' => 'Meses',
                                                 'uniqid' => uniqid()] );


                $html->enableSection( 'main', ['indicator1' => $indicator1,
                                               'indicator2' => $indicator2,
                                               'chart1'     => $chart1,
                                               'chart2'     => $chart2,
                                               'chart3'     => $chart3,
                                               'chart4'     => $chart4] );

                $container                           = new TVBox();
                $container->style                    = 'width: 100%';
                $container->adianti_target_container = 'dashboard';
                $container->adianti_target_title     = 'Dashboard';
                $container->add( $html );

                parent::add( $container );
                TTransaction::close();
            } catch ( Exception $e ) {
                parent::add( $e->getMessage() );
            }
        }
    }
