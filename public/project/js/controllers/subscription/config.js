(function(){
    var subscriptionControllers = angular.module('subscriptionControllers', []);

    subscriptionControllers.controller('SubscriptionController', [ '$http', '$scope', function ($http, $scope) {
        var self = this;
        self.conFee = 0;
        self.subscriptionDiscount = 0;

        self.application = window.CurrentApplication;


        var watchPrice = function(p, period, role){
            $scope.$watch(function(){
                return p['price_'+period];
            }, function(newVal, oldVal){
                if (!newVal){
                    p['price_'+period] = role !== 'candidate' ? oldVal : 0;
                }
                var editing = 'editing'+ period.ucfirst();
                self[editing] = null;
//                console.log(oldVal, newVal);
            });
        };

        self.configs = [];

        $http.get(Routing.buildUrl('/subscription/config?app='+self.application.id))
            .success(function (data) {
                console.log(data);
                self.configs = data.config;
                self.conFee = data.conFee;
                self.gracePeriod = data.gracePeriod;
                self.subscriptionDiscount = data.subscriptionDiscount;

                angular.forEach(self.configs, function(config, indexMain){
                   angular.forEach(config.partnership, function(p, index){
                        watchPrice(p, 'month', config.role.key);
                        watchPrice(p, 'year', config.role.key);
                   });
                });
        });


        var prevEdit = { period : '', m : 0, p : 0};

        self.editPrice = function(mainIndex, priceIndex, period){
            var editing = 'editing'+ period.ucfirst(),
                e;
            if ((period !== prevEdit.period) && (prevEdit.period !== '') ){
                e = 'editing' + prevEdit.period.ucfirst();
                console.log(prevEdit);
                self[e] = [];
                self[e][prevEdit.m] = [];
                self[e][prevEdit.m][prevEdit.p] = null;
            }
            prevEdit = { period :period, m : mainIndex, p :priceIndex};

            self[editing] = [];
            self[editing][mainIndex] = [];
            self[editing][mainIndex][priceIndex] = self.configs[mainIndex].partnership[priceIndex]['price_'+ period];
            if (! self[editing][mainIndex][priceIndex]){
                self[editing][mainIndex][priceIndex] = 1;
            }

        };

        self.blurEdit = function(editing, m, p){
            if (editing){
                editing[m][p] = null;
            }
        };


        self.save = function(){
            $.post(Routing.buildUrl('/subscription/config'),
                {
                    configs : self.configs,
                    app : self.application.id,
                    conFee : self.conFee,
                    gracePeriod : self.gracePeriod,
                    subscriptionDiscount: self.subscriptionDiscount
                }
            )
                .done(
                function(data){
                    $('.save-config').removeClass('hidden');
                }
            );
        }

    }]);

    App.requires.push('subscriptionControllers');
})();


