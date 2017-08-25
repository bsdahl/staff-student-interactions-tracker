app.controller('myCtrl', function($scope, $http) {
	// Initial Load
	$scope.rows = [];
	$http.get("./ajax.php")
    .then(function (response) {
    	$scope.rows = response.data.interactions;
    	$scope.totalItems = $scope.rows.length;
    });

    // Filter Dorm
    $scope.getDorm = function() {
    	$http.get("./ajax.php", { params: {dorm: $scope.dorm } })
    	.then(function (response) {
    		$scope.rows = response.data.interactions;
    		$scope.totalItems = $scope.rows.length;
    	});
    	document.getElementById('filter-wing').reset();
    }

    // Filter Dorm
    $scope.getWing = function() {
    	$http.get("./ajax.php", { params: {wing: $scope.wing } })
    	.then(function (response) {
    		$scope.rows = response.data.interactions;
    		$scope.totalItems = $scope.rows.length;
    	});
    	document.getElementById('filter-dorm').reset();
    }

    // Delete record
    $scope.delete = function(id) {
        $http.get("./ajax.php", { params: {delete: id }})
        .then(function (response) {
            $scope.rows = response.data.interactions;
            $scope.totalItems = $scope.rows.length;
        });
    }

    // Setup Select
	$scope.typeOptions = [
	    { name: '10', value: '10' }, 
	    { name: '25', value: '25' }, 
	    { name: '50', value: '50' },
	    { name: '100', value: '100' },
	    { name: 'All', value: '9999' }
    ];
    
    

    // Filters
    $scope.sortType = 'date';
    $scope.sortReverse = 1;
    $scope.dorm = '';
    $scope.wing = '';
	$scope.searchStaff = '';
	$scope.searchStudent = '';

	// Pagination

	
	$scope.currentPage = 1;
	$scope.numPerPage = $scope.typeOptions[0].value;
	//$scope.numPerPage = 10;

	$scope.paginate = function(value) {
	    var begin, end, index;
	    begin = ($scope.currentPage - 1) * $scope.numPerPage;
	    end = begin + $scope.numPerPage;
	    index = $scope.rows.indexOf(value);
	    return (begin <= index && index < end);
  };


    
});

  //We already have a limitTo filter built-in to angular,
//let's make a startFrom filter
app.filter('startFrom', function() {
    return function(input, start) {
        start = +start; //parse to int
        return input.slice(start);
    }
});