<div class="accordion" id="accordionExample">
  <div class="card">
    <div class="card-header" id="headingOne">
      <h2 class="mb-0">
        <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
          Your Email and Password
        </button>
      </h2>
    </div>

    <div id="collapseOne" class="collapse " aria-labelledby="headingOne" data-parent="#accordionExample">
      <div class="card-body">
       Your email and password are required as they are used to obtain authorization from Tidepool. 
       If you have run the settings page and provided them there, they are already
       filled. If not, you will have to enter them manually. 
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-header" id="headingTwo">
      <h2 class="mb-0">
        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
          Start and End Dates
        </button>
      </h2>
    </div>
    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
      <div class="card-body">
        The start and end dates refer to the dates of the test results you want to download.
        You may fill in one or both dates. If entering both, the start date must be older to the end date.
        If not, no results will be returned...<br>
        Think of it like a list going from newest to oldest test dates and the start date is somewhere
        down the list and the end date is a newer (higher up the list) date. Tidepool traverses from the
        bottom of the list up.
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-header" id="headingThree">
      <h2 class="mb-0">
        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
          Data Types
        </button>
      </h2>
    </div>
    <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
      <div class="card-body">
        The data type list contains all of Tidepool's types. At this time only the "Self Monitored Blood Gluces" 
        class is supported.
      </div>
    </div>
  </div>
</div>