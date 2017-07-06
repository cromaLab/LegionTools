<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

  <head>
    <title>Submit Only</title>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js" type="text/javascript"></script>
    <!--LegionJS-->
    <script src="scripts/vars.js" type="text/javascript"></script>
    <script src="scripts/legion.js" type="text/javascript"></script>
    <script src="scripts/gup.js" type="text/javascript"></script>
    <link href="style/legion.css" type="text/css" rel="stylesheet" />

    <!-- End LegionJS -->
    <script>
    
    </script>

  </head>
  
  <body>

      <p>Oops, we had more than enough participants for this task. Unfortunately, you will not be able to participate in the task. However, you will still be paid with the base pay. Press the submit button below.</p>

      <div id="instructions"></div>

      <script>
      $(document).ready(function() {
        Control.init();

        $("#legion-submit").removeAttr("DISABLED");
      });
      </script>
     
    </div>

  </body>

</html>
