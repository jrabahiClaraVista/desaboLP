<?php

namespace App\Service;

class SplioAPI
{

    private $universe;
    private $pass;


    public function __construct($universe, $pass)
    {
        #$this->container = $container;
        $this->universe = $universe;
        $this->pass = $pass;
    }

    public function exists($contactID)
    {   
        // ICI APPEL DE L'URL DE L'API avec les variable definies ci-dessus pour la création d'un contact
        $service_url = "https://".$this->universe.":".$this->pass."@s3s.fr/api/data/1.2/contact/$contactID";

        // ON INITIALISE CURL
        $curl = curl_init($service_url);

        //Init da requête CURL
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Expect:"));

        //Execution de la requête
        $curl_reponse = curl_exec($curl);
        $response = json_decode($curl_reponse);
        curl_close($curl);
        
        return $response;
    }

    public function create($contactID)
    {   
        // ICI APPEL DE L'URL DE L'API avec les variable definies ci-dessus pour la création d'un contact
        $service_url = "https://".$this->universe.":".$this->pass."@s3s.fr/api/data/1.2/contact";

        // ON INITIALISE CURL
        $curl = curl_init($service_url);

        //Dans le cas d'un client qui n'existe pas
        $query = array(
            'email'     => $contactID,
            'lang'      => 'fr',
            'fields'    => array(
                array('name' => 'email_hash', 'value' => md5(strtolower($contactID))))
        );

        $qstring = json_encode($query);

        //Init da requête CURL
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $qstring);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Expect:"));

        //Execution de la requête
        $curl_reponse = curl_exec($curl);
        $response = json_decode($curl_reponse);
        curl_close($curl);
        
        return $response;
    }

    public function update($contactID, $options)
    {   
        $service_url = "https://".$this->universe.":".$this->pass."@s3s.fr/api/data/1.2/contact/$contactID";
        // ON INITIALISE CURL
        $curl = curl_init($service_url);

        //Dans le cas d'un client qui n'existe pas
        $query = array(
            'fields'    => array(
                array('name' => 'desabo_campagne', 'value' => $options[0]),//Nom de la campagne
                array('name' => 'desabo_motif', 'value' => $options[1]),//Raison
                array('name' => 'is_optin', 'value' => 0),//optin
            )
        );

        $qstring = json_encode($query);

        //Init da requête CURL
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $qstring);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Expect:"));

        //Execution de la requête
        $curl_reponse = curl_exec($curl);
        $response = json_decode($curl_reponse);
        curl_close($curl);

        return $response;
    }

    public function isBlackList($contactID)
    {
        $service_url = "https://".$this->universe.":".$this->pass."@s3s.fr/api/data/1.2/blacklist/$contactID";

        // ON INITIALISE CURL
        $curl = curl_init($service_url);

        //Init da requête CURL
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Expect:"));


        //Execution de la requête
        $curl_reponse = curl_exec($curl);
        $response = json_decode($curl_reponse);

        curl_close($curl);

        return $response;
    }

    public function addBlackList($contactID)
    {
        $service_url = "https://".$this->universe.":".$this->pass."@s3s.fr/api/data/1.2/blacklist/$contactID";

        // ON INITIALISE CURL
        $curl = curl_init($service_url);

        //Init da requête CURL
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Expect:"));


        //Execution de la requête
        $curl_reponse = curl_exec($curl);
        $response = json_decode($curl_reponse);

        curl_close($curl);

        return $response;
    }
}