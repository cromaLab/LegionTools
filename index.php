<!DOCTYPE html>
<html>
<head>
    <title>Retainer trigger</title>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script type="text/javascript" src="Retainer/scripts/gup.js"></script>
    <script type="text/javascript" src="Retainer/scripts/trigger.js"></script>
    <script type="text/javascript" src="Retainer/scripts/writeNumOnline.js"></script>
    <script type="text/javascript" src="Retainer/scripts/bootstrap.touchspin.js"></script>
    <script type="text/javascript" src="Retainer/scripts/hitsOverview.js"></script>
    <script type="text/javascript" src="Retainer/scripts/jquery.blockUI.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>

    <script> var baseURL = "<?php include('config.php'); echo $baseURL; ?>"; </script>

    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="Retainer/style/trigger.css">

</head>

<body>
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

    <div class="row">
      <div class="col-md-6" style = "border-right: 1px #ccc solid;">
        <h3>Load an old experiment</h3>
        <form class="form-inline" role="form" style = "text-align: right;">
          <div class="form-group">
            <label class="sr-only" for="taskSessionLoad">Load an old experiment</label>
            <select id = "taskSessionLoad" class="form-control"></select>
          </div>
          <!-- <button type="submit" id="loadTask" class="btn btn-default">Load</button> -->
        </form>

        <h3>OR create a new experiment</h3>
        <form class="form-horizontal" role="form">
          <div class="form-group">
            <label for="taskSession" class="col-sm-5 control-label">Experiment name (remember this):</label>
            <div class="col-sm-7">
              <input type="text" class="form-control" id="taskSession" placeholder="Enter a task session name">
            </div>
          </div>
          <div class="form-group">
            <label for="hitTitle" class="col-sm-3 control-label">HIT Title</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="hitTitle" placeholder="Enter HIT title">
            </div>
          </div>
          <div class="form-group">
            <label for="hitDescription" class="col-sm-3 control-label">HIT Description</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="hitDescription" placeholder="Enter HIT description">
            </div>
          </div>
          <div class="form-group">
            <label for="hitKeywords" class="col-sm-3 control-label">HIT Keywords</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="hitKeywords" placeholder="Enter HIT keywords (separated by a single space)">
            </div>
          </div>
          <div class="form-group row">
              <label for="country" class="col-sm-3 control-label">Worker country</label>
              <div class="col-sm-3">
                <select id = "country" class="form-control">
                  <option>All</option>
                  <option>US</option>
                </select>
              </div>
              <label for="percentApproved" class="col-sm-3 control-label">Min % approved</label>
              <div class="col-sm-3">
                  <input type="number" min = "0" max = "100" class="form-control" id="percentApproved" value = "0">
              </div>
          </div>
          <button type="submit" id="addNewTask" class="btn btn-primary">Add new task</button>
          <button disabled = "disabled" type="submit" id="updateTask" class="btn btn-default">Update</button>
        </form>
        </br>
      </div>

      <div class="col-md-4 recruitingDiv">
          <h3>Recruiting</h3>
          <div class="btn-group btn-group-lg">
            <button id="useRetainerMode" type="button" class="btn btn-default active">Retainer</button>
            <button id="useDirectMode" type="button" class="btn btn-default">Classic (direct)</button>
          </div>
          
          <!-- <div class="col-sm-6"> -->
          <div id="touchSpinDiv">
          Target number of workers:
          <input id="currentTarget" type="text" value="0" name="currentTarget">
          <script>
              $("input[name='currentTarget']").TouchSpin();
          </script>
          </div>

          <div id="priceRangeDiv"><p><form class="form-inline" role="form">
            <div class="form-group">
              <label class="sr-only" for="minPrice">Min task price</label>
              <input type="text" class="form-control" id="minPrice" placeholder="Min price in cents"> -
            </div>
            <div class="form-group">
              <label class="sr-only" for="maxPrice">Max task price</label>
              <input type="text" class="form-control" id="maxPrice" placeholder="Max price in cents">
            </div>
            <button type="submit" id="updatePrice" class="btn btn-default">Update</button>
          </form></p></div>

          <!-- Button trigger modal -->
          <p><button id="openInstructionsModal" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Edit waiting page instructions</button></p>

          <p><div class="input-group">
            <span class="input-group-addon">Access key</span>
            <input id="accessKey" type="text" class="form-control" placeholder="OPTIONAL. Can also edit amtKeys.php">
          </div>
          <div class="input-group">
            <span class="input-group-addon">Secret key</span>
            <input id="secretKey" type="text" class="form-control" placeholder="OPTIONAL. Can also edit amtKeys.php">
          </div>For account balance, visit <a href="https://requester.mturk.com/mturk/youraccount">here</a>.</p>

          <form id = "directModeForms" class="form-horizontal" role="form">
            <div class="form-group">
              <label for="sendToURL" class="col-sm-2 control-label">URL</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" id="sendToURL" placeholder="USE HTTPS! Enter URL to send workers to">
              </div>
            </div>

            <div class="form-group">
              <label for="sendToURL" class="col-sm-2 control-label">Price</label>
              <div class="col-sm-4">
                <input type="text" class="form-control" id="price" placeholder="Price in cents">
              </div>
            </div>

            <div class="form-group">
              <div class="col-sm-12">
                <div class="form-group row">
                  <label for="numHITs" class="col-sm-3 control-label">Num HITs</label>
                  <div class="col-sm-2">
                    <input type="text" class="form-control" id="numHITs" placeholder="">
                  </div>
                  <label for="numAssignments" class="col-sm-4 control-label">Num Assignments</label>
                  <div class="col-sm-2">
                    <input type="text" class="form-control" id="numAssignments" placeholder="">
                  </div>
                </div>
              </div>
            </div>

          </form>

          <p><div class="btn-group btn-group-lg">
            <button id="yesSandbox" type="button" class="btn btn-default active">Sandbox</button>
            <button id="noSandbox" type="button" class="btn btn-default">Live</button>
          </div>
          </p>

          <div id = "startStopButtons">
            <button type="submit" id="startRecruiting" class="btn btn-primary btn-lg">Start recruiting</button>
            <button type="submit" id="stopRecruiting" class="btn btn-danger btn-lg">Stop recruiting</button>
          </div>

          <div id = "postExpireButtons">
            <button type="submit" id="postHITs" class="btn btn-primary btn-lg">Post HITs</button>
            <button type="submit" id="expireHITs" class="btn btn-danger btn-lg">Expire All HITs</button>
          </div>
      </div>
    </div>
    <div class="row">
      <div id = "overview" class="col-md-6" style = "border-right: 1px #ccc solid; border-top: 1px #ccc solid;">
        <h3>Overview</h3>
        <form role="form">
          <button type="submit" id="approveAll" class="btn btn-success">Approve all</button>
          <button type="submit" id="disposeAll" class="btn btn-warning">Dispose all</button>
          <button type="submit" id="reloadHits" class="btn btn-default">Reload</button>
        </form>
        </br>
        <ul class="list-group" id="hitsList">

        </ul>

      </div>
      <div id = "triggerDiv" class="col-md-4" style = "border-top: 1px #ccc solid;">
          <h3>Workers ready</h3>
          <p id = "numOnlineText"><span id="numOnline">x</span></p>

          <form class="form" role="form">
            <div class="form-group">
              <input type="text" class="form-control" id="fireToURL" placeholder="USE HTTPS Enter URL to send workers to">
              <input type="text" class="form-control" id="numFire" placeholder="Number of workers to fire">
            </div>
            <div id = "fireButtonsGroup">
                <button type="submit" id="fireWorkers" class="btn btn-primary">Fire!</button>
                <button type="submit" id="clearQueue" class="btn btn-danger">Clear entire queue (pays workers)</button>
            </div>
          </form>
      </div>
    </div>

</body>

</html>
