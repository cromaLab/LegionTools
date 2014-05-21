<!DOCTYPE html>
<html>
<head>
    <title>Retainer trigger</title>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script type="text/javascript" src="scripts/gup.js"></script>
    <script type="text/javascript" src="scripts/trigger.js"></script>
    <script type="text/javascript" src="scripts/writeNumOnline.js"></script>
    <script type="text/javascript" src="scripts/bootstrap.touchspin.js"></script>
    <script type="text/javascript" src="scripts/hitsOverview.js"></script>

    <script> var baseURL = "<?php include('../baseURL.php'); echo $baseURL; ?>"; </script>

    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="style/trigger.css">

</head>

<body>

    <div class="row">
      <div class="col-md-5">
        <h3>Manage task (HIT)</h3>

        <form role="form">
          <div class="form-group">
            <label for="hitTitle">Task Title</label>
            <input type="text" class="form-control" id="hitTitle" placeholder="Enter HIT title">
          </div>
          <div class="form-group">
            <label for="hitDescription">Task Description</label>
            <input type="text" class="form-control" id="hitDescription" placeholder="Enter HIT description">
          </div>
          <div class="form-group">
            <label for="hitKeywords">Task Keywords</label>
            <input type="text" class="form-control" id="hitKeywords" placeholder="Enter HIT keywords (separated by a single space)">
          </div>
          <div class="form-group">
            <label for="taskSession">Task session name</label>
            <input type="text" class="form-control" id="taskSession" placeholder="Enter a task session name">
          </div>

          <button type="submit" id="addNewTask" class="btn btn-primary">Add new task</button>
          <button type="submit" id="loadTask" class="btn btn-default">Load task via task session</button>
          <button type="submit" id="updateTask" class="btn btn-default">Update task via task session</button>
        </form>
      </div>

      <div class="col-md-5">
          <h3>Target number of workers</h3>
                <input id="currentTarget" type="text" value="0" name="currentTarget">
                <script>
                    $("input[name='currentTarget']").TouchSpin();
                </script> </br>

                <form class="form-inline" role="form">
                  <div class="form-group">
                    <label class="sr-only" for="minPrice">Min task price</label>
                    <input type="text" class="form-control" id="minPrice" placeholder="Min task price in cents"> -
                  </div>
                  <div class="form-group">
                    <label class="sr-only" for="maxPrice">Max task price</label>
                    <input type="text" class="form-control" id="maxPrice" placeholder="Max task price in cents">
                  </div>
                  <button type="submit" id="updatePrice" class="btn btn-default">Update price</button>
                </form>
                </br>
                <button type="submit" id="startRecruiting" class="btn btn-primary">Start recruiting</button>
                <button type="submit" id="stopRecruiting" class="btn btn-danger">Stop recruiting</button>
      </div>
    </div>
    <div class="row">
      <div class="col-md-5">
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
      <div class="col-md-5">
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
