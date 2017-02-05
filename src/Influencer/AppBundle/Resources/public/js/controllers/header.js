'use strict';

angular.module('app')
	.controller('HeaderCtrl', ['$rootScope', '$scope', function($rootScope, $scope) {
		$scope.first_name = $rootScope.getUserData('first_name');
		$scope.last_name = $rootScope.getUserData('last_name');
		if (!$scope.first_name && !$scope.last_name) {
			$scope.first_name = $rootScope.getUserData('username');
		}
		$scope.profile_image = $rootScope.getUserData('profile_image');
	}]);
