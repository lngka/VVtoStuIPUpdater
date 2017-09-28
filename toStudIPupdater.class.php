<?php
    // requirement
    require_once("RESTTransmission.class.php");
    
    class toStudIPupdater {
        private $studIP_URL;
        private $sqlConnection;
        private $studIP_RESTToken; 
        
        public function __construct($studIP_URL, $sqlConnection, $studIP_RESTToken) {
            $this->stuipURL = $studIP_URL;
            $this->stuipURL = $sqlConnection;
            $this->stuipRESTToken = $studIP_RESTToken;
        }
        /*
         * function to check both student list and execute update if needed
         * currently return student list from stuip
         */
        public function updateStudentList($courseID) {
            
            $VV_studentList    = $this->get_VV_studentList();
            $studIP_studentList = $this->get_studIP_studentList($courseID);
            
            // process both list
            $newStudentList = $this->processStudentLists($VV_studentList, $studIP_studentList);

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
        private function get_VV_studentList() {
            $VV_studentList = array("abdulran", "ankermjo", "nguyenvo", "vollmeca", "wanghsia", "neustudent");
            return $VV_studentList;
        }
        
        
        private function get_studIP_studentList($courseID) {
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
         * function to process the student lists in VV and in StuIP
		 * @param $VV_studentList {array}: an array of RZ-Kennung from VV
		 * @param $studIP_studentList {array}: an array of RZ-Kennung from studIP
		 * @return $merged_list {array}: a combination, add new students from VV, old students in studIP unchanged
		 * @return array() : an emtpy array if both lists are the same
         */
        private function processStudentLists($VV_studentList, $studIP_studentList) {
            print_r("VVStudentList: \n");
            print_r($VV_studentList);
            print_r("stuIPStudentList: \n");
            print_r($studIP_studentList);
            
            $diffArr = array_diff($VV_studentList, $studIP_studentList);
            // return emtpy array if no difference between the two lists
            if (!count($diffArr)) {
                return array();
            } else {
				$merged_list = array_merge($studIP_studentList, $diffArr);
                print_r("Merged list: \n");
				print_r($merged_list);
				return $merged_list;
            }
            
        }
        
        public function __get($property) {
            if (property_exists($this, $property)) {
                return $this->$property;
            }
        }
    }
?>