<?php
//--------------------------------------------------------------------------------------------------------------------------
// data_create.ejs.php
// v0.0.2
// Under GPLv3 License
//
// Integration Sencha ExtJS Framework
//
// Integrated by: GI Technologies Inc. in 2011
//
// OpenEMR is a free medical practice management, electronic medical records, prescription writing,
// and medical billing application. These programs are also referred to as electronic health records.
// OpenEMR is licensed under the General Gnu Public License (General GPL). It is a free open source replacement
// for medical applications such as Medical Manager, Health Pro, and Misys. It features support for EDI billing
// to clearing houses such as Availity, MD-Online, MedAvant and ZirMED using ANSI X12.
//
// Sencha ExtJS
// Ext JS is a cross-browser JavaScript library for building rich internet applications. Build rich,
// sustainable web applications faster than ever. It includes:
// * High performance, customizable UI widgets
// * Well designed and extensible Component model
// * An intuitive, easy to use API
// * Commercial and Open Source licenses available
//
// Remember, this file is called via the Framework Store, this is the AJAX thing.
//--------------------------------------------------------------------------------------------------------------------------

session_name ( "MitosEHR" );
session_start();
session_cache_limiter('private');

//******************************************************************************
// Reset session count 10 secs = 1 Flop
//******************************************************************************
$_SESSION['site']['flops'] = 0;

include_once("../../library/dbHelper/dbHelper.inc.php");
include_once("../../library/I18n/I18n.inc.php");
require_once("../../repository/dataExchange/dataExchange.inc.php");

//------------------------------------------
// Database class instance
//------------------------------------------
$mitos_db = new dbHelper();

// Parce the data generated by EXTJS witch is JSON
$data = json_decode ( $_POST['row'] );
$sql = "INSERT INTO 
			pnotes
		SET
			body = '" 			. dataEncode( $data[0]->body ) . "', " . "
			pid = '"			. dataEncode( $data[0]->pid ) . "', " . "
			user_id = '" 		. dataEncode( $data[0]->user_id ) . "', " . "
			facility_id = '" 	. $_SESSION['site']['facility'] . "', " . "
			activity = '" 		. dataEncode( $data[0]->activity) . "', " . "
			authorized = '" 	. dataEncode( $data[0]->authorized) . "', " . "
			assigned_to = '" 	. dataEncode( $data[0]->assigned_to) . "', " . "
			message_status = '" . dataEncode( $data[0]->message_status) . "', " . "
			subject = '" 		. dataEncode( $data[0]->subject) . "', " . "
			reply_id = '" 		. dataEncode( $data[0]->reply_id) . "', " . "
			note_type = '" 		. dataEncode( $data[0]->note_type) . "'";

$mitos_db->setSQL($sql);
$mitos_db->execLog();
echo "{ success: true }";

?>