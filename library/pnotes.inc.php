<?php
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

require_once("{$GLOBALS['srcdir']}/sql.inc.php");

// 06-2009, BM migrated the patient_note_types array to the list_options table

function getPnoteById($id, $cols = "*")
{
  return sqlQuery("SELECT $cols FROM pnotes WHERE id=? " .
    " AND deleted != 1 ". // exclude ALL deleted notes
    "order by date DESC limit 0,1", array($id) );
}

function getPnotesByDate($date, $activity = "1", $cols = "*", $pid = "%",
  $limit = "all", $start = 0, $username = '', $docid = 0, $status = "")
{
$sqlParameterArray = array();
  if ($docid) {
    $sql = "SELECT $cols FROM pnotes AS p, gprelations AS r " .
    "WHERE p.date LIKE ? AND r.type1 = 1 AND " .
    "r.id1 = ? AND r.type2 = 6 AND p.id = r.id2";
    array_push($sqlParameterArray, '%'.$date.'%', $docid);
  }
  else {
    $sql = "SELECT $cols FROM pnotes AS p " .
      "WHERE date LIKE ? AND pid LIKE ?";
    array_push($sqlParameterArray, '%'.$date.'%', $pid);
  }
  $sql .= " AND deleted != 1"; // exclude ALL deleted notes
  if ($activity != "all") {
    $sql .= " AND activity = ?";
    array_push($sqlParameterArray, $activity);
  }
  if ($username) {
    $sql .= " AND assigned_to LIKE ?";
    array_push($sqlParameterArray, $username);
  }
  if ($status)
    $sql .= " AND message_status IN ('".str_replace(",", "','", $status)."')";
  $sql .= " ORDER BY date DESC";
  if($limit != "all")
    $sql .= " LIMIT $start, $limit";

  $res = sqlStatement($sql, $sqlParameterArray);

  for ($iter = 0;$row = sqlFetchArray($res);$iter++)
    $all[$iter] = $row;
  return $all;
}

function getPnotesByPid ($pid, $activity = "1", $cols = "*", $limit=10, $start=0)
{
  $res = sqlStatement("SELECT $cols FROM pnotes WHERE pid LIKE '$pid' " .
    "AND activity = '$activity' ".
    " AND deleted != 1 ".
    " ORDER BY date DESC LIMIT $start,$limit");
  for ($iter = 0; $row = sqlFetchArray($res); $iter++)
    $all[$iter] = $row;
  return $all;
}

function addPnote($pid, $subject, $newtext, $authorized = '0', $activity = '1',
  $title='Unassigned', $assigned_to = '', $datetime = '', $message_status = "New")
{
  if (empty($datetime)) $datetime = date('Y-m-d H:i:s');

  $body = date('Y-m-d H:i') . ' (' . $_SESSION['authUser'];
  if ($assigned_to) $body .= " to $assigned_to";
  $body = $body . ') ' . $newtext;

  return sqlInsert("INSERT INTO pnotes (date, subject, body, pid, user, groupname, " .
    "authorized, activity, title, assigned_to, message_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
    array($datetime, $subject, $body, $pid, $_SESSION['authUser'], $_SESSION['authProvider'], $authorized, $activity, $title, $assigned_to, $message_status) );
}

function updatePnote($id, $newtext, $title, $assigned_to, $message_status = "")
{
  $row = getPnoteById($id);
  if (! $row) die("updatePnote() did not find id '$id'");
  $activity = $assigned_to ? '1' : '0';

  $body = $row['body'] . "\n" . date('Y-m-d H:i') .
    ' (' . $_SESSION['authUser'];
  if ($assigned_to) $body .= " to $assigned_to";
  $body = $body . ') ' . $newtext;

  if ($message_status) {
    sqlStatement("UPDATE pnotes SET " .
      "body = ?, activity = ?, title= ?, " .
      "assigned_to = ?, message_status = ? WHERE id = ?",
      array($body, $activity, $title, $assigned_to, $message_status, $id) );
  }
  else {
    sqlStatement("UPDATE pnotes SET " .
      "body = ?, activity = ?, title= ?, " .
      "assigned_to = ? WHERE id = ?",
      array($body, $activity, $title, $assigned_to, $id) );
  }
}

function updatePnoteMessageStatus($id, $message_status)
{
  sqlStatement("update pnotes set message_status = ? where id = ?", array($message_status, $id) );
}

function authorizePnote($id, $authorized = "1")
{
  sqlQuery("UPDATE pnotes SET authorized = '$authorized' WHERE id = '$id'");
}

function disappearPnote($id)
{
  sqlStatement("UPDATE pnotes SET activity = '0' WHERE id=?", array($id) );
  return true;
}

function reappearPnote ($id)
{
  sqlStatement("UPDATE pnotes SET activity = '1' WHERE id=?", array($id) );
  return true;
}

function deletePnote($id)
{
  sqlStatement("UPDATE pnotes SET deleted = '1' WHERE id=?", array($id) );
  return true;
}
?>
