'use strict';

angular.module('app')
	.controller('HeaderCtrl', ['$rootScope', 'localStorageService', '$scope', function($rootScope, localStorageService, $scope) {
		$scope.user = localStorageService.get('currentUser');
		$scope.$on('profile-update', function() {
			$scope.user = localStorageService.get('currentUser');
		});
	}]);
