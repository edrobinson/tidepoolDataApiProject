<?php
/*
    This class gets a set of user metadata from the Tidepool system.
    The metadata provides user credentials for the next step of downloading data.
    
    curl -s -X GET -H "x-tidepool-session-token: <your-session-token>" -H "Content-Type: application/json" "https://int-api.tidepool.org/metadata/users/<your-userid>/users"

    This returns all of the users' metadata associated with the given <your-userid>, including what permissions it has upon those associated users. This step is optional. You may only need to do it if you have not previously stored the user ids of the individual patients or study subjects.

    From this list, find the user id of the patient (or study subject) whose data you wish to fetch. Referenced as <subject-userid> below.

    Continuing with our example we would use:
    curl -s -X GET -H "x-tidepool-session-token: ey...uk" -H "Content-Type: application/json" "https://int-api.tidepool.org/metadata/users/4533925fea/users"

    
    [{"emailVerified":true,"emails":["demo+jill@tidepool.org"],"termsAccepted":"2017-08-03T12:19:54-07:00","userid":"5d509deb6b","username":"demo+jill@tidepool.org","trustorPermissions":{"view":{}},"profile":{"fullName":"Jill Jellyfish","patient":{"birthday":"2000-01-01","diagnosisDate":"2000-01-01","targetDevices":["omnipod","dexcom"],"targetTimezone":"US/Pacific"}}},{"userid":"0223d994e9","trustorPermissions":{"custodian":{},"upload":{},"view":{}},"profile":{"fullName":"Marissa Medpumper","patient":{"birthday":"2000-01-17","mrn":"123456-mm","targetDevices":["carelink"],"targetTimezone":"US/Pacific"}}}]
*/
    class getUserMetadata{
        var $userid;
        var $token;
        var $result;
        var $curlinfo;
        var $body;
        public function __construct($intoken, $inuserid)
        {
            if($inuserid =='' or $intoken == '')
            {
                throw new Exception('Missing user id or token.');
            }
            $this->token = $intoken; 
            //echo 'ClassToken: '.$this->token.'<BR><BR>';
            $this->userid = $inuserid;
            //echo 'ClassUserid: '.$this->userid.'<BR><BR>';
            return true;
        }
        
        //Call for tehe metatata
        public function fetchMetadata()
        {
            $headers = array();
            $headers[] = 'X-tidepool-session-token:'.$this->token;
            $headers[] = 'Content-Type:application/json';
            $headers[] = 'Content-Length: 0';
            //$headers[] = 'Expect:';
            
            $url =  'https://int-api.tidepool.org/metadata/users/'.$this->userid.'/users';
            //echo 'metadata url: '.$url.'<BR><BR>';
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
            //curl_setopt($ch, CURLOPT_HTTPGET,1);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            
            
            $this->result = curl_exec($ch);
            
            if (curl_errno($ch)) {
                throw new Exception('RequestError:' . curl_error($ch));
            }
            $this->curlinfo = curl_getinfo($ch);
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $this->body = substr($this->result, $header_size); //Resonse body json string
            curl_close($ch);
        }
    //Get the entire response
    public function readResponse()
    {
        return $this->result;
    }
    
    //Get the json brom the response body
    public function readBody()
    {
        return $this->body;
    }
        
     

    //Get the curl info - debugging
    public function readInfo()
    {
        return $this->curlinfo;
    }
            
    }       