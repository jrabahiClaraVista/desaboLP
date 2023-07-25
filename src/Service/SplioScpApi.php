<?php

namespace App\Service;

use GuzzleHttp\Client as HttpClient;

class SplioScpApi
{

    private $universe;
    private $pass;
    private $api_key;
    private $api_base_url;
    private $client;


    public function __construct($universe, $pass, $api_key)
    {
        #$this->container = $container;
        $this->universe = $universe;
        $this->pass = $pass;
        $this->api_key = $api_key;
        $this->api_base_url = "'https://api.splio.com/data/contacts/";
        $this->client = new HttpClient();
    }

    public function auth()
    {
        $response = $this->client->request('POST', 'https://api.splio.com/authenticate', [
            'body' => '{"api_key":"'.$this->api_key.'"}',
            'headers' => [
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ],
            'verify' => false
        ]);
        
        return json_decode($response->getBody()->getContents());
    }

    public function exists($contactID, $token)
    {   
        $response = $this->client->request('GET', "https://api.splio.com/data/contacts/$contactID", [
            'headers' => [
                'accept' => 'application/json',
                'authorization' => "Bearer $token",
            ],
            'verify' => false
        ]);
        
        return json_decode($response->getBody()->getContents());
    }

    public function create($lastname,$firstname,$contactID, $token)
    {   
        $response = $this->client->request('POST', "https://api.splio.com/data/contacts", [
            'body' => '{"lists":[{"id":0}],"lastname":"'.$lastname.'","firstname":"'.$firstname.'","email":"'.$contactID.'"}',
            'headers' => [
                'accept' => 'application/json',
                'authorization' => "Bearer $token",
            ],
            'verify' => false
        ]);
        
        return json_decode($response->getBody()->getContents());
    }

    public function update($contactID, $options, $token)
    {   
        $response = $this->client->request('PATCH', "https://api.splio.com/data/contacts/$contactID", [
            'body' => '{"custom_fields":[{"name":"desabo_campagne","value":"'.$options[0].'"},{"name":"desabo_motif","value":"'.$options[1].'"},{"name":"is_optin","value":"0"}]}',
            'headers' => [
                'accept' => 'application/json',
                'authorization' => "Bearer $token",
            ],
            'verify' => false
        ]);

        return json_decode($response->getBody()->getContents());
    }

    public function isBlackList($contactID, $token)
    {
        $response = $this->client->request('GET', "https://api.splio.com/data/blacklists/emails?term=$contactID", [
            'body' => '{"data":["'.$contactID.'"]}',
            'headers' => [
                'accept' => 'application/json',
                'authorization' => "Bearer $token",
            ],
            'verify' => false
        ]);

        return json_decode($response->getBody()->getContents());
    }

    public function AddBlackListPerso($contactID, $token)
    {
        $response = $this->client->request('POST', "https://api.splio.com/data/blacklists/email", [
            'body' => '{"data":["'.$contactID.'"]}',
            'headers' => [
                'accept' => 'application/json',
                'authorization' => "Bearer $token",
            ],
            'verify' => false
        ]);
        
        return json_decode($response->getBody());
    }

    public function deleteBlackListPerso($contactID, $token)
    {
        $response = $this->client->request('DELETE', "https://api.splio.com/data/blacklists/email/custom", [
            'body' => '{"data":["'.$contactID.'"]}',
            'headers' => [
                'accept' => 'application/json',
                'authorization' => "Bearer $token",
            ],
            'verify' => false
        ]);

        return json_decode($response->getBody());
    }
}