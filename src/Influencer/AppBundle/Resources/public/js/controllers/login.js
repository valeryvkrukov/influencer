'use strict';

angular.module('app')
	.controller('LoginCtrl', ['$scope', '$state', '$stateParams', '$auth', '$http', 'Account', 'localStorageService', function($scope, $state, $stateParams, $auth, $http, Account, localStorageService) {
		if ($stateParams.confirmation) {
			$scope.confirmationStatus = $stateParams.confirmation;
		}
		$scope.login = function() {
			$auth.login($scope.user).then(function() {
				Account.getProfile().then(function(resp) {
					localStorageService.set('currentUser', resp.data);
				    $scope.user = resp.data;
				    $state.transitionTo('app.dashboard');
				});
			}).catch(function(error) {
				$scope.error = error.data.message;
			});
		};
		$scope.authenticate = function(provider) {
			$auth.authenticate(provider).then(function() {
				Account.getProfile().then(function(resp) {
					localStorageService.set('currentUser', resp.data);
				    $scope.user = resp.data;
				    $state.transitionTo('app.dashboard');
				});
			}).catch(function(error) {
				if (error.message) {
					$scope.error = error.message;
				} else if (error.data) {
					$scope.error = error.data.message;
				} else {
					$scope.error = error;
				}
			});
		};
		$scope.resetPassword = function() {
			if ($scope.resetEmail) {
				$http({
					url: Routing.generate('inf_reset_password'),
					method: 'POST',
					data: {email: $scope.resetEmail}
				}).then(function(resp) {
					$scope.resetStatus = resp.data.status;
					if (resp.data.status && resp.data.status == 'ok') {
						$scope.resetMessage = 'Email sent. Check please your email';
					} else {
						$scope.resetMessage = 'Error: ' + resp.data.message;
					}
				})
			}
		};
	}]);