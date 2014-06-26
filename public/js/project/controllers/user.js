
(function($){
    var userControllers = angular.module('userControllers', []);

    userControllers.config(usersRouter);

    function usersRouter($routeProvider){

        $routeProvider
        .when(
            '/', { templateUrl: '/dashboard/user/list', controller: "UserListCtl" }
        ).when(
            '/:user', { templateUrl: '/js/project/partials/users/user.html', controller: "UserCtl" }
        );

    }

    userControllers.controller("UserListCtl",['$scope',function($scope){

    }]);

    userControllers.controller("UserCtl",['$scope', '$routeParams',function($scope, $params){
            console.log($params.user);

    }]);

})(jQuery);
