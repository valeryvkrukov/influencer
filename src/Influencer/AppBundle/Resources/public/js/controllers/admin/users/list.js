'use strict';

angular.module('app')
	.controller('AdminUsersListCtrl', ['$scope', '$http', '$q', '$compile', 'Users', 'DTOptionsBuilder', 'DTColumnBuilder', function($scope, $http, $q, $compile, Users, DTOptionsBuilder, DTColumnBuilder) {
		$scope.users = {};
		$scope.filteredBy = 'all';
		$scope.dtInstance = {};
		$scope.dtOptions = DTOptionsBuilder.fromFnPromise(function() {
				return Users.loadAll($scope.field, $scope.filteredBy);
			})
			.withPaginationType('full_numbers')
			.withDOM("<'row p-l-15 p-r-15'<'col-sm-12 p-b-20 p-t-20'<'pull-right'f>>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'i><'col-sm-7'p>>")
			.withOption('createdRow', function(row, data, dataIndex) {
				$compile(angular.element(row).contents())($scope);
			})
			.withBootstrap();
		$scope.dtColumns = [
			DTColumnBuilder.newColumn('id').withTitle('ID'),
			DTColumnBuilder.newColumn('name').withTitle('Name').renderWith(function(data, type, full) {
				return '<span class="font-montserrat all-caps fs-12">'+full.firstName+' '+full.lastName+'</span>';
			}),
			DTColumnBuilder.newColumn('email').withTitle('Email'),
			DTColumnBuilder.newColumn('contactNumber').withTitle('Contact Number'),
			DTColumnBuilder.newColumn('role').withTitle('Role').renderWith(function(data, type, full) {
				if (full.roles) {
					var cell = '<span class="font-montserrat all-caps fs-12">';
					if (full.roles[0] == 'ROLE_ADMIN') {
						cell += 'System Admin';
					} else if (full.roles[0] == 'ROLE_INFLUENCER') {
						cell += 'Influencer';
					} else {
						cell += 'Client';
					}
					cell += '</span>';
					return cell;
				}
			}),
			DTColumnBuilder.newColumn('status').withTitle('Status').renderWith(function(data, type, full) {
				var cell = '<span class="font-montserrat fs-12';
				if (full.enabled == 1) {
					cell += ' text-success"><i class="fa fa-check"></i> enabled';
				} else {
					cell += ' text-danger"><i class="fa fa-ban"></i> disabled';
				}
				cell += '</span>';
				return cell;
			}),
			DTColumnBuilder.newColumn(null).withTitle('Actions').notSortable().renderWith(function(data, type, full, meta) {
				$scope.users[data.id] = data;
				var cell = '<span class="font-montserrat fs-12">';
				cell += '<a ui-sref="app.users.edit({id: '+data.id+'})" class="b-a b-grey b-rad-sm bg-master-light padding-5 m-l-5"><i class="fa fa-pencil fs-14 text-master"></i></a>';
				cell += '<a ng-click="selectedForDelete = users['+data.id+']" data-toggle="modal" data-target="#deleteUser" class="b-a b-grey b-rad-sm bg-master-light padding-5 m-l-5"><i class="fa fa-times fs-14 text-danger"></i></a>';
				if (full.enabled == 1) {
					cell += '<a ng-click="disableUser(users['+data.id+'], dtInstance)" class="b-a b-grey b-rad-sm bg-master-light padding-5 m-l-5"><i class="fa fa-ban fs-14 text-warning"></i></a>';
				} else {
					cell += '<a ng-click="disableUser(users['+data.id+'], dtInstance)" class="b-a b-grey b-rad-sm bg-master-light padding-5 m-l-5"><i class="fa fa-check fs-14 text-success"></i></a>';
				}
				cell += '</span>';
				return cell;
			})
		];
		$scope.editUser = function(user, instance) {
			console.log(user);
		};
		$scope.disableUser = function(user, instance) {
			Users.disable(user).then(function(resp) {
				instance.reloadData();
			});
		};
		$scope.deleteUser = function(user, instance) {
			Users.deleteUser(user).then(function(resp) {
				instance.reloadData();
			});
		};
		$scope.filterData = function(field, value, instance) {
			$scope.filteredBy = value;
			$scope.field = field;
			instance.reloadData();
		}
	}]);
		