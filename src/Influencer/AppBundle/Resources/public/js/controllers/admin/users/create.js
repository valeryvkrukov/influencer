'use strict';

angular.module('app')
	.controller('AdminUsersCreateCtrl', ['$scope', '$http', '$state', 'UserEdit', function($scope, $http, $state, UserEdit) {
		$scope.newUser = {
			roles: ['ROLE_CLIENT'],
			enabled: false
		};
		$scope.boolToStr = function(arg) {
			return arg?'Enabled':'Disabled';
		};
		$scope.updateUser = function(form) {
			if ($scope.createUserForm.$valid) {
				UserEdit.createNewUser($scope.newUser).then(function(resp) {
					if (resp.error) {
						alert(resp.error);
					} else {
						$scope.newUser = resp;
						$state.transitionTo('app.users.edit', {id: $scope.newUser.id});
					}
				});
			}
		};
	}]);