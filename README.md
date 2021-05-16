# tidepoolDataApiProject
 PHP based Tidepool data api implementation
 
 Tidepool.org is a non-profit whose interest is in making it easier for diabets and their health providers to manage their diabetes related data.
 They welcome interested developers to add to their offerings.
 
 This application uses PHP to retrieve a user's data from the Tidepool servers and present it in a useful form - PDF.
 
 Development was done using a WAMP stack under Windows-10. It should, however, be fairly portable.
 
 There is no formal documentation at this time. 
 
 The app is triggered by the index.php in the root directory. It loads the class specified in the config file. This page accepts the user's 
 credentials and runs the apis to get authorization followed by download of the sored blood glucose data. Then a PDF is generated and displayed
 in the browser.
 
