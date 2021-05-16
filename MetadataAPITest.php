<?php
/*
    Test the Tidepool Metadata retrievel api.
*/
    require 'classes/tidepoolAPI.php';          //Simplified API class
    
    $tpAPI = new tidepoolAPI();
    
    $uid = 'demo+intpublicclinic@tidepool.org';
    $pwd =   'eureka-charcoal-longbow';

    
    //$pwd = 'edr123467>';
   // $uid = 'edrobinsonjr@gmail.com';
    
    if($tpAPI->getTidepoolAuthorization($uid, $pwd))
    {
        try{
            $response = $tpAPI->getTidepoolMetadata();
        }catch(Exception $e){
            echo 'Metadata retrieval threw an exception:<br>';
            echo $e->message.' on line '.$e->line.'<br>';
        }
        if(!$response)
        {
            echo 'Metadata Retrieval Failed';
        }
        else
        {
            echo 'Metadata Retrieval Succeded.<br>';
            $data = file_get_contents('assets/jsonfiles/tidepoolmetadata.json');
            
            $json = json_decode($data);
            var_dump($json);
        }
    }
    else
    {
        echo 'Tidepool Authorization Failed.';
    }