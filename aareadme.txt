2/27/21

This folder contains all sources for my Tidepool data aquisition API development.

My primary reference is the 'Using Tidepool Data APIs.odt' in the doc folder in assets.
There are additional online resources.

Server side everything is PHP with the exception of a few node experiments.

Client side uses Smarty templated HTML, JS, JQuery , Bootstrap styling and js,css and script
generated by the Jaxon PHP framework on the server side.

The Tidepool API is managed in a single class - tidepoolAPI - located in the classes folder.
It uses the Requests library at https://requests.ryanmccue.info/docs/usage.html.
This library reduces the entire api coding to < 200 lines of code and comments and is very
fast and simple to use. I used it after experiencing some difficulty with PHP's Curl implementation.

Project Structure:

wamp64/www/tidepoolDataAPIProject
    |
    |
    assets
        |
        docs
        |
        help
        |
        includes
        |
        js
        |
        jsonfiles
        |
        libs
            |
            smarty
                |
                templates
        |
        misc
        |
        reports
        |
        styles
        |
        vendor
    |
    classes
    |
    uploads
    
        

