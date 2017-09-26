<?php
	# requirement
	require_once("RESTTransmission.class.php");

	# RESTTransmission global settings
	$echoForDebug = null;
	$OptionStudIPRESTUseTokenAuth           = true;
	$OptionStudIPRESTUrl                    = "https://studip.rz.uni-augsburg.de/api.php";
	
	# API call settings
	$route = array("<a HTTP verb>", "<the API endpoint>");
	$token = "vfW7ODHocFZzknCbOi5z7km";
	$params = array(); # optional request params for GET
	$values = array(); # optional request body for POST
	
###############################################################################################################################################################

	# ROUTE: alle Institute/Einrichtungen auflisten:
// 	$route = array("GET", "/v1/uaux/institutes");
	
	# ROUTE: alle Untereinrichtungen zur Einrichtung mit ID  auflisten
// 	$route = array("GET", "/v1/uaux/institutes?faculty=3ea606531e8c2cff18db75e05e688113");
	
// 	# ROUTE: eine Liste aller Veranstaltungstypen und ihrer IDs auflisten
// 	$route = array("GET", "/v1/uaux/seminartypes");
	
	# ROUTE: Abfrage für Benutzer-info
// 	$rzbk = "nguyenvo"; // die RZ-Kennung
// 	$role = "autor";
// 	$route = array("GET", "/multiaccount/$rzbk/role/$role)");
	
// 	# ROUTE: Anlegen einer Lehrveranstaltung
// 	$route = array("POST", "/uaux/course/");
// 	$values = array(
// 		"name" => "lookMomImakeThis5",
// 		"typ" => "2",
// 		"semester" => "SS 2017",
// 		"heimateinrichtung" => "19859e53b8fc9e55a3e36aef851ab3f5",
// 		"dozenten" => array("nguyenvo_dozent"),
// 		"studienbereiche" => array()
// 	);
	
	# Abfragen von LV-Informationen
	$cid = "0ad9d3146731a933022f72f0c60bfc32"; 
	$route = array("GET", "/uaux/course/" . $cid);

// 	# Modifizieren einer LV
// 	$cid = "0ad9d3146731a933022f72f0c60bfc32";
// 	$route = array("PUT", "/uaux/course/" . $cid);
// 	$values = array(
// 		"lvgruppen " => "test"
// 	);

	# Abfragen von LV-Gruppe mit <sig>
// 	$sig = "f5220cf4e8979eb3df8d414b978c3554";
// 	$route = array("GET", "/uaux/modul/sig/" . $sig);
###############################################################################################################################################################

	# Abfrage durchführen
	$transmission = new RESTTransmission($route, $params, $values, $token);
	$response = $transmission->execute();

	# Inhalt der Variablen $response ausgeben
	$json_string = json_encode($response, 128);
	print_r(prettyPrint($json_string));
	
	# make json pretty for viewing in terminal
	function prettyPrint($json) {
		$result = '';
		$level = 0;
		$in_quotes = false;
		$in_escape = false;
		$ends_line_level = NULL;
		$json_length = strlen( $json );

		for( $i = 0; $i < $json_length; $i++ ) {
			$char = $json[$i];
			$new_line_level = NULL;
			$post = "";
			if( $ends_line_level !== NULL ) {
				$new_line_level = $ends_line_level;
				$ends_line_level = NULL;
			}
			if ( $in_escape ) {
				$in_escape = false;
			} else if( $char === '"' ) {
				$in_quotes = !$in_quotes;
			} else if( ! $in_quotes ) {
				switch( $char ) {
					case '}': case ']':
						$level--;
						$ends_line_level = NULL;
						$new_line_level = $level;
						break;

					case '{': case '[':
						$level++;
					case ',':
						$ends_line_level = $level;
						break;

					case ':':
						$post = " ";
						break;

					case " ": case "\t": case "\n": case "\r":
						$char = "";
						$ends_line_level = $new_line_level;
						$new_line_level = NULL;
						break;
				}
			} else if ( $char === '\\' ) {
				$in_escape = true;
			}
			if( $new_line_level !== NULL ) {
				$result .= "\n".str_repeat( "\t", $new_line_level );
			}
			$result .= $char.$post;
		}

		return $result . "\n";
}

?>
