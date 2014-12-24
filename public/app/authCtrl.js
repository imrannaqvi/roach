app.controller('authCtrl', function ($scope, $rootScope, $routeParams, $location, $http, API) {
	//initially set those objects to null to avoid undefined error
	$scope.login = function (user) {
		API.login(user, function(data){
			console.log(data);
		});
	};
});
