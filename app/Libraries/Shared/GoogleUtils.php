<?php

namespace App\Libraries\Shared;

use GuzzleHttp\Client;

class GoogleUtils
{
    public static function retrieveLatLng($address)
    {
        // LatLng ophalen middels GuzzleHTTP
        $client = new Client();
        $res = $client->request('GET', 'https://api.kjsoftware.nl/api/googlelatlng?format=json&adres=' . $address);
        $code = $res->getStatusCode();

        if ($code == 200) {
            $json = $res->getBody();
            return json_decode($json);
        }

        return null;
    }
}