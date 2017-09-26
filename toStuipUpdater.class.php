<?php
    // requirement
    require_once("RESTTransmission.class.php");
    
    class toStuipUpdater {
        private $stuipURL;
        private $sqlConnection;
        private $stuipRESTToken; 
        
        public function __construct($stuipURL, $sqlConnection, $stuipRESTToken) {
            $this->stuipURL = $stuipURL;
            $this->stuipURL = $sqlConnection;
            $this->stuipRESTToken = $stuipRESTToken;
        }
        
        public function updateStudentList($courseID) {
            
            $VVStudentList    = $this->getVVStudentList();
            $stuIPStudentList = $this->getStuIPStudentList($courseID);
            
            // compare
            $newStudentList = $this->compareStudentLists($VVStudentList, $stuIPStudentList);

            // exit when update unnecessary
            if (!$newStudentList) {
                return "Update unnecessary";
            }
            
            return $newStudentList;
            
//             // excute changes
//             $route = array("PUT", "/uaux/course/" . $courseID);
//             $params = array();
//             $values = array(
//                     "autoren " => $newStudentList
//             );
//             $transmission = new RESTTransmission($route, $params, $values, $token);
//             $response = $transmission->execute();
//             
//             // report
//             $response = json_encode($response, 128);
//             return $response;
        }
        
        // TODO: get student list from VV's SQL server
        // currently return empty array
        private function getVVStudentList() {
            $studentListe = array();
            return $studentListe;
        }
        
        
        private function getStuIPStudentList($courseID) {
            // RESTTransmission global settings
            $echoForDebug = null;
            $OptionStudIPRESTUseTokenAuth           = true;
            $OptionStudIPRESTUrl                    = "https://studip.rz.uni-augsburg.de/api.php";
            
            // RESTTransmission local settings
            $route = array("GET", "/uaux/course/" . $courseID);
            $params = array();
            $values = array();
            $token = $this->__get("stuipRESTToken");
            
            // RESTTransmission build & execute
            $transmission = new RESTTransmission($route, $params, $values, $token); 
            $response = $transmission->execute();
            print_r($route);
            print_r($params);
            print_r($values);
            print_r($token . "\n");
            print_r($response . "\n");
            
            // RESTTransmission return stdClass if success, string if fail
            if(gettype($response) == "object") {
                // stdClass needs to be converted to array to access its attribute
                $response = json_decode(json_encode($response), true);
                $studentListe = $response["autoren"];
                return $studentListe;
            } else {
                return $response;
            }
            
        }
        
        // TODO: compare both student lists
        // currently returns $stuIPStudentList
        private function compareStudentLists($VVStudentList, $stuIPStudentList) {
            return $stuIPStudentList;
        }
        
        public function __get($property) {
            if (property_exists($this, $property)) {
                return $this->$property;
            }
        }
    }
?>