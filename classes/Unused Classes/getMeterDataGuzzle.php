<?php
/*
    Get Tidepool device data. i.e. glucose values, etc. using GuzzlerHttp client
    
    The curl command line:
    
    curl -s -X GET -H "x-tidepool-session-token: <your-session-token>" -H "Content-Type: application/json" "https://int-api.tidepool.org/data/<subject-userid>?type=<type code>"
*/    
    use GuzzleHttp\Client;

    
    class getMeterdataGuzzle{
        var $token;
        var $user;
        var $type;
        var $result;
        var $body;
        var $startdate;
        var $enddate;
        var $datatype;
        
        public function __construct($token='', $user='', $type='', 
                                    $startdate='', $enddate='')
        {
            if($token =='' or $user == '')
            {
                throw new Exception('Missing user id or token.');
            }
            if($type !='smbg') $type = 'smbg'; //Default the data type. No others supported
            $this->token = $token;
            $this->user  = $user;
            $this->type  = $type;
            $this->startdate = $startdate;
            $this->enddate = $enddate;
            $this->datatype = $type;
        }
            
        //Make an http call for the user data
        public function readMeterData()
        {
            //Create the request headers array
            $headers = array('X-Tidepool-Session-Token' => $this->token,
                             'Content-Type' => 'application/json');

            //Build the query string
            $query = array();
            $query['type'] = 'smbg';    //Bloog glucoses
            
            //Use only one of the dates or niether
            if($this->startdate != "")
            {
                $query['startDate'] = $this->convertDate($this->startdate);
            }
            else if($this->enddate != "")
            {
                $query['endDate'] = $this->convertDate($this->enddate);
            }
           
            
            //The url becomes:
            $url = 'https://int-api.tidepool.org/data/'.$this->user;

            //Instance the Guxxle client 
            $client = new GuzzleHttp\Client($headers);
            
            
            $response = $client->get($url,$query);

            $code = $response->getStatusCode();
            if($code != 200)
            {
                throw new Exception('Data request failed with code'.$code);
            }
            $body = $response->getBody();
            if(!$body)
            {
                throw new Exception('Data request did not return anything.');
            }
            $this->body = $body; //Save the json
            

/*
            //Save the request headers
            $reqheader = curl_getinfo($ch, CURLINFO_HEADER_OUT);
            file_put_contents('./request.txt',$reqheader);
            
            //Get the body of the response - json
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE); //The length of the response headers
            $this->body = substr($this->result, $header_size); //The body contains the json reply
            file_put_contents('curlresult.txt', $this->result); //Remove after dev.*/
        }
        
        //This converts our mm/dd/yyyy to a 2015-10-10T15:00:00.000Z format
        //'yyyy-MM-dd\'T\'HH:mm:ss.SSS\'Z\'
        //dt.formatGmt('yyyy-MM-dd\'T\'HH:mm:ss.SSS\'Z\
        //dt.formatGmt('yyyy-MM-dd\'T\'HH:mm:ss.SSS\'Z\''
        private function convertDate($dt)
        {
            $date = new DateTime($dt);
            $d = '';
            $d  = $date->format('Y-m-d');
            $d .= 'T';
            $d .= $date->format('h:i:s:v');
            $d .= 'Z';
           // var_dump($d); die;
            return $d;
        }
        //Return the json as received. The caller does whatever with it.
        public function readBody()
        {
            return $this->body;
        }
        
        //Return the htp response text
        public function readHTTPResult()
        {
            return $this->result;
        }
        
    }        
            