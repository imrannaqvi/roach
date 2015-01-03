'use strict';

app.controller('AuthenticationController', function ($scope, $rootScope, $routeParams, $location, $http, API) {
	//initially set those objects to null to avoid undefined error
	$scope.login_error = false;
	$scope.login = function () {
		API.login($scope.authentication).then(function(data){
			$scope.login_error = false;
			console.log('login success:', data);
			$location.path('/');
		}, function(data){
			$scope.login_error = true;
			console.log('login failure:', data);
		});
	};
});
