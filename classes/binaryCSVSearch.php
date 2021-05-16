<?php
/*
    Binary search on a csv file.
    The constructor will return the qualified row or false if the search fails.
    Initial usage is to lookup the HTTP code returned from T.P. and return its meaning
*/    

    class binaryCSVSearch{
        var $csv;           //The array of rows in the csv file
        var $csvcount;      //The number of rows
        var $sep;           //The field separator
        var $fldcount;      //The number of fields per row
        var $index;         //The row index of the tatget values
        var $targetvalue;   //The sought value of the index field
        var $targetrow;     //The row containing the target value in the index field
        
        public function __construct($file, $target, $index=0, $sep= ',')
        {
            //Required field check
            if($file == '' or $index < 0 or $target == '')
            {
                throw new Exception('All fields are required for the csvBinarySearch class.');
            }
            //Make certain the file exists
            if(!file_exists($file))
            {
                throw new Exception('The specified csv file is not found.');
            }
            //Try opening the file.
            if(!$this->csv = file($file))
            {
                throw new Exception('The file '.$file.' could not be opened.');
            }
            //Can thefile be used as an array
            try{
                $this->csvcount = count($this->csv);
            }catch (Exception $e){
                throw new Exception('The specified filedoes not appear to be an array.');
            }
            $this->sep = $sep;
            //Determine the field count per row
            $line1 = $this->csv[1];
            $fields = explode($sep, $line1);
            $this->fldcount = count($fields);
            //Validate the index field number
            if($index > $this->fldcount - 1)
            {
                throw new Exception('Specified field index is > the number of fields per row.');
            }
            $this->index = $index;
            $this->targetvalue = $target;
        }
        
        //Conduct the search of the csv
        public function binarySearch() 
        { 
            $low = 0; 
            $high = count($this->csv) - 1; 
            $id = $this->index; //position of the target field in each row
            
            while ($low <= $high) { 
                
                // compute middle index 
                $mid = floor(($low + $high) / 2); 
               
                $row = $this->csv[$mid]; //middle row
                $flds = explode(',',$row); //to array
                
                $val = $flds[$id]; //value in the target field
                
                
                // If it matches, the target, return the row.
                if($val == $this->targetvalue) { 
                    
                    return $row;
                } 

                //Continue the search
                if ($this->targetvalue < $val) { 
                    // search the left side of the array 
                    $high = $mid -1; 
                } 
                else { 
                    // search the right side of the array 
                    $low = $mid + 1; 
                } 
            } 
            
            // If we reach here the target doesn't exist 
            return false; 
        } 
} 
    
    
    