'use strict';

angular.module('app', ['mgo-angular-wizard'])
	.controller('CampaignCtrl', ['$scope', '$http', 'WizardHandler', function($scope, $http, WizardHandler) {
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
	}]);