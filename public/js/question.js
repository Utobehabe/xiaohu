
;(function () {
    'use strict';
    angular.module('question', [])
        .service('QuestionService', ['$http', '$state', 'AnswerService',
            function ($http, $state, AnswerService) {
                var me = this;
                me.new_question = {};
                me.data = {};
                me.it_answers = {};
                me.go_add_question = function () {
                    $state.go('question.add');
                };
                me.go = function () {
                    $http.post('api/question/add', me.new_question)
                        .then(function (request) {
                            console.log('request', request);
                            if (request.data.status) {
                                me.new_question = {};
                                $state.go('home');
                            }
                        }, function (error) {
                            console.log('error', error);
                        });
                }
                me.read = function (params) {
                    return $http.post('api/question/read', params)
                        .then(function (request) {
                            if (request.data.status) {
                                if (params.id) {
                                    me.data = request.data.data;
                                    me.it_answers = me.data.answers_user;
                                    me.it_answers = AnswerService.vote_count(me.it_answers);
                                }else {
                                    me.data = angular.merge({}, me.data, request.data.data);
                                }
                                return request.data.data;
                            }else if (request.data.msg == 'login required') {
                                $state.go('login');
                            }
                            return false;
                        });
                }
                me.vote = function (conf) {
                    // 判断当前用户是否投过票
                    /*var answers = me.it_answers;
                    for (var i=0; i<answers.length; i++) {
                        if (answers[i].id == conf.id) {
                            var users = answers[i].users;
                            for (var j=0; j<users.length; j++) {
                                if (users[j].id == his.id && conf.vote == users[j].pivot.vote) {
                                    conf.vote = 3;
                                }
                            }
                        }
                    }

                    $http.post('/api/answer/vote', conf)
                        .then(function (request) {
                            if (request.data.status) {
                                me.update_answer(conf.id);
                            }
                        });*/

                    /* 上面和下面都是一样的 */
                    var answers = me.it_answers;
                    for (var i=0; i<answers.length; i++) {
                        if (answers[i].id == conf.id) {
                            if (AnswerService.vote(conf, answers[i])) {
                                me.update_answer(conf.id);
                            }
                        }
                    }
                }
                me.update_answer = function (answer_id) {
                    $http.post('api/answer/read', {id: answer_id})
                        .then(function (request) {
                            if (request.data.status) {
                                for (var i=0; i<me.it_answers.length; i++) {
                                    var answer = me.it_answers[i];
                                    if (answer.id == answer_id) {
                                        // console.log('answer', answer);
                                        // console.log('request.data.data', request.data.data);
                                        me.it_answers[i] = request.data.data;
                                    }
                                }
                            }
                        });
                }
            }
        ])
        .controller('QuestionController', ['$scope', 'QuestionService',
            function ($scope, QuestionService) {
                $scope.Question = QuestionService;
            }
        ])
        .controller('QuestionAddController', ['$scope', 'QuestionService',
            function ($scope, QuestionService) {
            }
        ])
        .controller('QuestionDetailController', ['$scope', '$stateParams', 'AnswerService', 'QuestionService',
            function ($scope, $stateParams, AnswerService, QuestionService) {
                // console.log($stateParams);
                QuestionService.read($stateParams);
                $scope.detail_answer_id = $stateParams.answer_id;
                $scope.Answer = AnswerService;
            }
        ])
    ;
})();