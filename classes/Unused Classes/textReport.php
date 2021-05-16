<?php
/*
    CLass to generate text files from the Tidepool data download
*/

    class textReport{
        var $lineCount;     //Page control
        var $pageNum;       //Page number    
        var $file;          //Output file handle
        var $data;          //Browser data
        
        public function __construct($data)
        {
            $this->data = json_decode($data,true);    //User inputs
        }
        
        //Write a line to the file
        public function writeString($s)
        {
            $nl = chr(13); //New Line
            $s .= $nl;          
            fwrite($this->file, $s);
        }
       
        public function newPage($pageup)
        {
            $this->lineCount = 0;
            $lineFormat = '          %-10s %-12s %-12s';
            $nl = "\n";
            if($pageup)
            {
                $this->writeString(sprintf('%1s', chr(12))); //page break
            }
            $this->pageNum++;
            $this->writeString(sprintf('          %-10s', 'Page: '.$this->pageNum));
            $this->writeString(sprintf($lineFormat, 'Date', 'Time', 'Value (mg/dl)'));
            $this->writeString(sprintf('%1s',  $nl));
            $this->writeString(sprintf($lineFormat, '----------', '----------', '---------'));
            
    
        }
        
        public function generateReport()
        {
            $this->pageNum = 0;
            
            $string = file_get_contents("assets/jsonfiles/tidepooldata.json"); //Load the test results
            $json_a = json_decode($string, true); //Convert to a json array
            $lineFormat = '          %-10s %-12s %-12s';
            $nlFormat = '%2s';
            $nl = "\n";
            $this->file = fopen('report.txt', 'w');
            $this->newPage(false);
            //Pass over the measurements and add them to the table
            for($i = 0; $i < count($json_a); $i++){
                $obj = (Array)$json_a[$i];          //Get the next measurement
                
                //If the object type is "upload" we have reached the end of the measurements
                if($obj["type"]=='upload') break;
               
               //Qualify the time of measurement
                if($obj['time'] != '')
                {
                    if(!$this->checkDates($obj['time'])) continue;
                }
                
                $dt = new DateTime($obj["time"]);       //The measurement time in a date time object
                $tstdate  = $dt->format('Y-m-d');       //Format the date a yyyy-mm-dd
                $tsttime  = $dt->format('h:i a');       //Format the time as hour:minute:second am or pm
                $tstvalue = intval($obj["value"]*18);   //Get the measurement value -convert from mmol/L to mg/dl
                $this->writeString(sprintf($lineFormat, $tstdate, $tsttime, $tstvalue));
                $this->lineCount++;
                if($this->lineCount == 50) $this->newPage(true);
            }    
            fclose($this->file);
        }
        
    //If user entered either date check the input date qualifies
    function checkDates($testDate)
    {
        $indate = new DateTime($testDate);
        $startdate = $this->data['startdate'];
        $enddate   = $this->data['enddate'];
        $strt = new DateTime($startdate);
        $end  = new DateTime($enddate);
        //Case 1 user did not enter any dates
        if($startdate =='' and $enddate == '') return true; //No dates entered
        //Case 2 start and end dates entered
        if($startdate !='' and $enddate != '')
        {
            if($indate >= $strt and $indate <= $end)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        //Case 3 Start date only
        if($startdate != '' and $enddate == '')
        {
            if($indate >= $strt)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        //Case 4 enddate only
        if($startdate == '' and $enddate != '')
        {
            if($indate <= $end)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }        
            
            
        
    }