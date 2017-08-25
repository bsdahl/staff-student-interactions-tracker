<?php
/**
 * Database interface for student interactions
 *
 * This file connects to the interactions database and will respond to queries in JSON
 * format. Methods provided below for POST and GET.
 *
 * PHP version 5
 *
 * Copyright (C) Association Free Lutheran Bible School - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Benjamin Dahl <bdahl@aflc.org>, 2016
 *
 * @package    Student Interactions App
 * @author     Benjamin Dahl <ben.dahl@mail.com>
 * @copyright  2016 Association Free Lutheran Bible School
 * @version    SVN: $Id$
 */

require "login/loginheader.php";
require('config.php');

// Create connection
$conn = new mysqli($host, $username, $password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

// Handle POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	// Prepare the INSERT
	$stmt = $conn->prepare("INSERT INTO Interaction (staff_id, date, type, location, fine, fine_due_date, notes) 
							VALUES (?,?,?,?,?,?,?)");
	
	$stmt->bind_param("issssss", $staff_id, $date, $type, $location, $fine, $duedate, $notes);

	// Set parameters
	$staff_id = $_POST['staff-id'];
	$date = date('Y-m-d',strtotime($_POST['date']));
	$students = array_unique( explode(', ', $_POST['students']));
	$type = $_POST['type'];
	$fine = (empty($_POST['fine']) ? NULL : $_POST['fine']);
	$duedate = (empty($_POST['duedate']) ? NULL : $_POST['duedate']);
	$location = $_POST['location'];
	$notes = $_POST['notes'];
	//$date = '0000-00-00';
	// Validation
	if ($date == '0000-00-00') {
		http_response_code(400);
		echo "Invalid Date.";
		die();
	}

	// Insert Interaction Record
	$stmt->execute();
	echo $conn->error;

	// Get the new record id
	$interaction_id = $conn->insert_id;
	$stmt->reset();

	// Prepare student INSERT
	$stmt = $conn->prepare("INSERT INTO Students_Interactions (student_id, interaction_id) 
							VALUES ( (SELECT id FROM Student WHERE UPPER(CONCAT_WS(' ', first_name, last_name)) LIKE UPPER(?) ) ,?)");
	
	$stmt->bind_param("si", $student, $interaction_id);

	// Insert each student
	foreach ($students as $student) {
		$stmt->execute();
	}

	$stmt->reset();

	if ($_POST['type'] == 'Discipline') {
		$sql = "SELECT gender
				FROM Interaction
					INNER JOIN Students_Interactions
						ON Interaction.id = Students_Interactions.interaction_id
					INNER JOIN Student
						ON Students_Interactions.student_id = Student.id
				WHERE Interaction.id = {$interaction_id}
				GROUP BY gender";
		$result = $conn->query($sql);

		while($r = mysqli_fetch_assoc($result)) {
			if($r['gender'] == 'Female') {
				$to = 'lmccarlson@aflc.org';
			} else {
				$to = 'mattq@aflc.org';
			}
		}

		$subject = "Student Discipline Report";
		$msg = '<html><head><title>Student Discipline Report</title></head><body>';
		// Always set content-type when sending HTML email
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$headers .= "From: no-reply@aflbs.org";

		foreach ($_POST as $key => $value) {
			$key = strtoupper($key);
			$msg .= "<p>{$key}: {$value}";
		}
		$msg .= '</body></html>';
		mail($to,$subject,$msg,$headers);
	}

	echo '<div class="alert alert-success" role="alert">Success!<button type="button" class="close" data-dismiss="alert" aria-label="Close">
  		<span aria-hidden="true">&times;</span>
	</button></div>';
}


// Handle GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	
	// Get queries: list=students,dorm=male/female,wing=id,student=name,staff=name, else return all interactions

	if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
		$stmt = $conn->prepare("DELETE FROM Interaction WHERE id = ?");
		$stmt->bind_param("i",$_GET['delete']);
		$stmt->execute();
	}
	
	if(isset($_GET['list']) && $_GET['list'] == 'students') {
		
		$sql = "SELECT Concat(first_name, ' ',last_name) AS Name 
				FROM Student 
				ORDER BY last_name ASC";
		
		$result = $conn->query($sql);
		$rows = array();
		while($r = mysqli_fetch_assoc($result)) {
			$rows['students'][] = $r;
		}
		header('Content-Type: application/json');
		print json_encode($rows);
	} elseif (isset($_GET['list']) && $_GET['list'] == 'wing') {
		
		$sql = "SELECT id, Concat(first_name, ' ',last_name) AS Name 
				FROM Wing
				INNER JOIN Staff
				ON Staff.id = Wing.staff_id";
		
		$result = $conn->query($sql);
		$rows = array();
		while($r = mysqli_fetch_assoc($result)) {
			$rows['students'][] = $r;
		}
		header('Content-Type: application/json');
		print json_encode($rows);
	} else {
		$where = '';
		if (isset($_GET['dorm']) && !empty($_GET['dorm'])) {
			$dorm = $conn->real_escape_string($_GET['dorm']);
			$where = "WHERE Student.gender ='{$dorm}'";
		}
		if (isset($_GET['wing']) && !empty($_GET['wing'])) {
			$wing = $conn->real_escape_string($_GET['wing']);
			$where = "WHERE Wing.wing_id ='{$wing}'";
		}

		$sql = "SELECT Interaction.id,
					   date, 
					   Concat_WS(' ',Staff.first_name,Staff.last_name) as staff_member, 
					   GROUP_CONCAT(Student.first_name,' ',Student.last_name ORDER BY Student.last_name ASC Separator ', ') as students, 
					   type, 
					   location, 
					   notes
				FROM Interaction
					INNER JOIN Staff
						ON Interaction.staff_id = Staff.id
					INNER JOIN Students_Interactions
						ON Interaction.id = Students_Interactions.interaction_id
					INNER JOIN Student
						ON Students_Interactions.student_id = Student.id
					INNER JOIN Wing
						ON Student.wing_id = Wing.wing_id
				{$where}
				GROUP BY interaction_id
				ORDER BY Interaction.date DESC";

		$result = $conn->query($sql);
		$rows = array();
		while($r = mysqli_fetch_assoc($result)) {
			$rows['interactions'][] = $r;
		}
		header('Content-Type: application/json');
		print json_encode($rows);
	}

}

$conn->close();

?>