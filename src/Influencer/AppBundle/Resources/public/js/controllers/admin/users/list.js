'use strict';

angular.module('app')
	.controller('AdminUsersListCtrl', ['$scope', '$http', '$q', 'DTOptionsBuilder', 'DTColumnBuilder', function($scope, $http, $q, DTOptionsBuilder, DTColumnBuilder) {
		$scope.dtOptions = DTOptionsBuilder.fromFnPromise(function() {
			
		});
	}]);
		