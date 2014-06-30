
var App = angular.module('mission-next', ['ngRoute'], function ($interpolateProvider) {
    $interpolateProvider.startSymbol('<%');
    $interpolateProvider.endSymbol('%>');
});

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











