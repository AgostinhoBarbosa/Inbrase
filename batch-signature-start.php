<?php

    /*
     * This file is called asynchronously via AJAX by the batch signature page for each document being signed. It receives
     * the ID of the document and initiates a PAdES signature using REST PKI and returns a JSON with the token, which
     * identifies this signature process, to be used in the next signature steps (see batch-signature-form.js).
     */

    require 'util.php';
    require 'util-pades.php';
    require 'app/lib/lacuna/vendor/autoload.php';

    use Lacuna\RestPki\CadesSignatureStarter;
    use Lacuna\RestPki\PadesMeasurementUnits;

// Get the document id for this signature (received from the POST call, see batch-signature-form.js).
    $id = $_POST[ 'id' ];

// Instantiate the PadesSignatureStarter class, responsible for receiving the signature elements and start the
// signature process.
    $signatureStarter = new CadesSignatureStarter(getRestPkiClient());

// Set the document to be signed based on its ID.
    $signatureStarter->setFileToSignFromPath(sprintf("arquivos/protesto/".$id));

// Set the signature policy.
    //$signatureStarter->signaturePolicy = StandardSignaturePolicies::CADES_ICPBR_ADR_BASICA;
    $signatureStarter->signaturePolicy = "8108539d-c137-4f45-a1f2-de5305bc0a37";

// Set the security context. We have encapsulated the security context choice on util.php.
    $signatureStarter->securityContext = getSecurityContextId();

// Opcionalmente, defina se o conteúdo deve ser encapsulado no CMS resultante. Se este parâmetro for omitido,
// as seguintes regras se aplicam:
// - Se nenhum CmsToCoSign for fornecido, o CMS resultante incluirá o conteúdo.
// - Se um CmsToCoSign for fornecido, o CMS resultante incluirá o conteúdo se, e somente se, o CmsToCoSign também incluir
//    o conteúdo.
    //$signatureStarter->encapsulateContent = true; //original
    $signatureStarter->encapsulateContent = true;

// Set the unit of measurement used to edit the pdf marks and visual representations.
    $signatureStarter->measurementUnits = PadesMeasurementUnits::CENTIMETERS;

// Set the visual representation to the signature. We have encapsulated this code (on util-pades.php) to be used on
// various PAdES examples.
    $signatureStarter->visualRepresentation = getVisualRepresentation(getRestPkiClient());

    /*
        Optionally, add marks to the PDF before signing. These differ from the signature visual representation in that
        they are actually changes done to the document prior to signing, not binded to any signature. Therefore, any number
        of marks can be added, for instance one per page, whereas there can only be one visual representation per signature.
        However, since the marks are in reality changes to the PDF, they can only be added to documents which have no
        previous signatures, otherwise such signatures would be made invalid by the changes to the document (see property
        PadesSignatureStarter::bypassMarksIfSigned). This problem does not occur with signature visual representations.

        We have encapsulated this code in a method to include several possibilities depending on the argument passed.
        Experiment changing the argument to see different examples of PDF marks. Once you decide which is best for your case,
        you can place the code directly here.
    */
//array_push($signatureStarter->pdfMarks, getPdfMark(1));

// Call the startWithWebPki() method, which initiates the signature. This yields the token, a 43-character
// case-sensitive URL-safe string, which identifies this signature process. We'll use this value to call the
// signWithRestPki() method on the Web PKI component (see batch-signature-form.js) and also to complete the signature
// on the POST action below (this should not be mistaken with the API access token).
    $token = $signatureStarter->startWithWebPki();

// Return a JSON with the token obtained from REST PKI (the page will use jQuery to decode this value)
    echo json_encode($token);
