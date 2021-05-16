<?php
/*
    runing the api with Guzzle
*/    
    //Run the authorization request
    //curl -i -X POST -u masteremail@domain.com https://int-api.tidepool.org/auth/login
    //Password: [your account password]
    
    require "assets/vendor/autoload.php";
    
    $client = new GuzzleHttp\Client([
    'base_uri' => 'https://int-api.tidepool.org',
    'auth' => ['edrobinsonjr@gmail.com','edr123467>']]);
    
    $response = $client->post('/auth/login');
    $body = $response->getBody();
    $ja = json_decode($body,true);
    $userid = trim($ja['userid']);
    file_put_contents('userid.txt', $userid);
    //echo 'UserID:'.$userid.'<BR><BR>';
    $token = $response->getHeader('x-tidepool-session-token');
    //var_dump($token);
    $token = trim($token[0]);
    //echo $token;
    file_put_contents('token.txt',$token);
    //Run the data request
    //curl -s -X GET -H "x-tidepool-session-token: <your-session-token>" -H "Content-Type: application/json" "https://int-api.tidepool.org/data/<subject-userid>"
    
    //Create the header aray
    $headers = array();
    $headers[] = 'x-tidepool-session-token:'.$token;
    $headers[] = 'Content-Type:application/json';
    //$headers[] = 'User-agent:'. $_SERVER['HTTP_USER_AGENT'];
   // var_dump($headers);
    $params = ['query' => ['type' => 'smbg']];
    
    $url = 'https://int-api.tidepool.org/data/'.$userid.'?type=smbg';
    //echo 'URL: '.$url.'<BR><BR>';
    $client = new GuzzleHttp\Client([    
    'headers' => $headers]);
     
    //var_dump($client);
    try{
        $response = $client->get($url);
    }catch(Exception $e){
        echo 'Guzzle message: '.$e->getMessage();
    }
    
    //var_dump($response);//->getBody());



