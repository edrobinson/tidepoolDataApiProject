<?php
/*
    This class fetches an auth token from the Tidepool system.
    It is used for all further interactions
    API Documentation:
    curl -i -X POST -u masteremail@domain.com https://int-api.tidepool.org/auth/login
    Password: [your account password]

    This will return an HTTP response that looks like this:
    HTTP/1.1 200 OK
    access-control-allow-headers: authorization, content-type, x-tidepool-session-token
    access-control-allow-methods: GET, POST, PUT
    access-control-allow-origin: *
    access-control-expose-headers: x-tidepool-session-token
    access-control-max-age: 0
    content-type: application/json
    date: Fri, 15 Jul 2016 00:25:23 GMT
    x-tidepool-session-token: eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkdXIiOjIuNTkyZSswNiwiZXhwIjoxNDcxMTM0MzIzLCJzdnIiOiJubyIsInVzciI6IjU0YzkwZmIzMjUifQ.bbkzG_rwp9IVMI3HVYm_ct8mMW_YTnTALUW12345678
    Content-Length: 172
    Connection: keep-alive

    {"emailVerified":true,"emails":["demo+intpublicclinic@tidepool.org"],"roles":["clinic"],"termsAccepted":"2017-08-16T10:30:56-07:00","userid":"4533925fea","username":"demo+intpublicclinic@tidepool.org"}

    From the response headers, save the x-tidepool-session-token header value.
    From the response body, save the userid excluding the quotation marks, 4533925fea in this example. 
    
    Usage: require '/assets/classes/getTidepoolAuth.php';
                    $getAuth = new getTidepoolAuth($userid, $password);
                    
    Throws an exception if either variable is missing.
    
    The command line curl is converted to a php curl session.
  
*/
    require 'classes/mapHttpCode.php'; //Http code to text class
    
class getTidepoolAuth{
    var $token;                 //Returned authorization token
    var $body;                  //Body of the response msg
    var $uid;                   //Users id from the browser
    var $pwd;                   //Users password from the browser
    var $result;                //Whole response message
    var $jsonar;                //The json in the response body
    var $tidepoolUserid;        //Response user id value actually a password
    var $tidepoolUsername;      //Response username actually the userid
    var $credentialFile = './assets/includes/tidepoolCredentials.json'; //Where the returned creds are stored
    
    //Constructor takes the userid and password of the patient
    public function __construct($user, $paswd){
        if($user == '' || $paswd == '')
        {
            throw new Exception('Missing user id or password');
        }
        $this->uid = $user;
        $this->pwd = $paswd;
    }

    //The fetchNewToken method retrieves anew token and userid
    //from Tidepool and stores them in an include file.
    //This method trys to load that file and set the token and id
    //so we don't have to call for it every time we're invoked.
    //If successful, it sets the credentials and returns true
    //else returns false.
    private function getCredentialFile()
    {
        if(!file_exists($this->credentialFile)) return false; //No file 
        if(!$json = file_get_contents($this->credentialFile)) return false; //Can't load
        $jsonar = json_decode($json,true);  //Load the creds
        $this->token = $jsonar['token'];    //Make them available
        $this->tidepoolUserid = $jsonar['userid'];

        return true; //We're done.
    }
    
    //Create the credential file after getting the creds from Tidepool.
    private function makeCredentialFile()
    {
        $arr   = array('token' => $this->token, 'userid' => $this->tidepoolUserid);
        $creds = json_encode($arr);
        file_put_contents($this->credentialFile, $creds);
    }
        
    //get a fresh token
    public function fetchNewToken()
    {
        //Do we already have the Tidepool credentials?
        if($this->getCredentialFile())
        {
            return;
        }
        
        //Need to ask for new ones. Continue 
       
       $url = 'https://int-api.tidepool.org/auth/login'; //TODO change if ever in production
      
       $ident = $this->uid.':'.$this->pwd;     //Build user id:password
       
       //Construct the curl options array
       $options = array(CURLOPT_URL => $url,
                         CURLOPT_RETURNTRANSFER => true,
                         CURLOPT_POST => true,
                         CURLOPT_HEADER => true,
                         CURLINFO_HEADER_OUT => true,
                         CURLOPT_USERPWD => $ident);
        
        $ch = curl_init();
        
        curl_setopt_array($ch, $options);
        
        $result = curl_exec($ch); //Execute the curl session and get the token reply
        
        if (curl_errno($ch)) {
            throw new Exception('RequestError:' . curl_error($ch));
        }
        
        $this->result = $result;

        //Get the token
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($result, 0, $header_size); //response header
        
        $headera = explode(':', $header);//break the headers on the embedded colons
        
        $token = $headera[2];                           //the token plus ' date' after it
        
        $tlen = strlen($token)-5;                       //length minus the "date:" word
        
        $this->token =  trim(substr($token,0,$tlen));   //The full token
        
        $this->body = substr($result, $header_size);    //Resonse body json string

        $this->jsonar = json_decode($this->body, true); //The json to an array

        $this->tidepoolUserid = $this->jsonar['userid'];       //The server provided user id
        $this->tidepoolUsername = $this->jsonar['username'];   //and username
        
        //Check the host response
        $httpResponseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($httpResponseCode == 200)
        {
            $this->makeCredentialFile();                    //Save for future use
            return true;
        }
        else
        {
            file_put_contents('request.txt',curl_getinfo(CURLINFO_HEADER_OUT)); //Request for debugging
            file_put_contents('result.txt', $this->result); //Return for debugging
            $meaning = $this->lookupHttpCodeMeaning($httpResponseCode);
            throw new Exception('Failed to renew credentials. HTTP Code = '.$httpResponseCode.' - '.$meaning);
        }
    }
    
    //HTTP Code to text - returns the textual meaning of an HTTP code.
    //Input:    HTTP code to be looked up
    //Output:   The row of the file the code is matched to
    public function lookupHttpCodeMeaning($httpCode)
    {
        //Inputs the the csv search class   
        //  1. Path to the csv of http codes
        //  2. The http code to be searched for
        //  3. The index of the code field in the csv records
        //  4. Optional Field seperator - defaults to comma
        $bsearch = new binaryCSVSearch('assets/includes/http-status-codes.csv',$httpCode,0, ',');
        if(!$res = $bsearch->binarySearch())
        {
            return 'Code not found.';
        }
        else
        {
            $parts = explode(',', $res);
            return $parts[1];
        }
    }        
    
    
    //Returns the token
    public function readToken()
    {
        return $this->token;
    }

    //Get returned userid
    public function readUserid()
    {
        return $this->tidepoolUserid;
    }
    
    //Returns the body
    public function readBody()
    {
        return $this->body;
    }

    //Get the entire response
    public function readResponse()
    {
        return $this->result;
    }

    //Get the json array
    public function readJson()
    {
        return $this->jsonar;
    }

    //Get the returned user name
    public function readUsername()
    {
        return $this->tidepoolUsername;
    }
    
    

}