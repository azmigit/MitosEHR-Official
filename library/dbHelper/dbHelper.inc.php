<?php

/* db Helper v0.0.1
 * Description: A PDO helper for MitosEHR, containts custom function to manage the database
 * in MitosEHR. PDO is new in PHP v5 
 * 
 * The PHP Data Objects (PDO) extension defines a lightweight, 
 * consistent interface for accessing databases in PHP. 
 * Each database driver that implements the PDO interface can expose database-specific 
 * features as regular extension functions. Note that you cannot perform any database 
 * functions using the PDO extension by itself; 
 * you must use a database-specific PDO driver to access a database server.
 * 
 * PDO provides a data-access abstraction layer, which means that, 
 * regardless of which database you're using, you use the same functions to issue queries 
 * and fetch data. PDO does not provide a database abstraction; it doesn't rewrite 
 * SQL or emulate missing features. 
 * You should use a full-blown abstraction layer if you need that facility.
 * 
 * PDO ships with PHP 5.1, and is available as a PECL extension for PHP 5.0; 
 * PDO requires the new OO features in the core of PHP 5, and so will not 
 * run with earlier versions of PHP.
 * 
 * Author: Gino Rivera Falu
 * Ver: 0.0.1
 */

//**********************************************************************
// Connect to the database
//**********************************************************************
$conn = new PDO( "mysql:host=" . $_SESSION['site']['db']['host'] . ";port=" . $_SESSION['site']['db']['port'] . ";dbname=" . $_SESSION['site']['db']['database'], $_SESSION['site']['db']['username'], $_SESSION['site']['db']['password'] );

//**********************************************************************
// Simple SQL Stament, with no Event LOG injection
// return: Array of records
//**********************************************************************
function sqlStatement($sql){
	// Get the global variable
	global $conn;
	
	// Get all the records
	$recordset = $conn->query($sql);
	$result = $recordset->fetch(PDO::FETCH_ASSOC);
	
	// return the recordset 
	return $result;
}

//**********************************************************************
// Simple SQL Stament, with Event LOG injection
// return: Array of records + Inject the action on the event log
// The Log Injection is automatic 
// It tries to detect an insert, delete, alter and log the event
//**********************************************************************
function sqlStatementLog($sql){
	// Get the global connection variable
	global $conn;
	
	// Execute the SQL stament
	$recordset = $conn->query($sql);
	
	// If the QUERY has INSERT, DELETE, ALTER then has to 
	// insert the event to the database.
	if (strpos($sql, "INSERT") && strpos($sql, "DELETE") && strpos($sql, "ALTER")){
		if (strpos($sql, "INSERT")) $eventLog = "Record insertion";
		if (strpos($sql, "DELETE")) $eventLog = "Record deletion";
		if (strpos($sql, "ALTER")) $eventLog = "Table alteration"; 
		$eventSQL = "INSERT INTO log 
				(date, event, comments, user, patient_id) 
				VALUES (NOW(), '" . $eventLog . "', '" . $sql . "', '" . $_SESSION['user']['name'] . "', '" . $_SESSION['patient']['id'] . "')";
		$conn->query($eventSQL);
	}
	$result = $recordset->fetch(PDO::FETCH_ASSOC);
	// return the recordset 
	return $result;
}

//**********************************************************************
// Simple SQL Stament, with Event LOG injection
// return: Array of records + Manually inject the action on the event log
//**********************************************************************
function sqlStatementEvent($eventLog, $sql){
	// Get the global connection variable
	global $conn;
	
	// Execute the SQL stament
	$conn->query($sql);
		
	// If the QUERY has INSERT, DELETE, ALTER then has to 
	// insert the event to the database.
	$eventSQL = "INSERT INTO log 
			(date, event, comments, user, patient_id) 
			VALUES (NOW(), '" . $eventLog . "', '" . $sql . "', '" . $_SESSION['user']['name'] . "', '" . $_SESSION['patient']['id'] . "')";
	$conn->query($eventSQL);
	
	// return the recordset 
	return $recordset;
}

//**********************************************************************
// Manually insert a event log to the database
//**********************************************************************
function sqlEventLog($eventLog, $comments, $userNotes=NULL){
	// Get the global connection variable
	global $conn;
	
	// Generate the SQL stament for Event Log injection
	$eventSQL = "INSERT INTO log 
					(date, event, comments, user, patient_id, user_notes) 
				VALUES (NOW(), '" . $eventLog . "', '" . $comments . "', '" . $_SESSION['user']['name'] . "', '" . $_SESSION['patient']['id'] . "', '" . $userNotes . "')";
	
	// Execute the stament
	$conn->query($eventSQL);
}

//**********************************************************************
// Return the number of records
//**********************************************************************
function sqlTotalCount($resource){
	if (!$resource) { return; }
	return $resource->RecordCount();
}

?>



