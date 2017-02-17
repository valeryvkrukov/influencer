'use strict';

angular.module('app')
	.controller('AdminUsersEditCtrl', ['$scope', '$http', '$stateParams', 'UserEdit', function($scope, $http, $stateParams, UserEdit) {
		$scope.selected = {};
		UserEdit.getInCurrentState($stateParams.id).then(function(resp) {
			$scope.selected = resp;
		});
		$scope.boolToStr = function(arg) {
			return arg?'Enabled':'Disabled';
		};
		$scope.updateUser = function() {
			UserEdit.updateSelectedUser($scope.selected).then(function(resp) {
				console.log(resp);
				$scope.selected = resp;
			});
		};
	}]);