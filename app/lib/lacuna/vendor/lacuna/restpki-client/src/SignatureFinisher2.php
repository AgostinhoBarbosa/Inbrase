<?php

namespace Lacuna\RestPki;

/**
 * Class SignatureFinisher2
 * @package Lacuna\RestPki
 *
 * @property string $token
 * @property string $signatureBase64
 * @property bool $forceBlobResult
 */
abstract class SignatureFinisher2
{
    public $token;
    public $signatureBase64;
    public $forceBlobResult = false;

    /** @var RestPkiClient */
    protected $client;

    /**
     * @param $client RestPkiClient
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * Sets the signature algorithm output in raw (binary) form
     *
     * @param $signature string
     */
    public function setSignatureRaw($signature)
    {
        $this->signatureBase64 = base64_encode($signature);
    }

    /**
     * @return SignatureResult
     *
     * @throws RestErrorException
     * @throws RestPkiException
     * @throws RestUnreachableException
     * @throws ValidationException
     */
    public function finish()
    {

        if (empty($this->token)) {
            throw new \LogicException("The token was not set");
        }

        $this->checkCompatibility();

        $request = array(
            'forceBlobResult' => $this->forceBlobResult,
            'signature' => $this->signatureBase64
        );
        $response = $this->client->post($this->getApiRoute(), $request);

        return new SignatureResult($this->client, $response->signatureFile, $response->certificate,
            $response->callbackArgument);
    }

    /**
     * @return string
     */
    protected abstract function getApiRoute();

    protected abstract function checkCompatibility();
}
