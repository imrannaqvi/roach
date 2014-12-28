var app = angular.module('roach', ['ngRoute', 'ngAnimate', 'LocalStorageModule']);
app.config(['$routeProvider', 'localStorageServiceProvider', function ($routeProvider, localStorageServiceProvider) {
	//localStorageServiceProvider settings
	localStorageServiceProvider
	.setPrefix('roach')
	.setStorageType('localStorage');
	//router
	$routeProvider.when('/login', {
		title: 'Login',
		templateUrl: 'partials/login.html',
		controller: 'authCtrl'
	}).when('/logout', {
		title: 'Logout',
		templateUrl: 'partials/login.html',
		controller: 'logoutCtrl'
	}).when('/signup', {
		title: 'Signup',
		templateUrl: 'partials/signup.html',
		controller: 'authCtrl'
	}).when('/dashboard', {
		title: 'Dashboard',
		templateUrl: 'partials/dashboard.html',
		controller: 'authCtrl'
	}).when('/', {
		title: 'Login',
		templateUrl: 'partials/login.html',
		controller: 'authCtrl'
	}).otherwise({
		redirectTo: '/login'
	});
}])
.run(function ($rootScope, $location, API) {
	$rootScope.logout = function(){
		console.log('$rootScope.logout():');
	};
	//TODO: request session by using token from localStorage on other than login url
	$rootScope.$on("$routeChangeStart", function (event, next, current) {
		//send session request
		if(
			! $rootScope.user && 
			['/login', '/signup'].indexOf(next.$$route.originalPath) === -1
		) {
			console.log('request session::', $rootScope.user);
			API.getSession().then(function(data){
				console.log('got session::', $rootScope.user, data);
			}, function(reason){
				console.log('error reason::', reason);
				$location.path("/login");
			});
		}
	});
});