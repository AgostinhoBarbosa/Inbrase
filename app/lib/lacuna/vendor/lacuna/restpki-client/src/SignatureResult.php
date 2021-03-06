<?php

namespace Lacuna\RestPki;

/**
 * Class SignatureResult
 * @package Lacuna\RestPki
 *
 * @property mixed $certificate Information about the signer's certificate
 * @property string $callbackArgument Callback argument passed when the signature process was started
 */
class SignatureResult extends FileResult
{
    public $certificate;
    public $callbackArgument;

    /**
     * @internal
     *
     * @param RestPkiClient $restPkiClient
     * @param mixed $model
     * @param mixed $certificate
     * @param string $callbackArgument
     */
    public function __construct($restPkiClient, $model, $certificate, $callbackArgument)
    {
        parent::__construct($restPkiClient, $model);
        $this->certificate = $certificate;
        $this->callbackArgument = $callbackArgument;
    }
}
