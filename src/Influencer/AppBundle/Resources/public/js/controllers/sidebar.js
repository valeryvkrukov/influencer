'use strict';

angular.module('app')
	.controller('SidebarCtrl', ['$scope', '$http', 'localStorageService', function($scope, $http, localStorageService) {
		$scope.user = localStorageService.get('currentUser');
	}]);