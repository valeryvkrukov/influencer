'use strict';

angular.module('app')
	.factory('Wallet', ['$q', '$http', function($q, $http) {
		return {
			getPayments: function(type) {
				var deferred = $q.defer();
				$http({
					url: Routing.generate('inf_wallet_get_payments', {type: type}),
					method: 'GET'
				}).then(function(resp) {
					deferred.resolve(resp.data);
				});
				return deferred.promise;
			},
			getInfo: function() {
				var deferred = $q.defer();
				$http({
					url: Routing.generate('inf_wallet_get_info'),
					method: 'GET'
				}).then(function(resp) {
					deferred.resolve(resp.data);
				});
				return deferred.promise;
			}
		};
	}])
	.controller('WalletCtrl', ['$scope', '$http', '$compile', '$filter', 'localStorageService', 'Wallet', 'DTDefaultOptions', 'DTOptionsBuilder', 'DTColumnBuilder', function($scope, $http, $compile, $filter, localStorageService, Wallet, DTDefaultOptions, DTOptionsBuilder, DTColumnBuilder) {
		if ($scope.templatePath === undefined) { 
			$scope.user = localStorageService.get('currentUser');
			$scope.templatePath = Routing.generate('inf_wallet', {role: $scope.user.role});
			$scope.dataType = 'payments';
			Wallet.getInfo().then(function(resp) {
				$scope.wallet = resp;
			});
		} else {
			DTDefaultOptions.setLoadingTemplate('<div class="progress-circle-indeterminate"></div>');
			$scope.dtInstance = {};
			$scope.dtOptions = DTOptionsBuilder.fromFnPromise(function() {
					return Wallet.getPayments($scope.dataType);
				})
				.withPaginationType('full_numbers')
				.withDOM("<'row p-l-15 p-r-15'<'col-sm-12 p-b-20 p-t-20'<'pull-right'f>>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'i><'col-sm-7'p>>")
				.withOption('createdRow', function(row, data, dataIndex) {
					$compile(angular.element(row).contents())($scope);
				})
				.withBootstrap();
			$scope.dtColumns = [
				DTColumnBuilder.newColumn('campaign_name').withTitle('Campaign').renderWith(function(data, type, full) {
					return '<span class="font-montserrat all-caps fs-12">'+full.campaign_name+'</span>';
				}),
				DTColumnBuilder.newColumn('budget').withTitle('Budget').renderWith(function(data, type, full) {
					return '<span class="font-montserrat fs-18 text-primary">$'+$filter('number')(full.campaign_budget, 2)+'</span>';
				}),
				DTColumnBuilder.newColumn('campaign_status').withTitle('Project Status').renderWith(function(data, type, full) {
					return '<span class="font-montserrat all-caps fs-12">'+full.campaign_status+'</span>';
				}),
				DTColumnBuilder.newColumn('amount').withTitle('Amount').renderWith(function(data, type, full) {
					var percentage = (parseFloat(full.amount)/parseFloat(full.campaign_budget)*100);
					return '<span class="font-montserrat fs-18 text-primary">$'+$filter('number')(full.amount, 2)+
						' <sup class="text-master text-'+(percentage>=100?'success':'warning')+'">('+$filter('number')(percentage, 2)+'%)</sup></span>';
				}),
				DTColumnBuilder.newColumn('paid_at').withTitle('Date').renderWith(function(data, type, full) {
					var date = new Date(full.paidAt.date);
					console.log(date);
					return '<span class="font-montserrat fs-18 text-master">'+$filter('date')(date, 'MM/dd/yyyy @ h:mma')+'</span>';
				})
			];
		}
		$scope.loadByType = function(type, instance) {
			$scope.dataType = type;
			instance.reloadData();
		};
	}]);
