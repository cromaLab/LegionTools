<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

  <head>
    <title>Submit Only</title>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js" type="text/javascript"></script>
    <!--LegionJS-->
    <script src="scripts/vars.js" type="text/javascript"></script>
    <script src="../LegionJS_Libraries/vars.js" type="text/javascript"></script>
    <script src="../LegionJS_Libraries/legion.js" type="text/javascript"></script>
    <script src="../LegionJS_Libraries/gup.js" type="text/javascript"></script>
    <link href="../LegionJS_Libraries/legion.css" type="text/css" rel="stylesheet" />

    <!-- End LegionJS -->
    <script>
    
    </script>

  </head>
  
  <body>

      <p>You are not needed for this task. However, you will still be paid and bonused according to the amount of time you waited. Please press the submit button below.</p>

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
