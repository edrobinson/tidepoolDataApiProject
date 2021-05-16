<?php
/*
    Lookup the http code meaning using the csv search class
*/
    require 'classes/binaryCSVSearch.php';  //Class to search a vsv file
    
    class mapHttpCode{

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
    }