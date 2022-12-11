<?php
require_once 'vendor/autoload.php';

use GuzzleHttp\Client;

$token = 'TSNETPRF|W51PyR/NEbZFHCKeSIgF2njTzGNUxkpv7PwySvDxdIw=';

$client = new Client([
    // You can set any number of default request options.
    'timeout'  => 3000.0,
]);

$response = $client->request('POST', 'https://cloud.sellandsign.com/calinda/hub/selling/do?m=sendCommandPacket', [
    'headers' => [
        'j_token' => $token
    ],

    'multipart' => [
        [
            'name'     => 'adhoc_light.sellsign',
            'contents' => fopen(__DIR__ . '/ressource/adhoc_light.sellsign', 'r'),
            'filename' => 'adhoc_light.sellsign',
            'headers'  => [
                'Content-type' => 'application/json'
            ]
        ],
        [
            'name'     => 'contrat_carat.pdf',
            'contents' => fopen(__DIR__ . '/ressource/contrat_carat.pdf', 'r'),
            'filename' => 'contrat_carat.pdf',
            'headers'  => [
                'Content-type' => 'application/pdf'
            ]
        ]
    ]
]);


var_dump($response->getBody()->getContents());

// $response2 = $client->request('POST', 'https://cloud.sellandsign.com/calinda/hub/selling/model/contractdefinition/list?action=getContractDefinitionList', [
//     'headers' => [
//         'j_token' => $token
//         ]
// ]);

// var_dump($response2->getBody()->getContents());
