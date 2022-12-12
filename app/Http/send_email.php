<?php

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Facade;

function send_email($id) {
    $url = 'http://172.24.206.101:9000/send/event/'.$id;
    $client = new Client();
    $response = $client->request('GET', $url, [
        'headers' => [ 'Authorization' => 'Basic ' . env("PRIVATE")]
    ]);
    return $response->getBody();
}