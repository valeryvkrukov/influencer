'use strict';

angular.module('app')
	.directive('infUnique', ['$q', 'CheckForUnique', function($q, CheckForUnique) {
		return {
			restrict: 'A',
			require: 'ngModel',
			link: function (scope, element, attrs, ngModel) {
				ngModel.$asyncValidators.unique = function (modelValue, viewValue) {
					var deferred = $q.defer();
                    var currentValue = modelValue || viewValue;
                    var property = attrs.shUniqueProperty;
					if (property) {
						if (property == 'username') {
							CheckForUnique.checkForUsername(currentValue).then(function(resp) {
								if (resp.data.status == 'success') {
									deferred.resolve();
								} else {
									deferred.reject();
								}
							});
						}
						if (property == 'email') {
							CheckForUnique.checkForEmail(currentValue).then(function(resp) {
								if (resp.data.status == 'success') {
									deferred.resolve();
								} else {
									deferred.reject();
								}
							});
						}
						return deferred.promise;
					} else {
						return $q.when(true);
					}
				}
			}
		};
	}]);