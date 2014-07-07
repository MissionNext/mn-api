
var App = angular.module('mission-next', ['ngRoute'], function ($interpolateProvider) {
    $interpolateProvider.startSymbol('<%');
    $interpolateProvider.endSymbol('%>');
   });

var Routing = {
    config : { urlPrefix : '/dashboard/ajax', templateUrlPrefix: '/project/views/partials'},

    buildUrl : function(url){

        return this.config.urlPrefix + url;
    },

    buildTemplateUrl : function(url){

        return this.config.templateUrlPrefix + url;
    }
};



String.prototype.ucfirst = function(){

  return this.toString().toLowerCase().replace(/\b[a-z]/g, function(letter) {
        return letter.toUpperCase();
    });
};

App.directive('ngc-blur', function() {
    return {
        restrict: 'A',
        link: function postLink(scope, element, attrs) {
            element.bind('blur', function () {
                scope.$apply(attrs.ngBlur);
            });
        }
    };
});

App.directive('focusIf', [function () {
    return function focusIf(scope, element, attr) {
        scope.$watch(attr.focusIf, function (newVal) {
            if (newVal) {
                scope.$evalAsync(function() {
                    element[0].focus();
                });
            }
        });
    }
}]);











