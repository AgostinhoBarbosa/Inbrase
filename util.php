<?php

    use Lacuna\RestPki\RestPkiClient;
    use Lacuna\RestPki\StandardSecurityContexts;

    require __DIR__.'/app/lib/lacuna/vendor/autoload.php';

    function getRestPkiClient()
    {

        // -----------------------------------------------------------------------------------------------------------
        // PASTE YOUR ACCESS TOKEN BELOW
        $restPkiAccessToken = 'pJiyG3_BsO43ey9kvieOuWj6H66WFnbppy8yJtmkQx3G72rsmKNFyoxMcefSGyZuctp7-tDSJ7OIkbApHS1JbHvKNtugnErXMqnHwjFRaSLWXgxhOXGVk-oC01fDgE7Vxe9iOwowG2eRnTNeITIR5DH3lB5HnGcgZGomE9bXDkMXZ3jcjTTzeZBp9IdojpWny4WK8OBodqvgH8zyz4Cjkx07wx2XANoqAlocdW7cnxmyzqBEjNlIrqOvUN6-cr522_9ZzL7dvykt1NWxIhpHxG-9PUq5AGeEsRaZtwMKecxiorcJF4rFPvo3OOqptvHkivPYXIz-LBzi3BodktZzkKRHhxenv2nNRfGSYp-EC66rK2cFAajzyGpf80eMeCpMD7DngoB_X795qvf-bWRMraTDq0BmeuS7hMcCrek_BfBBCSUwHc0uCk8pwdgJIiz-F6iZRJY1buSRm3ozEhSNeGcfVcta1ERU-3ffbDyHZyAspyWBs8sUIZ8xDik_e-700XpJqg';
        //
        // -----------------------------------------------------------------------------------------------------------

        // Throw exception if token is not set (this check is here just for the sake of newcomers, you can remove it)
        if ( strpos($restPkiAccessToken, ' API ') !== FALSE ) {
            throw new Exception('The API access token was not set! Hint: to run this sample you must generate an API access token on the REST PKI website and paste it on the file api/util.php');
        }

        // -----------------------------------------------------------------------------------------------------------
        // IMPORTANT NOTICE: in production code, you should use HTTPS to communicate with REST PKI, otherwise your API
        // access token, as well as the documents you sign, will be sent to REST PKI unencrypted.
        // -----------------------------------------------------------------------------------------------------------
        //$restPkiUrl = 'http://pki.rest/';
        $restPkiUrl = 'https://pki.rest/'; // <--- USE THIS IN PRODUCTION!

        return new RestPkiClient($restPkiUrl, $restPkiAccessToken);
    }

    function getSecurityContextId()
    {
        /*
         * Lacuna Text PKI (for development purposes only!)
         *
         * This security context trusts ICP-Brasil certificates as well as certificates on Lacuna Software's
         * test PKI. Use it to accept the test certificates provided by Lacuna Software, uncomment the following
         * line.
         *
         * THIS SHOULD NEVER BE USED ON A PRODUCTION ENVIRONMENT!
         * For more information, see https://github.com/LacunaSoftware/RestPkiSamples/blob/master/TestCertificates.md
         */
        //return StandardSecurityContexts::LACUNA_TEST;
        // In production, accept only certificates from ICP-Brasil.
        return StandardSecurityContexts::PKI_BRAZIL;
    }

    function setExpiredPage()
    {
        header('Expires: '.gmdate('D, d M Y H:i:s', time() - 3600).' GMT');
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header('Cache-Control: private, no-store, max-age=0, no-cache, must-revalidate, post-check=0, pre-check=0');
        header('Pragma: no-cache');
    }

    function createAppData()
    {
        $appDataPath = "arquivos";
        if ( !file_exists($appDataPath) ) {
            mkdir($appDataPath);
        }
        $appDataPath .= "/protesto";
        if ( !file_exists($appDataPath) ) {
            mkdir($appDataPath);
        }
    }

    function getPdfStampContent()
    {
        return file_get_contents('content/PdfStamp.png');
    }
