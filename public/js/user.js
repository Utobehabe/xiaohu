
;(function () {
    'use strict';
    angular.module('user', ['answer', 'question'])
        .service('UserService', ['$http', '$state', function ($http, $state) {
        var me = this;
        me.signup_data = {};
        me.login_data = {};
        me.login_fail = false;
        me.signup = function () {
            $http.post('api/user/signup', me.signup_data)
                .then(function (request) {
                    if (request.data.status) {
                        me.signup_data = {};    //清空数据
                        $state.go('login'); //跳转到指定路由
                    }
                }, function (error) {   // 如果操作失败，执行此步骤
                    console.log('e', error);
                });
        };
        me.login = function () {
            $http.post('api/user/login', me.login_data)
                .then(function (request) {
                    if(request.data.status) {
                        location.href = '/';
                    }else {
                        me.login_fail = true;
                    }
                }, function (error) {
                    console.log('error', error);
                });
        };
        me.username_exists = function () {
            $http.post('api/user/exists', { username: me.signup_data.username })
                .then(function (request) {  /* 成功执行 */
                    if (request.data.status && request.data.count) {
                        me.signup_username_exists = true;   // 设置注册用户名是否存在
                    }else {
                        me.signup_username_exists = false;
                    }
                }, function (error) { /* 错误执行 */
                    console.log('error', error);
                });
        };
        me.read = function (param) {
            return $http.post('api/user/read', param)
                .then(function (request) {
                    if (request.data.status) {
                        if (param.id === 'self' || angular.isNumber(param.id)) {
                            me.self_data = request.data.data;
                        }
                    }else if (request.data.msg === 'login required'){
                        $state.go('login');
                    }
                });
        }
    }])
        .controller('SignupController', ['$scope', 'UserService',
            function ($scope, UserService) {
                $scope.User = UserService;

                // 第三个参数为递归检查每一项数据
                $scope.$watch(function () {
                    return UserService.signup_data;
                }, function(newValue, oldValue) {
                    if (newValue.username != oldValue.username) {
                        UserService.username_exists();
                    }
                }, true);
            }]
        )
        .controller('LoginController', ['$scope', 'UserService',
            function ($scope, UserService) {
                $scope.User = UserService;
            }]
        )
        .controller('UserController', [
            '$scope', '$stateParams', 'UserService', 'AnswerService', 'QuestionService',
            function ($scope, $stateParams, UserService, AnswerService, QuestionService) {
                $scope.User = UserService;
                UserService.read($stateParams);
                AnswerService.read({'user_id': $stateParams.id})
                    .then(function (request) {
                        if (request) {
                            UserService.his_answers = request;
                        }
                    });
                QuestionService.read({'user_id': $stateParams.id})
                    .then(function (request) {
                        if (request) {
                            UserService.his_questions = request;
                        }
                    });
            }
        ])
    ;
})();