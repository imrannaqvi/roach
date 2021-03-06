'use strict';

var app = angular.module('roach', ['ngRoute', 'ngAnimate', 'LocalStorageModule', 'zj.namedRoutes']);
app.config(['$routeProvider', 'localStorageServiceProvider', function ($routeProvider, localStorageServiceProvider) {
	//localStorageServiceProvider settings
	localStorageServiceProvider
	.setPrefix('roach')
	.setStorageType('localStorage');
	//router
	$routeProvider.when('/login', {
		name: 'login',
		title: 'Login',
		templateUrl: 'partials/login.html',
		controller: 'AuthenticationController'
	}).when('/logout', {
		name: 'logout',
		title: 'Logout'
	}).when('/signup', {
		name: 'signup',
		title: 'Signup',
		templateUrl: 'partials/signup.html',
		controller: 'AuthenticationController'
	}).when('/?', {
		name: 'dashboard',
		title: 'Dashboard',
		templateUrl: 'partials/dashboard.html',
		controller: 'AuthenticationController'
	}).otherwise({
		title: 'Page Not Found',
		templateUrl: 'partials/404.html'
	});
}])
.run(function ($rootScope, $location, API, ACL, localStorageService, $NamedRouteService) {
	$rootScope.logout = function(){
		console.log('$rootScope.logout():');
	};
	//TODO: request session by using token from localStorage on other than login url
	$rootScope.$on("$routeChangeStart", function (event, next, current) {
		console.log('$routeChangeStart:', event, next, current);
		//next.$$route.originalPath
		if(next.$$route.originalPath === $NamedRouteService.reverse('logout')) {
			$rootScope.user = false;
			API.token = false;
			API.user = false;
			localStorageService.remove(API.token_key);
			$location.path($NamedRouteService.reverse('login'));
			return;
		}
		//send session request
		if(
			! $rootScope.user && 
			( ! next.$$route || 
			[$NamedRouteService.reverse('login'), $NamedRouteService.reverse('signup')].indexOf(next.$$route.originalPath) === -1 )
		) {
			API.getSession().then(function(data){
				console.log('got session::', $rootScope.user, data);
			}, function(reason){
				console.log('error reason::', reason);
				$location.path($NamedRouteService.reverse('login'));
			});
		}
		//append route dependent title to default title
		$rootScope.title = 'roach';
		if(next.$$route.title) {
			$rootScope.title += ' : '+ next.$$route.title;
		}
	});
});