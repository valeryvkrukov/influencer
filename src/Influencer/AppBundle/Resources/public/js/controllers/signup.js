'use strict';

angular.module('ui.select').config(function($provide) {
    $provide.decorator('uiSelectChoicesDirective', function($delegate) {
        var directive = $delegate[0];

        var templateUrl = directive.templateUrl;

        directive.templateUrl = function(tElement) {
            tElement.addClass('ui-select-choices');
            return templateUrl(tElement);
        };

        return $delegate;
    });

    $provide.decorator('uiSelectMatchDirective', function($delegate) {
        var directive = $delegate[0];

        var templateUrl = directive.templateUrl;

        directive.templateUrl = function(tElement) {
            tElement.addClass('ui-select-match')
            return templateUrl(tElement);
        };

        return $delegate;
    });
});

angular.module('app')
	.factory('CheckForUnique', ['$q', '$http', function($q, $http) {
		return {
			checkForUsername: function(username) {
				return $http({
        			url: Routing.generate('inf_check_for_username'),
        			method: 'POST',
        			data: {
        				'username': username
        			}
        		});
			},
			checkForEmail: function(email) {
				return $http({
        			url: Routing.generate('inf_check_for_email'),
        			method: 'POST',
        			data: {
        				'email': email
        			}
        		});
			}
		};
	}])
	.factory('GetPredefinedVars', ['$q', '$http', function($q, $http) {
		return {
			getIntl: function() {
				var deferred = $q.defer();
				$http.get(Routing.generate('inf_get_intl_vars')).then(function(resp) {
					//if (resp.status === 200) {
						deferred.resolve(resp.data);
					/*} else {
						deferred.reject();
					}*/
				});
				return deferred.promise;
			},
			getTypes: function() {
				var deferred = $q.defer();
				$http.get(Routing.generate('inf_get_post_types')).then(function(resp) {
					//if (resp.status === 200) {
						deferred.resolve(resp.data);
					/*} else {
						deferred.reject();
					}*/
				});
				return deferred.promise;
			},
			getSocialNetworks: function() {
				var deferred = $q.defer();
				$http.get(Routing.generate('inf_get_social_networks')).then(function(resp) {
					//if (resp.status === 200) {
						deferred.resolve(resp.data);
					/*} else {
						deferred.reject();
					}*/
				});
				return deferred.promise;
			}
		};
	}])
	.factory('Register', ['$q', '$http', function($q, $http) {
		return {
			submitInfluencerData: function(data) {
				var deferred = $q.defer();
				$http({
					url: Routing.generate('inf_registration_save'),
					method: 'POST',
					data: data
				}).then(function(resp) {
					console.log(resp);
					deferred.resolve(resp.data);
				}, function() {
					deferred.reject();
				});
				return deferred.promise;
			}
		};
	}])
	.controller('SignupCtrl', ['$scope', '$sce', '$location', '$auth', 'toastr', 'GetPredefinedVars', 'WizardHandler', 'Register', function($scope, $sce, $location, $auth, toastr, GetPredefinedVars, WizardHandler, Register) {
		$scope.languages = [];
		$scope.countries = [];
		$scope.networks = [];
		$scope.postTypes = [];
		$scope.price = {};
		$scope.user = {};
		$scope.initIntlVars = function() {
			GetPredefinedVars.getIntl().then(function(resp) {
				if (resp.data.countries) {
					$scope.countries = resp.data.countries;
				}
				if (resp.data.languages){
					$scope.languages = resp.data.languages;
				}
			});
			GetPredefinedVars.getTypes().then(function(resp) {
				if (resp.data.types) {
					$scope.postTypes = resp.data.types;
				}
			});
			GetPredefinedVars.getSocialNetworks().then(function(resp) {
				if (resp.data.networks) {
					$scope.networks = resp.data.networks;
				}
			})
		};
		$scope.sizeOf = function(obj) {
		    return Object.keys(obj).length;
		};
		$scope.steps = {
			main: false,
			audience: false,
			campaign: false
		};
		$scope.trustAsHtml = function(value) {
            return $sce.trustAsHtml(value);
        };
        $scope.registrationFinished = function() {
			/*registerService($scope.user, $scope.user.user_type).then(function(response) {
				console.log(response);
			});*/
        	Register.submitInfluencerData($scope.user).then(function(resp) {
        		if (resp.status === 200) {
        			$location.path('/');
        		} else {
        			alert(resp.data.error);
        		}
        	});
		};
		$scope.goToStep = function(step){
        	WizardHandler.wizard().goTo(step - 1);
        };
        $scope.getCurrentStep = function(){
        	return WizardHandler.wizard().currentStepNumber();
        };
        $scope.goBack = function() {
            WizardHandler.wizard().goTo(0);
        };
        $scope.setSubmitted = function(form) {
        	angular.forEach(form.$error, function (field) {
    	        angular.forEach(field, function(errorField){
    	            errorField.$setTouched();
    	            errorField.$pristine = false;
    	        })
    	    });
        	form.$setSubmitted();
        };
        $scope.checkForMain = function() {
        	return $scope.steps['main'];
        };
        $scope.checkForAudience = function() {
        	return $scope.steps['audience'];
        };
        $scope.checkForCampaign = function() {
        	return $scope.steps['campaign'];
        };
        $scope.saveMainInfo = function(mainInfluencerInfo) {
        	if (mainInfluencerInfo.$valid) {
        		$scope.steps['main'] = true;
        		WizardHandler.wizard().next();
        	} else {
        		$scope.setSubmitted(mainInfluencerInfo);
        		$scope.steps['main'] = false;
        	}
        };
        $scope.saveAudienceInfo = function(audienceAndInfluencerDataInfo) {
        	$scope.user.audience = [];
        	if ($scope.user.website === undefined) {
        		$scope.user.website = '';
        	}
        	angular.forEach(angular.element('#audience-list').find('.tag'), function(val, key) {
        		$scope.user.audience.push(val.innerText);
			});
        	if (audienceAndInfluencerDataInfo.$valid && $scope.user.audience.length > 0) {
        		$scope.steps['audience'] = true;
        		WizardHandler.wizard().next();
        	} else {
        		$scope.setSubmitted(audienceAndInfluencerDataInfo);
        		$scope.steps['audience'] = false;
        	}
        };
        $scope.saveCampaignInfo = function(campaignInfluencerInfo) {
        	if (campaignInfluencerInfo.$valid) {
        		$scope.steps['campaign'] = true;
        		WizardHandler.wizard().next();
        	} else {
        		$scope.setSubmitted(campaignInfluencerInfo);
        		$scope.steps['campaign'] = false;
        	}
        };
        $scope.connectAccount = function(network) {
        	$auth.link(network).then(function(resp) {
        		if ($scope.user.socials === undefined) {
    				$scope.user.socials = {};
    			}
        		if (resp.data.profile.id) {
        			$scope.user.socials[network] = {
        				id: resp.data.profile.id,
        				token: resp.data.token
        			};
        		}
        		if (resp.data.profile.sub) {
        			$scope.user.socials[network] = {
        				id: resp.data.profile.sub,
        				token: resp.data.token
        			};
        		}
        	});
        };
	}]);