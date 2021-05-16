<?php
/*
    Get Tidepool device data. i.e. glucose values, etc.
    
    The curl command line:
    
    curl -s -X GET -H "x-tidepool-session-token: <your-session-token>" -H "Content-Type: application/json" "https://int-api.tidepool.org/data/<subject-userid>?type=<type code>"
*/    

    class getMeterdata{
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
            
        //Run a curl session to fetch the data 
    //curl -s -X GET -H "x-tidepool-session-token: <your-session-token>" -H "Content-Type: //application/json" //"https://int-api.tidepool.org/data/<subject-userid>?type=<type code>"
        
        public function readMeterData()
        {
            //Create the request headers array
            $headers = array();
            $headers[] = 'X-Tidepool-Session-Token:'.$this->token;
            $headers[] = 'Content-Type:application/json';
            

            //Build the query string
            $query = '?type=smbg'; //Only supporting finger stick results now
            
            //Use only date
            if($this->startdate != "")
            {
                $query .= '&startdate='.$this->convertDate($this->startdate);
            }
            else if($this->enddate != "")
            {
                $query .= '&enddate='.$this->convertDate($this->enddate);
            }
            
           // $querylen = strlen($query);
            //Add the length header
           // $headers[] = 'Content-length:'.$querylen;
            
            
            //The url becomes:
            $url = 'https://int-api.tidepool.org/data/'.$this->user.$query;
            $url = trim($url);
            
            //Set all of the options
            $options = array(CURLOPT_RETURNTRANSFER => true,
                             CURLOPT_URL => $url,
                             CURLOPT_TIMEOUT => 10,
                             CURLOPT_MAXREDIRS => 10,
                             CURLOPT_HEADER => true,
                             CURLINFO_HEADER_OUT => true,
                             CURLOPT_HTTPHEADER => $headers,
                             CURLOPT_HTTPGET => true,
                             CURLOPT_VERBOSE => true);

            //Create the curl session
            $ch = curl_init();
                             
            //and add them to the curlobject
            curl_setopt_array($ch, $options);
            
            
            //Execute the curl request capturing the returned http response
            $this->result = curl_exec($ch);
            
            //Oops...
            if (curl_errno($ch)) {
                //var_dump(curl_getinfo($ch)); 
                throw new Exception('RequestError:' . curl_error($ch));
            }
            
            //Save the request headers
            $reqheader = curl_getinfo($ch, CURLINFO_HEADER_OUT);
            file_put_contents('./request.txt',$reqheader);
            
            //Get the body of the response - json
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE); //The length of the response headers
            $this->body = substr($this->result, $header_size); //The body contains the json reply
            file_put_contents('curlresult.txt', $this->result); //Remove after dev.
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
            