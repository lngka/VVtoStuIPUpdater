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
        /*
         * function to check both student list and execute update if needed
         * currently return student list from stuip
         */
        public function updateStudentList($courseID) {
            
            $VVStudentList    = $this->getVVStudentList();
            $stuIPStudentList = $this->getStuIPStudentList($courseID);
            
            // process both list
            $newStudentList = $this->processStudentLists($VVStudentList, $stuIPStudentList);

            // exit when update unnecessary
            if (!count($newStudentList)) {
                return "No changes, no update needed";
            } else {
                // execute changes
                $route = array("PUT", "/uaux/course/" . $courseID);
                $params = array();
                $values = array(
                    "autoren " => $newStudentList
                );
                $token = $this->__get("stuipRESTToken");
                $transmission = new RESTTransmission($route, $params, $values, $token);
                $response = $transmission->execute();
                
                print_r("Trying to update student list...");
                return $response;
            }
            
            

        }
        
        // TODO: get student list from VV's SQL server
        // currently return a pre-defined array
        private function getVVStudentList() {
            $VVStudentList = array("abdulran", "ankermjo", "nguyenvo", "vollmeca", "wanghsia", "neustudent");
            return $VVStudentList;
        }
        
        
        private function getStuIPStudentList($courseID) {
            // StuIP store the student list under the JSON-Key "autoren"
            $STUDENTLIST_KEY_NAME = "autoren";
            
            // RESTTransmission global settings
            $echoForDebug = null;
            $OptionStudIPRESTUseTokenAuth           = true;
            $OptionStudIPRESTUrl                    = "https://studip.rz.uni-augsburg.de/api.php";
            
            // RESTTransmission settings & execution
            $route = array("GET", "/uaux/course/" . $courseID);
            $params = array();
            $values = array();
            $token = $this->__get("stuipRESTToken");
            $transmission = new RESTTransmission($route, $params, $values, $token); 
            $response = $transmission->execute();
            
            // RESTTransmission return stdClass if success, string if fail
            // stdClass needs to be converted to array to access its attribute
            if(gettype($response) == "object") {
                $response = json_decode(json_encode($response), true);
                $studentListe = $response[$STUDENTLIST_KEY_NAME];
                return $studentListe;
            } else {
                return $response;
            }
            
        }
        
        /*
         * function to combine the student lists in VV and in StuIP
         */
        private function processStudentLists($VVStudentList, $stuIPStudentList) {
            print_r("VVStudentList: \n");
            print_r($VVStudentList);
            print_r("stuIPStudentList: \n");
            print_r($stuIPStudentList);
            
            $diffArr = array_diff($VVStudentList, $stuIPStudentList);
            // return emtpy array if no difference between the two lists
            if (!count($diffArr)) {
                return array();
            } else {
                print_r("Merged list: \n");
                print_r(array_merge($stuIPStudentList, $diffArr));
                return array_merge($stuIPStudentList, $diffArr);
            }
            
        }
        
        public function __get($property) {
            if (property_exists($this, $property)) {
                return $this->$property;
            }
        }
    }
?>