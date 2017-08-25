<?php
/**
 * Main index for interactions app
 *
 * This file provides the initial page for the interactions app including
 * a form for logging student interactions.
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
$staff_id = $_SESSION['staff_id'];

// Create connection
require('config.php');
$conn = new mysqli($host, $username, $password, $db_name);

// Get Staff info
$sql = "SELECT id, first_name, last_name FROM Staff WHERE id={$staff_id}";
$result = $conn->query($sql);
$r = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=0">
		<title>AFLBS Student Interactions</title>

		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
		<!-- jQuery UI CSS -->
    	<link href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" type="text/css" rel="stylesheet">
		<!-- Bootstrap styling for Typeahead -->
	    <link href="css/tokenfield-typeahead.min.css" type="text/css" rel="stylesheet">
	    <!-- Tokenfield CSS -->
	    <link href="css/bootstrap-tokenfield.min.css" type="text/css" rel="stylesheet">
	    <!-- Custom Styles -->
	    <link href="css/style.css" type="text/css" rel="stylesheet">
	    <!-- Apple icons -->
		<link rel="apple-touch-icon" href="./img/touch-icon-iphone.png">
		<link rel="apple-touch-icon" sizes="76x76" href="./img/touch-icon-ipad.png">
		<link rel="apple-touch-icon" sizes="120x120" href="./img/touch-icon-iphone-retina.png">
		<link rel="apple-touch-icon" sizes="152x152" href="./img/touch-icon-ipad-retina.png">
		<meta name="apple-mobile-web-app-title" content="AFLBS">

		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.2/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>

	    <!-- Static navbar -->
	    <nav class="navbar navbar-default navbar-static-top">
	      <div class="container-fluid">
	        <div class="navbar-header">
	          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
	            <span class="sr-only">Toggle navigation</span>
	            <span class="icon-bar"></span>
	            <span class="icon-bar"></span>
	            <span class="icon-bar"></span>
	          </button>
	          <a class="navbar-brand" href="./">Interactions Beta</a>
	        </div>
	        <div id="navbar" class="navbar-collapse collapse">
	          <ul class="nav navbar-nav navbar-right">
	            <li class="active"><a href="./">Home</a></li>
	            <li><a href="./reports.php">Reports</a></li>
	            <li class="dropdown">
	              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Admin <span class="caret"></span></a>
	              <ul class="dropdown-menu">
	                <li><a href="#"></a></li>
	                <li><a href="#">Manage students</a></li>
	                <li><a href="#">Download CSV</a></li>
	                <li role="separator" class="divider"></li>
	                <li class="dropdown-header">User</li>
	                <li><a href="./login/logout.php">Logout</a></li>
	              </ul>
	            </li>
	          </ul>
	        </div><!--/.nav-collapse -->
	      </div>
	    </nav>

	    <div id="content" class="container-fluid">
	    	<div class="row">
	    		<div class="col-lg-4 col-lg-offset-4">
			    	<h2>Log a student interaction</h2>

				    <form id="interactionform" action="ajax.php" method="post">
				    	
				    	<div class="row">
				    		<div class="col-md-6">
						    	<div class="form-group">
						    		<label for="staff">Staff member</label>
						    		<input type="hidden" name="staff-id" <?php echo sprintf('value="%s"',$staff_id); ?> >
						    		<input type="text" class="form-control" name="staff" <?php echo sprintf('value="%s %s"',$r['first_name'], $r['last_name']); ?> readonly required>
						    	</div>
						    </div>
				    	</div>
				    	<div class="row">
				    		<div class="col-md-6">
						    	<div class="form-group">
						    		<label for="date">Date</label>
						    		<input type="date" class="form-control" name="date" required>
						    	</div>
						    </div>
				    	</div>
				    	<div class="row">
				    		<div class="col-md-6">
						    	<div class="form-group">
						    		<label for="type">Interaction Type</label>
									<select id="type" class="form-control" name="type" required>
									  <option>Casual</option>
									  <option>Counseling</option>
									  <option>Discipline</option>
									  <option>Academic</option>
									  <option>Feedback</option>
									</select>
						    	</div>
						    </div>
				    	</div>
				    	<div class="row">
				    		<div class="col-md-6">
						    	<div class="discipline form-group hidden">
						    		<label for="students">Optional Fine</label>
						    		<div class="input-group">
						    		<span class="input-group-addon">$</span>
						    		<input type="number" class="form-control" name="fine">
						    		</div>
						    	</div>
						    </div>
				    	</div>
				    	<div class="row">
				    		<div class="col-md-6">
						    	<div class="discipline form-group hidden">
						    		<label for="duedate">Fine Due Date</label>
						    		<input type="date" class="form-control" name="duedate">
						    	</div>
						    </div>
				    	</div>
				    	<div class="row">
				    		<div class="col-md-6">
						    	<div class="form-group">
						    		<label for="location">Location</label>
									<select class="form-control" name="location" required>
									  <option>Dorm</option>
									  <option>On-Campus</option>
									  <option>Off-Campus</option>
									  <option>Meal</option>
									</select>
						    	</div>
						    </div>
				    	</div>
				    	<div class="row">
				    		<div class="col-sm-12">
						    	<div class="form-group">
						    		<label for="students">Students</label>
						    		<input id="input-students" type="text" class="form-control" name="students" required>
						    		<span class="help-block">Exact spelling is required. Please use the suggestions.</span> 
						    	</div>
						    </div>
				    	</div>
				    	<div class="row">
				    		<div class="col-sm-12">
				    			<div class="form-group">
				    				<label for="notes">Notes</label>
				    				<textarea class="form-control" rows="6" name="notes"></textarea>
				    			</div>
				    		</div>
				    	</div>

				    	<button type="submit" class="btn btn-default btn-primary">Submit</button>
				    </form>
				    <div class="row">
			    		<div class="col-md-12">
						    <div id="formResponse"></div>
			    		</div>
			    	</div>
	    		</div>
	    		<div class="col-md-3 col-md-offset-1 text-center hidden-md-up">
	    			<div class="well">
	    				<h4>Helpful tips</h4>
	    				<p>You can add this app to your iPhone homescreen by clicking on the share icon at the bottom of this page</p>
	    				<p>When you login, iPhone will ask you if you want to save your password, click yes.</p>
	    			</div>
	    		</div>
	    	</div>
	    </div>

	    <footer class="footer">
	      <p class="text-muted">Created by Ben Dahl</p>
	    </footer>

		<!-- jQuery -->
		<script src="//code.jquery.com/jquery.js"></script>
		<script   src="https://code.jquery.com/ui/1.10.3/jquery-ui.min.js"   integrity="sha256-lnH4vnCtlKU2LmD0ZW1dU7ohTTKrcKP50WA9fa350cE="   crossorigin="anonymous"></script>
		
		<!-- Bootstrap JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

		<!-- Bootstrap Tokenfield -->
		<script type="text/javascript" src="js/bootstrap-tokenfield.min.js" charset="UTF-8"></script>
	    <script type="text/javascript" src="js/scrollspy.js" charset="UTF-8"></script>
	    <script type="text/javascript" src="js/affix.js" charset="UTF-8"></script>
	    <script type="text/javascript" src="js/typeahead.bundle.min.js" charset="UTF-8"></script>
	    <script type="text/javascript" src="js/docs.min.js" charset="UTF-8"></script>

		<script type="text/javascript">
			
			// Load the students data into the tokenfield
			$(document).ready(function() {
				$.getJSON("ajax.php?list=students", function(data){
					var arr = $.map(data.students, function(el) { return el.Name});
					$('#input-students').tokenfield({
					  autocomplete: {
					    source: arr,
					    delay: 100
					  },
					  showAutocompleteOnFocus: true
					})
				});				
			});

			// Conditionally Show the Fine fields
			$('#type').change(function() {
				if (this.value == 'Discipline') {
					$('.discipline').removeClass('hidden');
				} else {
					$('.discipline').addClass('hidden');
				}
			});

			// Interaction Form Ajax
			$('#interactionform').submit(function(e) {
				var data = $(this).serialize();
				$.post("./ajax.php", data,
				    function(data, status){
				    	$('#formResponse').html( data );
				    	// Reset form on success
				    	$('#input-students').tokenfield('setTokens', []);
					    $('.discipline').addClass('hidden');
					    $('#interactionform')[0].reset();
				    })
					.fail(function(data, status, errorThrown) {
						alert( errorThrown + ": " + data.responseText );
				});
			    e.preventDefault();
			 });
		</script>
	</body>
</html>