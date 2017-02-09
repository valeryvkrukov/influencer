'use strict';

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
		$scope.categories = [];
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
			GetPredefinedVars.getCategories().then(function(resp) {
				if (resp.data.categories) {
					$scope.categories = resp.data.categories;
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
			});
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
        	Register.submitInfluencerData($scope.user).then(function(resp) {
        		if (resp.token) {
        			$location.path('/');
        		} else {
        			alert(resp.error);
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
        	/*angular.forEach(angular.element('#audience-list').find('.tag'), function(val, key) {
        		$scope.user.audience.push(val.innerText);
			});*/
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
        	$auth.link(network, {'link_account': 1}).then(function(resp) {
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