'use strict';

angular.module('app')
	.controller('LogoutCtrl', ['$rootScope', '$location', '$auth', function($rootScope, $location, $auth) {
		if (!$auth.isAuthenticated()) { 
			return; 
		}
		$auth.logout().then(function() {
			$rootScope.user = {};
			$location.path('/');
		});
	}]);
