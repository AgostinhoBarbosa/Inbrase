<?php

    /**
     * This action renders the batch signature page.
     *
     * Notice that the only thing we'll do on the server-side at this point is determine the IDs of the documents
     * to be signed. The page will handle each document one by one and will call the server asynchronously to
     * start and complete each signature.
     */

    require __DIR__.'/app/lib/lacuna/vendor/autoload.php';

    $documentsIds = TSession::getValue('instrumentos');

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <?php include 'app/lib/lacuna/includes.php' // jQuery and other libs (used only to provide a better user experience, but NOT required to use the Web PKI component) ?>
</head>
<body>

<div class="container">

    <?php // Messages about the signature process will be rendered in here ?>
    <div id="messagesPanel"></div>

    <h2>Assinatura Instrumento Protesto - CADES</h2>

    <form id="signForm" method="POST">


        <div class="form-group">
            <label>Arquivos para Assinar</label>

            <p>
                Você assinara os seguintes documentos:
                <?php
                    // UL element to hold the batch's documents (we'll render these programatically,
                    // see batch-signature-form.js).
                ?>
            <ul id="docList"/>
            </p>
        </div>

        <?php
            // Render a select (combo box) to list the user's certificates. For now it will be
            // empty, we'll populate it later on (see batch-signature-form.js).
        ?>
        <div class="form-group">
            <label for="certificateSelect">Choose a certificate</label>
            <select id="certificateSelect" class="form-control"></select>
        </div>

        <?php
            // Action buttons. Notice that the "Sign File" button is NOT a submit button. When the user clicks the button,
            // we must first use the Web PKI component to perform the client-side computation necessary and only when
            // that computation is finished we'll submit the form programmatically (see batch-signature-form.js).
        ?>
        <button id="signButton" type="button" class="btn btn-primary">Assinar</button>
        <button id="refreshButton" type="button" class="btn btn-default">Atualizar Certificadoo</button>

    </form>

</div>


<?php
    // The file below contains the JS lib for accessing the Web PKI component. For more information, see:
    // https://webpki.lacunasoftware.com/#/Documentation
?>
<script src="app/lib/lacuna/content/js/lacuna-web-pki-2.9.0.js"></script>

<?php
    // The file below contains the logic for calling the Web PKI component. It is only an example, feel free to alter it
    // to meet your application's needs. You can also bring the code into the javascript block below if you prefer.
?>
<script src="app/lib/lacuna/content/js/batch-signature-form.js"></script>
<script>
    $( document ).ready( function () {
        // Once the page is ready, we call the init() function on the javascript code (see batch-signature-form.js)
        batchSignatureForm.init( {
            documentsIds: <?= json_encode($documentsIds); ?>, // The IDs of documents.
            certificateSelect: $( '#certificateSelect' ),       // The <select> element (combo box) to list the certificates.
            refreshButton: $( '#refreshButton' ),               // The "refresh" button.
            signButton: $( '#signButton' )                      // The button that initiates the operation.
        } );
    } );
</script>

</body>
</html>

