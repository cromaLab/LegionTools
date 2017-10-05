    <!DOCTYPE html>
    <html>
    <head>
      <title>Retainer trigger</title>
      <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
      <script type="text/javascript" src="Retainer/scripts/gup.js"></script>
      <script type="text/javascript" src="Retainer/scripts/trigger.js"></script>
      <!-- <script type="text/javascript" src="scripts/getMoneyOwed.js"></script> -->
      <script type="text/javascript" src="Retainer/scripts/writeNumOnline.js"></script>
      <script type="text/javascript" src="Retainer/scripts/bootstrap.touchspin.js"></script>
      <script type="text/javascript" src="Retainer/scripts/hitsOverview.js"></script>
      <script type="text/javascript" src="Retainer/scripts/jquery.blockUI.js"></script>
      <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>

      <script> var baseURL = "<?php include('config.php'); echo $baseURL; ?>"; </script>

      <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
      <link rel="stylesheet" href="Retainer/style/trigger.css">
      <link href="css/style.css" rel="stylesheet">
    </head>
    <body>

      <div class="blocky">
        <div class="container">

          <div id = "loginModal" class="modal fade">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <!-- <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button> -->
                  <h4 class="modal-title">Login with your Mechanical Turk keys</h4>
                </div>
                <div class="modal-body">
                  <input id = "modalAccessKey" type="text" class="form-control" name="accessKey" placeholder="Access Key" required="" autofocus="" />
                  <input id = "modalSecretKey" type="text" class="form-control" name="secretKey" placeholder="Secret Key" required=""/>

                  </br>Your keys are never stored on our server.   
                </div>
                <div class="modal-footer">
                  <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->
                  <button id = "modalLoginButton" type="button" class="btn btn-primary">Login</button>
                </div>
              </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
          </div><!-- /.modal -->


          <!-- Modal -->
          <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title">Edit waiting page instructions</h4>
                </div>
                <div class="modal-body">
                  <textarea id = "waitingInstructions" class="form-control" rows="5"></textarea>
                </div>
                <div class="modal-footer">
                  <button id="waitingInstructionsUpdated" type="button" class="btn btn-primary" data-dismiss="modal">Save and close</button>
                  <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                </div>
              </div>
            </div>
          </div>

            <!-- Modal for confirming bonus amount -->
            <div class="modal fade" id="bonusModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Do you want to bonus the following amount?</h4>
                        </div>
                        <div class="modal-body">
                            <br />
                            <p>Bonus Amount:</p>
                            <input id = "modalBonusAmount" type="text" class="form-control" name="bonusAmountField" placeholder="0.01" required="" autofocus="" />
                            <br /><p>Reason:</p>
                            <input id = "modalBonusReason" type="text" class="form-control" name="bonusReasonField" required="" value="Great work." />
                            <br /><p>Worker ID:</p>
                            <input id = "modalBonusWorkerId" type="text" class="form-control" name="bonusWorkerIdField" required="" readonly />
                            <br /><p>Assignment ID:</p>
                            <input id = "modalBonusAssignmentId" type="text" class="form-control" name="bonusAIDField" required="" readonly />
                        </div>
                        <div class="modal-footer">
                            <button id="sendBonusButtonInModal" type="button" class="btn btn-primary" data-dismiss="modal">Send Bonus</button>
                            <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                        </div>
                    </div>
                </div>
            </div>

          <div class="row">
            <div class="col-md-6">
              <div class="cool-block">
                <div class="cool-block-bor">
                  <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#sectionA">Load Panel</a></li>
                    <li id = "recruitingTabLi" class = "disabled"><a data-toggle="tab" href="#sectionB">Recruiting Panel</a></li>
                  </ul>
                  <div class="tab-content">
                    <!-- Load Panel -->
                    <div id="sectionA" class="tab-pane fade in active">
                      <br />
                      <span class="grandTitre text-primary">Load an old experiment</span>
                      <form class="form-horizontal" role="form">
                        <div class="form-group air">  
                          <label class="sr-only" for="taskSessionLoad">Load an old experiment</label>
                          <div class="col-lg-4 pull-right">
                            <select id = "taskSessionLoad" class="form-control"></select>  
                          </div>
                          <button type="button" id='deleteExperiment' disabled='disabled' class="btn btn-danger pull-right" style='margin-left:10px;'>Delete</button>
                          <button type="button" id='copyExperiment' disabled='disabled' class="btn btn-default pull-right">Copy</button>
                        </div>
                        <!-- <button type="submit" id="loadTask" class="btn btn-default">Load</button> -->
                      </form>

                      <span class="grandTitre text-primary">OR create a new experiment</span>
                      <form class="form-horizontal" role="form">
                        <div class="form-group air">
                          <label for="taskSession" class="col-sm-4 control-label">Experiment name (remember this):</label>
                          <div class="col-sm-8">
                            <input type="text" class="form-control" id="taskSession" placeholder="Enter a task session name">
                          </div>
                        </div>
                        <div class="form-group air">
                          <label for="hitTitle" class="col-sm-4 control-label">HIT Title</label>
                          <div class="col-sm-8">
                            <input type="text" class="form-control" id="hitTitle" placeholder="Enter HIT title">
                          </div>
                        </div>
                        <div class="form-group air">
                          <label for="hitDescription" class="col-sm-4 control-label">HIT Description</label>
                          <div class="col-sm-8">
                            <input type="text" class="form-control" id="hitDescription" placeholder="Enter HIT description">
                          </div>
                        </div>
                        <div class="form-group air">
                          <label for="hitKeywords" class="col-sm-4 control-label">HIT Keywords</label>
                          <div class="col-sm-8">
                            <input type="text" class="form-control" id="hitKeywords" placeholder="Enter HIT keywords (space-separated)">
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="country" class="col-sm-4 control-label">Worker country</label>
                          <div class="col-sm-3">
                            <select id = "country" class="form-control">
                              <option>All</option>
                              <option>US</option>
                            </select>
                          </div>
                          <label for="percentApproved" class="col-sm-3 control-label">Min % approved</label>
                          <div class="col-sm-2">
                            <input type="number" min = "0" max = "100" class="form-control text-center" id="percentApproved" value = "0">
                          </div>
                        </div>

                        <div class="form-group air">
                          <div class="col-lg-offset-2 col-lg-10">
                            <div class="cta">
                              <div class="cta-buttons">
                                <button type="submit" id="addNewTask" class="btn btn-info btn-lg pull-right">Add new experiment</button>
                                <button disabled="disabled" type="submit" id="updateTask" class="btn btn-default btn-lg pull-right">Update</button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </form>
                    </div>
                    <!--/ End Load Panel-->

                    <!-- Recruiting Panel -->
                    <div id="sectionB" class="tab-pane fade">
                      <div id = "recruitingDiv">
                        <span class="grandTitre text-primary">Recruiting</span>
                        <br /><br />
                        <form class="form-horizontal" role="form">
                          <div class="form-group">
                            <div class="btn-group col-lg-offset-1 col-lg-10">
                              <button id="yesSandbox" type="button" class="btn btn-lg btn-default active">Sandbox</button>
                              <button id="noSandbox" type="button" class="btn btn-lg btn-default">Live</button>
                            </div>
                          </div>

                          <div class="form-group">

                           <div class="col-lg-10 col-lg-offset-1">
                             <div class="input-group">
                              <span class="input-group-addon">Access Key</span>
                              <input type="text" id="accessKey" class="form-control" placeholder="Optional, can also edit amtKeys.php">
                            </div>
                          </div>
                        </div>

                        <div class="form-group">
                         <div class="col-lg-10 col-lg-offset-1">
                           <div class="input-group">
                            <span class="input-group-addon">Secret Key</span>
                            <input type="text" id="secretKey" class="form-control" placeholder="Optional, can also edit amtKeys.php">
                          </div>
                          <div class="checkbox">
                            <label>
                              <input id = "requireUniqueWorkers" type="radio" value="">
                              Require unique workers
                            </label>&nbsp;
                            <button id="resetUniqueWorkers" class="btn btn-danger btn-xs">Reset History</button>
                            <button id="deleteMturkKeys" class="btn btn-info btn-xs">Delete Keys</button>
                          </div>
                        </div>
                      </div>
                      <div class="form-group">
                       <label class=" col-lg-offset-1 col-lg-6 control-label">For account balance, visit <a href="https://requester.mturk.com/mturk/youraccount" target="_blank">here.</a></label>

                     </div>
                     <div class="form-group">
                      <!-- Nav tabs -->
                      <ul class="nav nav-tabs" role="tablist">
                        <li id = "useRetainerMode" class="active"><a href="#retainerTab" role="tab" data-toggle="tab">Retainer</a></li>
                        <!-- <li id = "useAutoMode"><a href="#retainerTab" role="tab" data-toggle="tab">Auto</a></li> -->
                        <li id = "useDirectMode"><a href="#directTab" role="tab" data-toggle="tab">Direct (classic)</a></li>
                      </ul>
                    </div>
                  </form>
                  <!-- Tab panes -->
                  






                  <div class="tab-content">
                    <div class="tab-pane active" id="retainerTab">
                      <div id="touchSpinDiv">

                        <form class="form-horizontal" role="form">

                          <div class="form-group someAirForRecruitingPanel">
                            <label class="col-lg-6 col-lg-offset-1 control-label">Target number of assignable HITs</label>
                          </div>

                          <div class="form-group someAirForRecruitingPanel">
                            <div class="col-lg-10 col-lg-offset-1">
                              <input id="currentTarget" class="text-center" type="text" value="0" name="currentTarget">
                            </div> 

                            <script>
                            $("input[name='currentTarget']").TouchSpin({
                              min: 0,
                              max: 100,
                              mousewheel: false
                            });
                            </script>

                          </div>
                        </form>
                      </div>

                      <div id="priceRangeDiv"><p><form class="form-horizontal" role="form">
                        <div class="form-group someAirForRecruitingPanel">
                          <div class="col-lg-5 col-lg-offset-1">
                            <label for="minPrice">Min task price (cents)</label>
                            <input type="text" class="form-control" id="minPrice" placeholder="Min price in cents">
                          </div> 
                          <div class="col-lg-5">
                            <label for="maxPrice">Max task price (cents)</label>
                            <input type="text" class="form-control" id="maxPrice" placeholder="Max price in cents">
                          </div>
                          <div class="col-lg-10 col-lg-offset-1">
                            <br/>
                            <label for="thirdPartyUrl">Instructions Page</label>
                            <input type="text" class="form-control" id="instrPage" placeholder="Workers will see this page before they accept the HIT">
                            <br/>
                            <label for="thirdPartyUrl">Tutorial Page</label>
                            <input type="text" class="form-control" id="tutPage" placeholder="Tutorial URL to route workers">
                            <br/>
                            <label for="thirdPartyUrl">Post-Tutorial Waiting Page</label>
                            <input type="text" class="form-control" id="waitPage" placeholder="Workers will see this page after successful tutorial completion">
                          </div>
                        </div>
                        <!-- <button type="submit" id="updatePrice" class="btn btn-default">Update</button> -->
                      </form></p></div>

                      <!-- Button trigger modal -->
                      <form class="form-horizontal" role="form">
                        <div class="form-group someAirForRecruitingPanel">
                         <div class="col-lg-offset-1 col-lg-10">
                          <div class="cta">
                            <div class="cta-buttons">
                              <button type = "button" id="openInstructionsModal" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Edit waiting page instructions</button> &nbsp;
                            </div>
                          </div>
                        </div>
                      </div>
                    </form>
                    <form id = "autoSendToURLForm" class="form-horizontal forAutoTab" role="form">
                      <div class="form-group">
                        <div class="col-lg-offset-1 col-lg-10">
                          <label for="autoSendToURL" class="col-sm-2 control-label">URL</label>
                          <div class="col-sm-10">
                            <input type="text" class="form-control" id="autoSendToURL" placeholder="USE HTTPS! Enter URL to send workers to">
                          </div>
                        </div>
                      </div>
                    </form>

                    <form class="form-horizontal" role="form">
                      <div id = "startStopButtons">
                        <div class="form-group someAirForRecruitingPanel">
                         <div class="col-lg-offset-1 col-lg-10">
                           <div class="cta">
                            <div class="cta-buttons">
                              <button type="submit" id="startRecruiting" class="btn btn-info btn-lg">Start recruiting</button> &nbsp;
                              <button type="submit" id="stopRecruiting" class="btn btn-danger btn-lg">Stop recruiting</button> 
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>

                <div class="tab-pane" id="directTab">
                  <br/>
                  <form id = "directModeForms" class="form-horizontal" role="form">
                    <div class="form-group someAirForRecruitingPanel">

                     <div class="col-lg-offset-1 col-lg-10">
                      <label for="sendToURL" class="col-sm-2 control-label">URL</label>
                      <div class="col-sm-10">
                        <!--<input type="text" class="form-control" id="directModeInstrPage" placeholder="Instructions Page">-->
                        <input type="text" class="form-control" id="sendToURL" placeholder="USE HTTPS! Enter URL to send workers to">
                      </div>
                    </div>
                  </div>

                  <div class="form-group someAirForRecruitingPanel">

                   <div class="col-lg-offset-1 col-lg-10">
                    <label for="sendToURL" class="col-sm-4 control-label">Price (cents)</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" id="price" placeholder="Price in cents">
                    </div>
                  </div>

                </div>

                <div class="form-group">
                  <div class="col-sm-12">

                    <div class="form-group row someAirForRecruitingPanel">
                      <div class="col-lg-offset-1 col-lg-12">
                        
                        <label for="numHITs" class="col-sm-4 control-label">
                          Num HITs
                        </label>

                        <div class="col-sm-3">
                          <input type="text" class="form-control" id="numHITs" placeholder="">
                        </div>

                        <button class="btn btn-primary btn-circle"
                          onclick="alert('This is the number of individual HITs you would like to post.')">
                        <span class="glyphicon glyphicon-info-sign"></span></button>

                      </div>
                    </div>

                    <div class="form-group row someAirForRecruitingPanel">
                      <div class="col-lg-offset-1 col-lg-12">

                        <label for="numAssignments" class="col-sm-4 control-label">
                          Num Assignments 
                        </label>
                            
                        <div class="col-sm-3">
                          <input type="text" class="form-control" id="numAssignments" placeholder="">
                        </div>

                        <button class="btn btn-primary btn-circle"
                          onclick="alert('This is the number of times you would like each individual HIT to be completed.\n\nFor example, if you set number of HITs to 2 and number of assignments to 3, then each of the 2 HITs will be completed by 3 workers for a total of 6 responses.')">
                        <span class="glyphicon glyphicon-info-sign"></span></button>
                      
                      </div>
                    </div>
                  </div>

                </div>

              </form>
              <form class="form-horizontal" role="form">
                <div id = "postExpireButtons">

                  <div class="form-group someAirForRecruitingPanel">
                    <div class="col-lg-offset-1 col-lg-8">
                      <div class="cta">
                        <div class="cta-buttons">
                          <button type="submit" id="postHITs" class="btn btn-info btn-lg">Post HITs</button>
                          <button type="submit" id="expireHITs" class="btn btn-danger btn-lg">Expire All HITs</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

              </form>
            </div>

          </div>












        </div>
      </div>
      <!-- End Recruiting Panel -->
    </div>
  </div>
</div>


<!-- Debug button -->
<div class="cool-block">
    <!-- <button type="submit" id="debugButton" class="btn btn-info btn-lg pull-right">DEBUG</button> -->
    <div class="cool-block-bor">
        <p id="statusbar"></p>
    </div>
</div>


</div>
<div class="col-md-6">
  <!-- Overview Tab -->
  <div id = "overview">
    <div class="cool-block">
      <div class="cool-block-bor">
        <span class="grandTitre text-primary">Overview</span>
        <br /><br />
        <form role="form" class="form-horizontal">
          <div class="cta">
            <div class="cta-buttons">
              <button type="submit" id="reloadHits" class="btn btn-info btn-sm">Load HITs</button> &nbsp;
              <button type="submit" id="approveAll" class="btn btn-success btn-sm">Approve all loaded HITs</button> &nbsp;
              <button type="submit" id="disposeAll" class="btn btn-warning btn-sm">Dispose all loaded HITs</button>
            </div>
          </div>
        </form>
      </br>
      <ul class="list-group" id="hitsList">
      </ul>
    </div>
    <!-- <div class="col-sm-6"> -->
  </div>
</div>
<!--/ Overview Tab -->


<!-- Workers Tab -->
<div id = "triggerDiv">
  <div class="cool-block">
    <div class="cool-block-bor">
      <span class="grandTitre text-primary">Workers ready</span>
      <p id = "numOnlineText"><span id="numOnline">x</span></p>
      <form class="form-horizontal" role="form">
        <div class="form-group">
          <div class="col-lg-10 col-lg-offset-1">
            <input type="text" class="form-control" id="fireToURL" placeholder="Use HTTPs enter URL to send workers to ...">
          </div>
        </div>

        <div class="form-group">
          <div class="col-lg-10 col-lg-offset-1">
            <input type="text" class="form-control" id="numFire" placeholder="Number of workers to route">
          </div>
        </div>                          

        <div class="form-group" id = "fireButtonsGroup">
          <div class="col-lg-offset-1 col-lg-10">
            <div class="cta">
              <div class="cta-buttons pull-right">
                <button type="submit" id="fireWorkers" class="btn btn-info btn-sm">Route!</button> &nbsp;
                <button type="submit" id="clearQueue" class="btn btn-danger btn-sm">Clear entire queue (pays workers)</button> 
                <!--    <div id="checkBoxes">
                        <form id="checkBoxesCounts">
                            <table>
                                <div id="dynamicCheckBoxes">

                                </div>
                            </table>
                        </form>
                    </div>
                -->
              </div>
            </div>
          </div>
        </div>
      </form>
    

    </div>
  </div>
</div>

<!--/ End Workers Tab -->

</div>
</div>
</body>

</html>
