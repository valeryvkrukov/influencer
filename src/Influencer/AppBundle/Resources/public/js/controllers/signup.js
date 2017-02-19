'use strict';

angular.module('app')
	.controller('SignupCtrl', ['$scope', '$sce', '$location', '$auth', 'toastr', 'GetPredefinedVars', 'WizardHandler', 'Register', 'CheckForUnique', function($scope, $sce, $location, $auth, toastr, GetPredefinedVars, WizardHandler, Register, CheckForUnique) {
		$scope.languages = [];
		$scope.countries = [];
		$scope.categories = [];
		$scope.networks = [];
		$scope.postTypes = [];
		$scope.price = {};
		$scope.user = {};
		$scope.dateOptions = {
			maxDate: '-10y',
			dateFormat: 'yy-mm-dd'
		};
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
        $scope.setProfileImage = function($file, $event, $flow) {
        	var fileReader = new FileReader();
			fileReader.onload = function(event) {
				$scope.user.profileImage = event.target.result;
				//delete $flow.files[0];
				$scope.$apply(function($scope){
	                  $scope.myImage=event.target.result;
	                });
			};
			fileReader.readAsDataURL($file.file);
        };
        $scope.cropProfileImage = function(obj) {
        	$scope.sourceImage = '';
        	$scope.croppedImage = '';
            var fileReader = new FileReader();
            fileReader.onload = function(event) {
				$scope.$apply(function($scope){
					$scope.sourceImage = event.target.result;
	            });
			};
			fileReader.readAsDataURL(obj.files[0].file);
        };
        $scope.saveCroppedImage = function(croppedImage) {
        	$scope.user.profileImage = croppedImage;
        };
        $scope.removeProfileImage = function(obj) {
        	obj.cancel();
        	$scope.user.profileImage = null;
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
        	if (audienceAndInfluencerDataInfo.$valid) {
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
	}]).directive('ensureUnique', ['$http', function($http) {
		return {
			require: 'ngModel',
			link: function(scope, ele, attrs, c) {
				scope.$watch(attrs.ngModel, function() {
					$http({
						url: Routing.generate('inf_check_for_email'),
						method: 'POST',
						data: {'email': ele.val()}
					}).then(function(resp) {
						if (resp.data.status == 'fail') {
							c.$setValidity('unique', false);
						} else {
							c.$setValidity('unique', true);
						}
					});
				});
			}
		}
	}]);	
