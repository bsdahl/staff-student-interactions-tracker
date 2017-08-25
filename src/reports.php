<?php
/**
 * Reports page for student interactions app
 *
 * This file contains the interface for viewing reports within the student 
 * interactions app. Uses AngularJS to pull data from ajax.php
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

// Lets prefill the wing options
$sql = "SELECT id, Concat(first_name, ' ',last_name) AS Name 
		FROM Wing
		INNER JOIN Staff
		ON Staff.id = Wing.staff_id";
		
$result = $conn->query($sql);

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
		<!-- Fontawesome CSS -->
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
    
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
	            <li><a href="./">Home</a></li>
	            <li class="active"><a href="./reports.php">Reports</a></li>
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

	    <div id="content" class="container-fluid" ng-app="myApp" ng-controller="myCtrl">

	    	<h2>Reports</h2>

	    	<div class="row">
	    		<div class="col-sm-12">
		    		<div class="panel panel-default">
		    			
		    			  <div class="panel-heading">
			    			  <a href="#" data-toggle="collapse" data-target="#demo">
			    			  	<span><i class="glyphicon glyphicon-chevron-up"></i></span>
			    			  </a>
		    			  </div>
		    			  
						  <div id="demo" class="panel-body collapse in">

				    		<form id="filter-dorm" class="form-inline">
				    			<select class="form-control" name="dorm" ng-change="getDorm()" ng-model="dorm">
				    				<option value="">Filter by dorm</option>
				    				<option value="Male">Mens dorm</option>
				    				<option value="Female">Womens dorm</option>
				    			</select>
				    		</form>

				    		<form id="filter-wing" class="form-inline">
				    			<select class="form-control" name="wing" ng-change="getWing()" ng-model="wing">
				    				<option value="">Filter by wing</option>
				    				<?php
				    				while($row = mysqli_fetch_assoc($result)) {
				    					echo sprintf('<option value="%d">%s</option>',$row['id'],$row['Name']);
									}
				    				?>
				    			</select>
				    		</form>

				    		<form id="staff-search" class="form-inline">
			    				<div class="form-group">
				    				<input id="input-staff" ng-model="searchStaff" type="text" class="form-control" placeholder="Staff Name" name="staff">
				    			</div>
				    			<button type="submit" class="btn btn-default" ng-click="searchStaff = '';">Clear</button>
			    			</form>

			    			<form id="student-search" class="form-inline">
			    				<div class="form-group">
				    				<input id="input-student" ng-model="searchStudent" type="text" class="form-control" placeholder="Student Name" name="student">
				    			</div>
				    			<button type="submit" class="btn btn-default" ng-click="searchStudent = '';">Clear</button>
			    			</form>

				    	</div>
				    </div>
	    		</div>
	    	</div>

			<div class="row">
				<div class="col-sm-12">
					<form id="display-records" class="form-inline">
						<select class="form-control" name="dorm" ng-model="numPerPage" ng-options='option.value as option.name for option in typeOptions'>
						</select>
						Records per page
					</form>
				</div>
			</div>

	    	<div class="panel panel-default">
	    		<div class="panel-heading"></div>
				<div class="table-responsive" style="-webkit-overflow-scrolling: touch;">
			    	<table class="table table-striped table-bordered table-condensed">
			    		<thead>
			    			<tr>
			    				<th>
			    					<a href="#" ng-click="sortType = 'date'; sortReverse = !sortReverse;">
			    						Date 
			    						<span ng-show="sortType == 'date' && !sortReverse" class="fa fa-caret-down"></span>
			    						<span ng-show="sortType == 'date' && sortReverse" class="fa fa-caret-up"></span>
			    					</a>
			    				</th>
			    				<th>
			    					<a href="#" ng-click="sortType = 'staff_member'; sortReverse = !sortReverse">
			    						Staff Member 
			    						<span ng-show="sortType == 'staff_member' && !sortReverse" class="fa fa-caret-down"></span>
			    						<span ng-show="sortType == 'staff_member' && sortReverse" class="fa fa-caret-up"></span>
			    					</a>
			    				</th>
			    				<th>Students</th>
			    				<th>
			    					<a href="#" ng-click="sortType = 'type'; sortReverse = !sortReverse">
			    						Type
			    						<span ng-show="sortType == 'type' && !sortReverse" class="fa fa-caret-down"></span>
			    						<span ng-show="sortType == 'type' && sortReverse" class="fa fa-caret-up"></span>
			    					</a>
			    				</th>
			    				<th>
			    					<a href="#" ng-click="sortType = 'location'; sortReverse = !sortReverse">
			    						Location 
			    						<span ng-show="sortType == 'location' && !sortReverse" class="fa fa-caret-down"></span>
			    						<span ng-show="sortType == 'location' && sortReverse" class="fa fa-caret-up"></span>
			    					</a>
			    				</th>
			    				<th>Notes</th>
			    				<th>Delete</th>
			    			</tr>
			    		</thead>
			    		 <tfoot>
						    <tr>
						      <td colspan="7">Total records: {{ rows.length }}</td>
						    </tr>
						  </tfoot>
			    		<tbody>
			    			<tr ng-repeat="row in rows | orderBy:sortType:sortReverse | filter:searchStaff | filter:searchStudent | startFrom:(currentPage-1)*numPerPage | limitTo:numPerPage">
			    				<td nowrap>{{ row.date }}</td>
			    				<td nowrap>{{ row.staff_member }}</td>
			    				<td >{{ row.students }}</td>
			    				<td nowrap>{{ row.type }}</td>
			    				<td nowrap>{{ row.location }}</td>
			    				<td >{{ row.notes }}</td>
			    				<td><span id="{{ row.id }}" ng-click="delete(row.id)" class="glyphicon glyphicon-remove-circle" style="cursor:pointer;" data-toggle="tooltip" data-placement="top" title="Delete?"></span></td>
			    			</tr>
			    		</tbody>
			    	</table>
		    	</div>
	    	</div>

			<pagination 
				total-items="totalItems" 
				ng-model="currentPage" 
				max-size="5" 
				items-per-page="numPerPage" 
				class="pagination">
			</pagination>
	    </div>

	    <footer class="footer">
	      <p class="text-muted">Created by Ben Dahl</p>
	    </footer>

		<!-- jQuery -->
		<script src="//code.jquery.com/jquery.js"></script>
		<script   src="https://code.jquery.com/ui/1.10.3/jquery-ui.min.js"   integrity="sha256-lnH4vnCtlKU2LmD0ZW1dU7ohTTKrcKP50WA9fa350cE="   crossorigin="anonymous"></script>
		
		<!-- Bootstrap JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

		<!-- Load AngularJS -->
	    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.8/angular.min.js"></script>

		<script src="https://code.angularjs.org/1.2.16/angular-resource.js"></script>
		<script src="https://angular-ui.github.io/bootstrap/ui-bootstrap-tpls-0.11.0.js"></script>

	    <script type="text/javascript" src="./js/myApp.js"></script>
	    <script type="text/javascript" src="./js/myCtrl.js"></script>

		<script type="text/javascript">
			/*$( "#input-staff" ).autocomplete({
			 	source: ['Benjamin Dahl','Matthew Quanbek','Matthew Pillman'],
			    minLength: 0,
			}).focus(function () {
			    $(this).autocomplete("search");
			});

			$( "#input-student" ).autocomplete({
			 	source: ['Benjamin Dahl','Matthew Quanbek','Matthew Pillman'],
			    minLength: 0,
			}).focus(function () {
			    $(this).autocomplete("search");
			});*/
			$(document).ready(function() {
				$.getJSON("ajax.php?list=students", function(data){
					var arr = $.map(data.students, function(el) { return el.Name});
					$( "#input-student" ).autocomplete({
					 	source: arr,
					    minLength: 0,
					    change: function (event, ui) { $('this').trigger('change'); }
					});
				});				
			});
		</script>
	</body>
</html>