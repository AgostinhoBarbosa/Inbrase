<?php

use Lacuna\RestPki\RestPkiClient;

require __DIR__ . '/vendor/autoload.php';

function getRestPkiClient()
{

    // -----------------------------------------------------------------------------------------------------------
    // PASTE YOUR ACCESS TOKEN BELOW
    $restPkiAccessToken = 'pJiyG3_BsO43ey9kvieOuWj6H66WFnbppy8yJtmkQx3G72rsmKNFyoxMcefSGyZuctp7-tDSJ7OIkbApHS1JbHvKNtugnErXMqnHwjFRaSLWXgxhOXGVk-oC01fDgE7Vxe9iOwowG2eRnTNeITIR5DH3lB5HnGcgZGomE9bXDkMXZ3jcjTTzeZBp9IdojpWny4WK8OBodqvgH8zyz4Cjkx07wx2XANoqAlocdW7cnxmyzqBEjNlIrqOvUN6-cr522_9ZzL7dvykt1NWxIhpHxG-9PUq5AGeEsRaZtwMKecxiorcJF4rFPvo3OOqptvHkivPYXIz-LBzi3BodktZzkKRHhxenv2nNRfGSYp-EC66rK2cFAajzyGpf80eMeCpMD7DngoB_X795qvf-bWRMraTDq0BmeuS7hMcCrek_BfBBCSUwHc0uCk8pwdgJIiz-F6iZRJY1buSRm3ozEhSNeGcfVcta1ERU-3ffbDyHZyAspyWBs8sUIZ8xDik_e-700XpJqg';
    //                     
    // -----------------------------------------------------------------------------------------------------------

    // Throw exception if token is not set (this check is here just for the sake of newcomers, you can remove it)
    if (strpos($restPkiAccessToken, ' API ') !== false) {
        throw new \Exception('The API access token was not set! Hint: to run this sample you must generate an API access token on the REST PKI website and paste it on the file api/util.php');
    }

    // -----------------------------------------------------------------------------------------------------------
    // IMPORTANT NOTICE: in production code, you should use HTTPS to communicate with REST PKI, otherwise your API
    // access token, as well as the documents you sign, will be sent to REST PKI unencrypted.
    // -----------------------------------------------------------------------------------------------------------
    //$restPkiUrl = 'http://pki.rest/';
    $restPkiUrl = 'https://pki.rest/'; // <--- USE THIS IN PRODUCTION!

    return new RestPkiClient($restPkiUrl, $restPkiAccessToken);
}

function setExpiredPage()
{
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() - 3600) . ' GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: private, no-store, max-age=0, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Pragma: no-cache');
}

function createAppData()
{
    $appDataPath = "contratos";
    if (!file_exists($appDataPath)) {
        mkdir($appDataPath);
    }
}

function getPdfStampContent()
{
    return file_get_contents($_SERVER['DOCUMENT_ROOT'].'/app/lib/lacuna/content/PdfStamp.png');
}
