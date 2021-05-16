<?php
/*
    This is part of the Tidepool data api project.
    
    This class uses the open source FPDF class to generate
    a PDF of the tidepool blood glucose results downloaded 
    from the Tidepool servers.
    
    The table contains 3 columns:
    1. The sample date
    2. The sample time
    3. The sample value in mg/dl
    
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
    
    There are 2 classes in this file:
    PDF - extends the Fpdf class and overrides the header and footer methods.
    pdfTableReport - prepares the input, instances the PDF class to generate the report
                     and saves the report.
    
    The caller adds a value to the input data - "dateCheck". If true the date checking method
    to see if the test date is within the user selected range. If false - as when using a local
    user downloaded file of test results - the dateCheck is set to false and the check skipped.
    
    In the latter case, file scans the local file and sets the start and end dates in the input
    data so it can be set in the page headers.
    

*/    
    use Fpdf\Fpdf;
    
//Extending the Fpdf class so we can override header and footer methods
//and pass the pdf parameters and user inputs.
class PDF extends Fpdf{
    var $data;  //Constructor input data
    var $stats; //Caller computed statistics text
    
    function __construct($orientation='P', $units='in', $size='Letter', $data)
    {
       $this->data = $data; 
       parent::__construct($orientation, $units, $size);
        
    }

    //Create a key to the glucose value colors
    //for display in the headers
    function colorKey()
    {
        //Key words
        $wrds = array('Low','V. Low', 'Low', 'Normal', 'High', 'V.High', 'High');
        //The key words RGB values
        $clrs = Array(
                        array(0,0,0), array(255,0,0), array(255,102,0),
                        array(0,128,0), array(204,0,255), array(102,0,102), array(0,0,0));
        $this->ln();
        $this->Cell(.75);
        for($i=0;$i<7;$i++)
        {
            $l = $this->GetStringWidth($wrds[$i]) + .2;
            $rgb = $clrs[$i];
            $this->SetTextColor($rgb[0], $rgb[1],$rgb[2]);
            $this->Cell($l,.5,$wrds[$i]);
        }
    } 

    //This loads the app config file and outputs the patient
    // name abd DOB
    function patientInfo()
    {
        $conf = file_get_contents('assets/includes/tidepoolConfig.json');
        $conf = json_decode($conf,true);
        $name = $conf['username'];
        $dob  = $conf['birthdate'];
        $dt = new DateTime($dob);
        $dob = $dt->Format('M/d/Y');
        $txt = 'Patient: '.$name.'   DOB: '.$dob;
        $this->Ln();
        $this->Cell(0,0,$txt,0,0,'C');
        
    }

    // Page header
    function Header()
    {
        extract($this->data);
        $this->SetFont('Arial','B',15);
        // Title
        $this->Cell(0,.4,'Blood Glucose Values',0,0,'C');
        $this->SetFont('Arial','',10);
        //Patient id
        $this->patientInfo();
        $this->Ln();
        //Selected date range
        $this->Cell(0,.5,'Date Range: '.$startdate.' - '.$enddate,0,0,'C');
        $this->colorKey();
        $this->Ln(.4);
    }

    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-.75);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        //Print date
        $date = date('m-d-Y  g:ia');
        $txt = 'Date: '.$date.'  Page '.$this->PageNo().'/{nb}';
        // Page number
        $this->Cell(0,1,$txt,0,0,'C');
    }    

    //Set the text color for the passed 
    //value
    
    public function pickTextColor($glucose)
    {
        $rgb = array(0,0,0);
        switch (true)
        {
            case ($glucose < 54):
                $rgb = array(255,0,0); //Red
                break;
            case ($glucose >= 54 and $glucose <= 70):
                $rgb = array(255,102,0); //Orange
                break;
            case ($glucose >= 70 and $glucose <=180):
                $rgb = array(0,128,0); //Green
                break;
            case ($glucose >= 180 and $glucose <= 250):
                $rgb = array(204,0,255); //Lt. Violet
                break;
            case ($glucose > 255):
                $rgb = array(102,0,102); //Purple
                break;
        }
        //Set the chosen color
        $this->SetTextColor($rgb[0],$rgb[1],$rgb[2]);
    }
    // Simple table
    function BasicTable($header, $data)
    {
        // Header
        $this->SetX(.9); //Centering
        foreach($header as $col)
            $this->Cell(2,.25,$col,1);
        $this->Ln();
        // Data
        foreach($data as $row)
        {
            $date = $row[0];
            $time = $row[1];
            $glucose = $row[2];
            $this->SetX(.9); //Centering the table...
            $this->Cell(2,.25,$date,1,0,'C'); //output date
            $this->Cell(2,.25,$time,1,0,'C'); //... 
            $rgb = $this->pickTextColor($glucose);//Glucose value to color
            $this->Cell(2,.25,$glucose,1,0,'C');
            $this->setTextColor(0,0,0);//Reset the color
            $this->Ln();
        }
        //Add a page with the statistics
        $this->AddPage('P', 'Letter');
        $this->Cell(0,2,$this->stats,0,0,'C');
    }
}

class pdftableReport{    
    var $data;          //Params from the constructor
    var $pdf;           //Pdf obj
    var $header = array('Sample Date', 'Sample Time', 'Sample Value (mg/dl)'); //Report Headers
    var $stdDev;        //Standard deviation
    var $avg;           //Average value
    var $nbrVals;       //Data set size
    var $stats;         //Statistics line
    
    
    public function __construct($data)
    {
        $this->data = $data;
        date_default_timezone_set('America/Denver'); //So we get our time right
        
        
    }
    
    //When processing a local file, we need to 
    //build the date range from the data.
    function setDateRange($json_a)
    {
        $strt = '';
        $end = '';

        $first = (array)$json_a[0];
        $dt = new DateTime($first['time']);
        $this->data['enddate'] = $dt->Format('Y-m-d');
        for($i = 0; $i < count($json_a); $i++)
        {
            $m = (Array)$json_a[$i];
            if($m["type"]=='upload') continue;
            $dt = new DateTime($m['time']);
            if ($end == '') //The first date found if the end date
            {
                $end = $dt->Format('Y-m-d');
            }
            else
            {
                $strt = $dt->Format('Y-m-d');
            }
        }
        $this->data['startdate'] = $strt;
        $this->data['enddate'] = $end;
    }
    
    //Mean, count and sd of the data set
    public function stdDeviation($data)
    {
        $sumX   = 0;
        $sumXsq = 0;
        $avgX   = 0;
        $n      = count($data);
        
        //Do the sum of x
        foreach($data as $row) //Row is date,time,value
        {
            $sumX += $row[2];
        }
        $avgX = $sumX/$n;   //Average
        
        //Do the sum of the differences from the mean squared
        foreach($data as $row)
        {
            $x = ($row[2] - $avgX) ** 2;
            $sumXsq += $x;
        }

        $x = $sumXsq/$n;
        $this->stdDev = intval(sqrt($x));
        $this->avg = intVal($avgX);
        $this->nbrVals = $n;
        $this->stats = 'The average of '.$n.' values is '.$this->avg.' with an S.D. of '.$this->stdDev;
    }
                


    //This passes over the json file and extracts all of the qualifiers.
    //Then it instances the PDF class and generates the report.
    public function report($file)
    {
        //Load the tidepool json and decode it into an array of json objects
        $json_a = json_decode(file_get_contents($file), true);

        //If no date check, get the date range from the json data
        if(!$this->data['dateCheck'])
        {
            $this->setDateRange($json_a);
        }
        
        $pdfinput = array();
        
        //Pass over the measurements and add them to the table to be passed to the pdf class
        for($i = 0; $i < count($json_a); $i++){
            $measurement = (Array)$json_a[$i];          //Get the next measurement
            
            //If the object type is "upload" we have reached the end of the measurements
            if($measurement["type"] !='smbg') continue ; //Not fingerstick result?
            //Qualify the time of measurement
           // if($measurement['time'] != '' and $this->data['dateCheck']==true)
          //  {
          //      if(!$this->checkDates($measurement['time'])) continue;
           // }
            $dt = new DateTime($measurement["time"]);       //The measurement time in a date time object
            $tstdate  = $dt->format('M, d, Y');       //Format the date a yyyy-mm-dd
            $tsttime  = $dt->format('h:i a');       //Format the time as hour:minute:second am or pm
            $tstvalue = $measurement['value'];
            if($measurement['units'] != 'mg/dL')
            {
                $tstvalue = intval($tstvalue*18); //Convert mmol/l to mg/dl if needed
            }
            $pdfinput[] = array($tstdate, $tsttime, $tstvalue);
        }
       
        unset($json_a); //Done wid it...
        
        $this->stdDeviation($pdfinput); //Compute stats;
        $pdf = new PDF('P', 'in', 'Letter',$this->data); //Portrait, inches and Letter size
        $pdf->stats = $this->stats; //Pass the stat text
        $pdf->AliasNbPages();
        $pdf->SetFont('Arial','',14);
        $pdf->AddPage();
        $pdf->BasicTable($this->header,$pdfinput);
        
        //Create the document file name
        $now = date('d-m-y-h-ia');
        $pdfname = 'assets/reports/'.$now.'glucoses.pdf'; //Path to the pdf file);
        $_SESSION['pdfname'] = $pdfname;
        //Output the report
        $pdf->Output('F',$pdfname);
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