<?php
    // requirement
    require_once("toStuipUpdater.class.php");
    
    //init SQL connection
//     $sqlConnection = mysqli_connect($servername, $username, $password);
//     
//     // Check connection
//     if (!$conn) {
//         die("Connection failed: " . mysqli_connect_error());
//     } else {
//         echo "Connected successfully";
//     }
    
    
    // init updater
    $stuipURL = "stuipURL";
    $sqlConnection = "sqlConnection";
    $token = "vfW7ODHocFZzknCbOi5z7km";
    $toStuipUpdater = new toStuipUpdater($stuipURL, $sqlConnection, $token);
    
    // execute
    $courseID = "0ad9d3146731a933022f72f0c60bfc32";
    $val = $toStuipUpdater->updateStudentList($courseID);
    echo "\n";
    echo "toStuipUpdater says: $val\n";
?>
   