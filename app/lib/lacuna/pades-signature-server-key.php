<?php
    require __DIR__.'/vendor/autoload.php';
    require __DIR__.'/util.php';
    
    use Adianti\Registry\TSession;
    use Lacuna\RestPki\PadesMeasurementUnits;
    use Lacuna\RestPki\PadesSignatureFinisher2;
    use Lacuna\RestPki\PadesSignatureStarter;
    use Lacuna\RestPki\StandardSecurityContexts;
    
    $ini = parse_ini_file( $_SERVER[ 'DOCUMENT_ROOT' ].'/app/config/application.ini', TRUE );
    
    $quem_assina = TSession::getValue( 'quem_assina' );
    
    switch ( $quem_assina ) {
        case 1:
            $certificado = $_SERVER[ 'DOCUMENT_ROOT' ]."/app/Certificado/david.pfx";
            break;
        case 2:
            $certificado = $_SERVER[ 'DOCUMENT_ROOT' ]."/app/Certificado/debora.pfx";
            break;
        case 3:
            $certificado = $_SERVER[ 'DOCUMENT_ROOT' ]."/app/Certificado/rafael.pfx";
            break;
    }
    
    if ( !$certStore = file_get_contents( $certificado ) ) {
        throw new \Exception( "Unable to read PKCS #12 file" ); //Senha do Certificado
    }
    
    if ( !openssl_pkcs12_read( $certStore, $certObj, $ini[ 'general' ][ 'keyA1' ] ) ) {
        throw new \Exception( "Unable to open the PKCS #12 file" );
    }
    
    $signatureStarter = new PadesSignatureStarter( getRestPkiClient() );
    $signatureStarter->setSignerCertificateRaw( $certObj[ 'cert' ] );
    $signatureStarter->measurementUnits = PadesMeasurementUnits::CENTIMETERS;
    //$signatureStarter->signaturePolicy = StandardSignaturePolicies::PADES_BASIC;
    $signatureStarter->signaturePolicy = '6a39aeea-a2d0-4754-bf8c-19da15296ddb'; //Assinatura com carimbo de tempo
    //$signatureStarter->securityContext = '803517ad-3bbc-4169-b085-60053a8f6dbf';
    $signatureStarter->securityContext = StandardSecurityContexts::PKI_BRAZIL;
    
    // Set the visual representation for the signature
    $signatureStarter->visualRepresentation = [
        'text'     => [
            'text'               => 'Documento assinado eletronicamente por {{name}}, CPF: {{br_cpf_formatted}} conforme art. 1º, III, "b", da Lei 11.419/2006.',
            'includeSigningTime' => TRUE,
            'horizontalAlign'    => 'Left',
            'container'          => [
                'left'   => 1.3,
                'top'    => 0,
                'right'  => 0,
                'bottom' => 0
            ]
        ],
        'image'    => [
            'resource'        => [
                'content'  => base64_encode( getPdfStampContent() ),
                'mimeType' => 'image/png'
            ],
            'horizontalAlign' => 'Left',
            'verticalAlign'   => 'Center',
        ],
        'position' => [
            'pageNumber'       => -1,
            'measurementUnits' => PadesMeasurementUnits::CENTIMETERS,
            'auto'             => [
                'container'              => [
                    'left'   => 1.4,
                    'right'  => 1.4,
                    'bottom' => 3.7,
                    'height' => 2.5
                ],
                'signatureRectangleSize' => [
                    'width'  => 18,
                    'height' => 1.1
                ],
                'rowSpacing'             => 0.2
            ]
        ]
    ];
    
    
    $userfile = TSession::getValue( 'arquivo_carimbo' );
    $signatureStarter->setPdfToSignFromPath( $userfile );
    
    if ( empty( $userfile ) ) {
        return FALSE;
    }
    
    $signatureParams = $signatureStarter->start();
    
    // Perform the signature using the parameters returned by Rest PKI with the key extracted from PKCS #12
    openssl_sign( $signatureParams->toSignData, $signature, $certObj[ 'pkey' ], $signatureParams->openSslSignatureAlgorithm );
    
    // Instantiate the PadesSignatureFinisher2 class, responsible for completing the signature process
    $signatureFinisher = new PadesSignatureFinisher2( getRestPkiClient() );
    
    // Set the token
    $signatureFinisher->token = $signatureParams->token;
    
    // Set the signature
    $signatureFinisher->setSignatureRaw( $signature );
    
    // Call the finish() method, which finalizes the signature process and returns a SignatureResult object
    $signatureResult = $signatureFinisher->finish();
    
    // The "certificate" property of the SignatureResult object contains information about the certificate used by the user
    // to sign the file.
    $signerCert = $signatureResult->certificate;
    
    // At this point, you'd typically store the signed PDF on your database. For demonstration purposes, we'll
    // store the PDF on a temporary folder publicly accessible and render a link to it.
    
    //$filename = uniqid() . ".pdf";
    //createAppData(); // make sure the "app-data" folder exists (util.php)
    
    // The SignatureResult object has functions for writing the signature file to a local file (writeToFile()) and to get
    // its raw contents (getContent()). For large files, use writeToFile() in order to avoid memory allocation issues.
    return $signatureResult->writeToFile( $userfile );

?>
