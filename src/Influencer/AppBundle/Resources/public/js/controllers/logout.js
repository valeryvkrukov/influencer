'use strict';

angular.module('app')
	.controller('LogoutCtrl', ['$scope', '$location', '$auth', 'localStorageService', function($scope, $location, $auth, localStorageService) {
		if (!$auth.isAuthenticated()) { 
			return; 
		}
		$auth.logout().then(function() {
			$scope.user = {};
			localStorageService.clearAll();
			$location.path('/');
		});
	}]);
