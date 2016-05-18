// var app = angular.module('mainApp', ['ngMaterial','directivelibrary','12thdirective','uiMicrokernel', 'ui.router','ui.sortable' ,'ngAnimate','ngMessages']);
// app.controller('oneCtrl', function ($scope, $state, $objectstore, $mdDialog, $rootScope, UploaderService, $window) {
// 	$scope.url={};	
// 	$scope.url.email="https://abccompany.12thdoor.com";

// 	//$rootScope.Settings12thdoor.profile={};
// 	$scope.submit = function() {
// 		console.log($rootScope.Settings12thdoor);	
// 		var client = $objectstore.getClient("Settings12thdoor");
// 		client.onComplete(function(data) {
// 				//$rootScope.Settings12thdoor=data;
// 				console.log(data);
// 				$mdDialog.show(
// 					$mdDialog.alert()
// 					.parent(angular.element(document.body))
// 					.content('Successfully Saved.')
// 					.ariaLabel('Alert Dialog Demo')
// 					.ok('OK')
// 					.targetEvent(data)
// 					);
// 			});

// 		client.onError(function(data) {
// 			$mdDialog.show(
// 				$mdDialog.alert()
// 				.parent(angular.element(document.body))
// 				.content('There was an error saving the data.')
// 				.ariaLabel('Alert Dialog Demo')
// 				.ok('OK')
// 				.targetEvent(data)
// 				);

// 		});

// 		$rootScope.Settings12thdoor.uniqueRecord = "35";
// 		client.insert($rootScope.Settings12thdoor, {
// 			KeyProperty: "uniqueRecord"
// 		});

// 	}


// 	$scope.changeUrl= function(ev) {
// 		$mdDialog.show({
// 			controller: DialogprofilechangeUrlController,
// 			templateUrl: 'setting_partials/changeUrlProfile.html',
// 			parent: angular.element(document.body),
// 			targetEvent: ev,
// 			clickOutsideToClose:true
// 		})
// 		.then(function(answer) {

// 		}, function() {
// 			$scope.status = 'You cancelled the dialog.';
// 		});
// 	};

// 	function loadcusFieldsprofile(){
// 		$scope.cusFieldsprofile = $rootScope.Settings12thdoor.profile.CusFiel;
// 		console.log($scope.cusFieldsprofile);
// 	};

// 	$scope.addcusfieldsProfile= function(ev) {
// 		$mdDialog.show({
// 			controller: DialogprofileController,
// 			templateUrl: 'setting_partials/addCustomdetailsforProfile.html',
// 			parent: angular.element(document.body),
// 			targetEvent: ev,
// 			clickOutsideToClose:true
// 		})
// 		.then(function(answer) {
// 			if(answer == true){
// 				loadcusFieldsprofile();
// 			}

// 		}, function() {
// 			$scope.status = 'You cancelled the dialog.';
// 		});
// 	};

// 	$scope.deleteprofilerow = function(cusFieldsprofile, index){  
// 		$rootScope.Settings12thdoor.profile.CusFiel.splice(index, 1);
// 	}

// 	$scope.editprofilecusFieldsrow = function(cusFieldsProfileedit, ev) {
// 		console.log(cusFieldsProfileedit);
// 		$mdDialog.show({
// 			controller: DialogEditprofilecusfieldsController,
// 			templateUrl: 'setting_partials/editCustomdetailsforprofile.html',
// 			parent: angular.element(document.body),
// 			targetEvent: ev,
// 			locals: { cusFieldsProfileedit: cusFieldsProfileedit },
// 			clickOutsideToClose:true
// 		})
// 		.then(function(answer) {
// 			if(answer == true){
// 				loadcusFieldsprofile();
// 			}

// 		}, function() {
// 			$scope.status = 'You cancelled the dialog.';
// 		});
// 	};

// 	//....................................................................

// 	function loadlogoimage(){
// 		$scope.imagelogoprofile = $rootScope.Settings12thdoor.profile.name;
// 		console.log($scope.imagelogoprofile);
// 	};

// 	$scope.uploadImage = function(ev) {
// 		$mdDialog.show({
// 			controller: DialogProfileUploadImageController,
// 			templateUrl: 'setting_partials/uploadImage.html',
// 			parent: angular.element(document.body),
// 			targetEvent: ev,
// 			clickOutsideToClose:true
// 		})
// 		.then(function(answer) {
// 			loadlogoimage();
// 		}, function() {
// 			$scope.status = 'You cancelled the dialog.';
// 		});
// 	};

// 	$scope.deletelogoimage = function(imagelogoprofile, index){  
// 		$rootScope.Settings12thdoor.profile.name.splice(index, 1);
// 	}

// 	var save=function(){
// 		$scope.imagearray = UploaderService.loadArray();
// 		if ($scope.imagearray.length > 0) {
// 			for (indexx = 0; indexx < $scope.imagearray.length; indexx++) {
// 				$uploader.upload("45.55.83.253","expenseimagesNew", $scope.imagearray[indexx]);//class expenseimagesNew
// 				$uploader.onSuccess(function(e,data){
// 				});
// 				$uploader.onError(function(e,data){       
// 					var toast = $mdToast.simple()
// 					.content('There was an error, please upload!')
// 					.action('OK')
// 					.highlightAction(false)
// 					.position("bottom right");
// 					$mdToast.show(toast).then(function() {
//         //whatever
//     });
// 				});       
// 			}
// 		};
// 		$scope.expense.UploadImages.val = UploaderService.loadBasicArray();
// 	}

// });

// function DialogprofilechangeUrlController($scope, $mdDialog, $rootScope) {
// 	$scope.hide = function() {
// 		$mdDialog.hide();
// 	};

// 	$scope.cancel = function() {
// 		$mdDialog.cancel();
// 	};

// }

// function DialogprofileController($scope, $mdDialog, $rootScope) {

// 	$scope.fields=[];
// 	if(!$rootScope.Settings12thdoor.profile.CusFiel)
// 		$rootScope.Settings12thdoor.profile.CusFiel=[];

// 	$scope.submit = function() {
// 		$rootScope.Settings12thdoor.profile.CusFiel.push({
// 			labelshown:$scope.labelshown,
// 			inputType:$scope.inputType,
// 			textBoxFields:$scope.textBoxFields,
// 			fields:$scope.fields,
// 			type:$scope.type
// 		})

// 		$mdDialog.hide();
// 		console.log($rootScope.Settings12thdoor.profile.CusFiel);
// 	}

// 	$scope.hide = function() {
// 		$mdDialog.hide();
// 	};

// 	$scope.cancel = function() {
// 		$mdDialog.cancel();
// 	};


// };

// function DialogEditprofilecusfieldsController($scope, $mdDialog, $rootScope, cusFieldsProfileedit) {

// 	$scope.Settings12thdoor=angular.copy(cusFieldsProfileedit);
// 	console.log($scope.Settings12thdoor);

// 	$scope.submit = function(obj) {
// 		$rootScope.Settings12thdoor.profile.CusFiel.splice($scope.Settings12thdoor,1);
// 		$rootScope.Settings12thdoor.profile.CusFiel.push(obj);
// 		console.log($scope.Settings12thdoor);
// 		$mdDialog.hide();
// 	}

// 	$scope.hide = function() {
// 		$mdDialog.hide();
// 	};

// 	$scope.cancel = function() {
// 		$mdDialog.cancel();
// 	};
// };