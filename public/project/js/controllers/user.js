(function(){
    var userControllers = angular.module('userControllers', []);

    userControllers.config(usersRouter);

    function usersRouter($routeProvider){

        $routeProvider
        .when(
            '/', { templateUrl: Routing.buildUrl('/user/list'), controller: "UserListCtl" }
        ).when(
            '/:user', { templateUrl: Routing.buildTemplateUrl('/users/user.html'), controller: "UserCtl" }
        );

    }

    userControllers.controller("UserListCtl",['$scope',function($scope){
        console.log("user list");

    }]);

    userControllers.controller("UserCtl",['$scope', '$routeParams', '$http', function($scope, $params, $http){
        $scope.user = { 'status' : null };
        $http.get(Routing.buildUrl('/user/'+$params.user)).success(function(user){
            $scope.user = user;
        });
    }]);

    App.requires.push('userControllers');

})();



