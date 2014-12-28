app.controller('authCtrl', function ($scope, $rootScope, $routeParams, $location, $http, API) {
	//initially set those objects to null to avoid undefined error
	$scope.login_error = false;
	$scope.login = function () {
		API.login($scope.authentication).then(function(data){
			$scope.login_error = false;
			console.log('login success:', data);
			//TODO: save token to localStorage
			$location.path('dashboard');
		}, function(data){
			$scope.login_error = true;
			console.log('login failure:', data);
		});
	};
});
