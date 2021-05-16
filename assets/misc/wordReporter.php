<?php
/**
    This file reads a json file of glucose meter values 
    obtained from dexcom.com as written by tidepool.com's
    diabetes data handling service and creates a spreadsheet
    which is saved in a pdf file.
    
    
    The json is as follows:
    
    {"conversionOffset":0,
    "deviceId":"AbbottFreeStyleLite DCGU118-N0731",
    "deviceTime":"2021-02-09T09:22:00",
    "id":"fmpsu2fpfa652rn3t0ba3bdleafk16sb",
    "localTime":"2021-02-09T09:22:00.000Z",
    "payload":"{\"logIndices\":[0]}",
    "time":"2021-02-09T16:22:00.000Z",  <-- the timestamp used
    "timezoneOffset":-420,
    "type":"smbg",                      <-- is upload after value records
    "units":"mg/dL",
    "uploadId":"upid_9c4ee611cdee",
    "value":172}                        <-- the sample value
    
    We use the open source PhpOffice/PhpWord library
    to generate a spreadsheet and save it to a pdf file.
    the phpSpreadsheet docs are online.
    
    We install and use the Mpdf library as the pdf writer as that
    is the library pdfSpreadsheet comes with...
    
    The entire thing happens in a single/no functions line

**/

    require_once('assets/vendor/autoload.php');
class wordReportWriter{
    var $jsonfile;

    public function __construct($jsonfile)
    {
       
        $this->jsonfile = $jsonfile;
    }
        
    public function generateWordReport()
    {
        //Instance the Word class
        $word = new \PhpOffice\PhpWord\PhpWord();
        //Load the json file and decode it
        $string = file_get_contents($this->jsonfile);
        //print_r($string); die;
/*        if(strlen($string <3)) //Did we get an empty json?
        {
            throw new Exception(' Invalid data file received from tidepool.');
        }*/
        $json_a = json_decode($string, true);
//var_dump($json_a); die;
        //Read and write the values into the word obj.
        $sect = $word->addSection(); //Add a section to the document
        $header = "Date               Time           Value (mg/dl)";
        $header2= "--------------     ----------       ----------------";
        $sect->addText($header, array('name' => 'Tahoma', 'size' => 14));
        $sect->addText($header2, array('name' => 'Tahoma', 'size' => 14));
        for($idx = 0; $idx < count($json_a); $idx++){
        $obj = (Array)$json_a[$idx];
        if($obj["type"]=='upload') break; //Row after the last reading
        $ttime = $obj["time"];
        $dt = new DateTime($ttime);
        $tstdate  = $dt->format('Y-m-d');
        $tsttime  = $dt->format('h:i a');
        $tstvalue = intval($obj["value"] * 18);// The value converted from mmol/l to mg/dl

        $line   = "$tstdate     $tsttime     $tstvalue";
        $sect->addText($line, array('name' => 'Tahoma', 'size' => 14));
        } 

        //Create a writer obj and save the document to it
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($word, 'ODText');
        $fname = './reports/glucoses.odt';
        $objWriter->save($fname);
    }
    
}    
    
 
 