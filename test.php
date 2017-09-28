<?php
    // requirement
    require_once("toStudIPupdater.class.php");
    
    // RESTTransmission global settings
    $echoForDebug = null;
    $OptionStudIPRESTUseTokenAuth           = true;
    $OptionStudIPRESTUrl                    = "https://studip.rz.uni-augsburg.de/api.php";
    
    // init updater
    $stuipURL = "stuipURL";
    $sqlConnection = "sqlConnection";
    $token = "vfW7ODHocFZzknCbOi5z7km";
    $toStudIPupdater = new toStudIPupdater($stuipURL, $sqlConnection, $token);
    
    // execute
    $courseID = "0ad9d3146731a933022f72f0c60bfc32";
    $val = $toStudIPupdater->updateStudentList($courseID);
    echo "\nStuip says: \n";
    print_r($val . "\n");
?>
   