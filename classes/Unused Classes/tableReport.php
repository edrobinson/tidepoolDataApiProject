<?php
/*
    This is part of the Tidepool data api project.
    
    Using phpWord, create a PDF document with a table in it.
    
    The table will contain 3 columns:
    1. The sample date
    2. The sample time
    3. The sample value in mg/dl
    
    Then load the tidepool json file and populate the table.
    Then save the it as a docs.
    
    The phpWord class is part of the phpOffice suite.
    
    See the phpWord samples in Glucose Decoder/samples folder.
    
    The Tidepool json objects look like this:
    
    {"conversionOffset":0,
    "deviceId":"AbbottFreeStyleLite DCGU118-N0731",
    "deviceTime":"2021-02-09T09:22:00",
    "id":"fmpsu2fpfa652rn3t0ba3bdleafk16sb",
    "localTime":"2021-02-09T09:22:00.000Z",
    "payload":"{\"logIndices\":[0]}",
    "time":"2021-02-09T16:22:00.000Z",  <-- the measurement timestamp
    "timezoneOffset":-420,
    "type":"smbg",                      <-- Self Managed Blood Glucose (Finger Stick)
    "units":"mg/dL",
    "uploadId":"upid_9c4ee611cdee",
    "value":172}                        <-- the measurement value

*/    
    //require('/assets/vendor/autoload.php');
    
    //Using the phpOffice suite Word generator
    use PhpOffice\PhpWord\Settings;
    use PhpOffice\PhpWord\Shared\Converter;
    use PhpOffice\PhpWord\Style\TablePosition;
    
class tableReport{    
    var $data;
    
    public function __construct($data)
    {
        $this->data = $data;
    }
        
    public function report()
    {
        //Styling to center text in the table cells
        $cstyle = array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER);

        //Basic setup
        $phpWord = new \PhpOffice\PhpWord\PhpWord();    //Instance the Word class
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(14); 
        $phpWord->getDocumentProperties();
        
        $section = $phpWord->addSection();                  //Add a section to the document
        $headerstyle = array('size' => 16, 'bold' => true); //Header style
        
       
        
        $section->addText('Tidepool Glucose Values', $headerstyle); //and a big bold section header
        $table = $section->addTable(array('width'=>'50%'));         //Insert a word table
        
        //Add a header row to the table
        $table->addRow();
        $table->addCell(3000)->addText('Sample Date',null,$cstyle);
        $table->addCell(3000)->addText('Sample Time',null,$cstyle);
        $table->addCell(3000)->addText('Sample Value (mg/dl)',null,$cstyle);
        
        //Load the tidepool json and decode it into an array of json objects
        $string = file_get_contents("assets/jsonfiles/tidepooldata.json");
        $json_a = json_decode($string, true);
        
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
            
            $table->addRow();                       //Add a new row to the table
            
            //Add the values in cells in the row
            $table->addCell(3000)->addText($tstdate,null,$cstyle);
            $table->addCell(3000)->addText($tsttime,null,$cstyle);
            $table->addCell(3000)->addText($tstvalue,null,$cstyle);
        }    

        //Write out the document
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'PDF'); //Write the Word to a PDF
        //Create the document file name
        $dt = $dt = new DateTime('now');
        $now = $dt->format('Y_m-d-h-i');
        $pdfname = './assets/reports/'.$now.'glucoses.pdf';//Path to the pdf file
        $objWriter->save($pdfname); //Save the file
        $_SESSION['pdfname'] = $pdfname; //For the page display pdf
    }
    
    //If user entered either date check the input date qualifies
    function checkDates($testDate)
    {
        $indate = new DateTime($testDate);
        //Case 1 user did not enter any dates
        if($this->data['startdate'] =='' and $this->data['enddate'] == '') return true; //No dates entered
        //Case 2 start and end dates entered
        if($this->data['startdate'] !='' and $this->data['enddate'] != '')
        {
            $strt = new DateTime($this->data['startdate']);
            $end  = new DateTime($this->data['enddate']);
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
        if($this->data['startdate'] != '' and $this->data['enddate'] == '')
        {
            $strt = new DateTime($this->data['startdate']);
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
        if($this->data['startdate'] == '' and $this->data['enddate'] != '')
        {
            $end  = new DateTime($this->data['enddate']);
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