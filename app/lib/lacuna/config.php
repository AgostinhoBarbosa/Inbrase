<?php

function getConfig()
{
    return [

        // -------------------------------------------------------------------------------------------------------------
        // Web PKI Configuration
        // -------------------------------------------------------------------------------------------------------------
        'webPki' => [

            // Base64-encoded binary license for the Web PKI. This value is passed to Web PKI component's constructor.
            'license' => null
        ],

        // -------------------------------------------------------------------------------------------------------------
        // REST PKI Configuration
        // -------------------------------------------------------------------------------------------------------------
        'restPki' => [

            // =================================================
            //     >>>> PASTE YOU ACCESS TOKEN BELOW <<<<
            // =================================================
            'accessToken' => 'pJiyG3_BsO43ey9kvieOuWj6H66WFnbppy8yJtmkQx3G72rsmKNFyoxMcefSGyZuctp7-tDSJ7OIkbApHS1JbHvKNtugnErXMqnHwjFRaSLWXgxhOXGVk-oC01fDgE7Vxe9iOwowG2eRnTNeITIR5DH3lB5HnGcgZGomE9bXDkMXZ3jcjTTzeZBp9IdojpWny4WK8OBodqvgH8zyz4Cjkx07wx2XANoqAlocdW7cnxmyzqBEjNlIrqOvUN6-cr522_9ZzL7dvykt1NWxIhpHxG-9PUq5AGeEsRaZtwMKecxiorcJF4rFPvo3OOqptvHkivPYXIz-LBzi3BodktZzkKRHhxenv2nNRfGSYp-EC66rK2cFAajzyGpf80eMeCpMD7DngoB_X795qvf-bWRMraTDq0BmeuS7hMcCrek_BfBBCSUwHc0uCk8pwdgJIiz-F6iZRJY1buSRm3ozEhSNeGcfVcta1ERU-3ffbDyHZyAspyWBs8sUIZ8xDik_e-700XpJqg',

            // Address of your Rest PKI installation (with the trailing '/' character)
            "endpoint" => null
        ]
    ];
}
