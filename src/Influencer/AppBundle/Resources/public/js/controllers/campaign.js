'use strict';

angular.module('app', ['mgo-angular-wizard'])
	.factory('Influencers', ['$q', '$http', function($q, $http) {
		return {
			loadBySelectedParams: function(params) {
				var deferred = $q.defer();
				$http({
					url: Routing.generate('inf_load_influencers'),
					method: 'POST',
					data: params
				}).then(function(resp) {
					deferred.resolve(resp.data);
				});
				return deferred.promise;
			}
		};
	}])
	.controller('CampaignCtrl', ['$scope', '$http', '$sce', 'WizardHandler', 'GetPredefinedVars', 'Influencers', function($scope, $http, $sce, WizardHandler, GetPredefinedVars, Influencers) {
		$scope.countries = [];
		$scope.languages = [];
		$scope.postTypes = [];
		$scope.campaign = {
			gender: 'both',
			countries: [],
			languages: [],
			postTypes: []
		};
		$scope.init = function() {
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
		};
		$scope.trustAsHtml = function(value) {
            return $sce.trustAsHtml(value);
        };
		$scope.campaignRequested = function() {
            alert("Wizard finished :)");
        };
        $scope.goBack = function() {
        	WizardHandler.wizard().goTo(0);
        };
        $scope.getCurrentStep = function() {
        	return WizardHandler.wizard().currentStepNumber();
        };
        $scope.goToStep = function(step) {
        	WizardHandler.wizard().goTo(step - 1);
        };
        $scope.loadInfluencers = function() {
        	Influencers.loadBySelectedParams($scope.campaign).then(function(resp) {
        		console.log(resp);
        		$scope.campaign.influencers = resp;
        	});
        };
	}]);