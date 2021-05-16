<?php
/*
   Tidepool Project Configuration class
   Add to as needed.

*/
class Config {
	//App. default page
	public $defaultPage = 'TidepoolMainV2';            //First page from index
    
	
    //Text for the footer copyright notices
    public $footerText = ' by Ed Robinson and RSI. All rights reserved.';
	
    //Text for title tag and nav brand element
    public $brands = array('Login' => '',
                           'Processing'=>'',  
                           'default' => 'Tidepool Data Reporting',
                           );
}                          
?>