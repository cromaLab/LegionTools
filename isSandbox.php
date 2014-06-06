<?php

    // I would recommend an API like this.
    //   If &sandbox is in URL then it is sandboxed, else it is not.
    function useSandbox() {
        return (isset($_GET['sandbox']));
    }

?>
