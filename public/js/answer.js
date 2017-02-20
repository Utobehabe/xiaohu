
;(function () {
    'use strict'

    angular.module('answer', [])
        .service('AnswerService', ['$http', '$state',
            function ($http, $state) {
                var me = this;
                me.data = {};
                me.answer_form = {};
                me.new_comment = {};
                me.his = {
                    id: parseInt($('html').attr('user-id'))
                };
                /*
                * @answers 既包括问题也包括回答，如果是问题将会跳过统计
                * */
                me.vote_count = function (answers) {
                    for (var i=0; i<answers.length; i++) {
                        var votes,item = answers[i];
                        /* 如果没有question_id或users，则说明不是问题或问题没有票数 */
                        if (!item['question_id'])
                            continue;

                        me.data[item.id] = item;

                        if (!item['users'])
                            continue;

                        /* 添加赞成票和反对票的记录 */
                        item.upvote_count = 0;
                        item.downvote_count = 0;

                        /* users是所有投票用户的用户信息 */
                        votes = item['users'];
                        if (votes) {
                            for (var j=0,len=votes.length; j<len; j++) {
                                var v = votes[j];

                                /*
                                 * 获取pivot元素中的用户投票信息
                                  * 如果是1，则增加赞同票
                                  * 如果是2，则增加反对票
                                 * */
                                if (v['pivot'].vote === 1) {
                                    item.upvote_count++;
                                }else if (v['pivot'].vote === 2){
                                    item.downvote_count++;
                                }
                            }
                        }
                    }
                    return answers;
                }

                me.add_or_update = function (question_id) {
                    if (!question_id) {
                        return "question_id is required";
                    }
                    // console.log(me.answer_form);
                    me.answer_form.question_id = question_id;

                    if (me.answer_form.id) {
                        $http.post('api/answer/alter', me.answer_form)
                            .then(function (request) {
                                me.answer_form = {};
                                $state.reload();
                            });
                    }else {
                        $http.post('api/answer/add', me.answer_form)
                            .then(function (request) {
                                me.answer_form = {};
                                $state.reload();
                            });
                    }
                }

                me.delete = function (id) {
                    if(!id) {
                        return "id is required";
                    }
                    $http.post('api/answer/remove', {id: id})
                        .then(function (request) {
                            $state.reload();
                        });
                }
                
                me.add_comment = function () {
                    return $http.post('api/comment/add', me.new_comment)
                        .then(function (request) {
                            if (request.data.status) {
                                return true;
                            }else {
                                return false;
                            }
                        });
                }

                /*
                * @conf为id和vote的配置项
                * 当没有传递该数据，警告
                * 传递数据，返回一个promise对象
                * */
                me.vote = function (conf, answer) {
                    if (!conf.id || !conf.vote) {
                        console.log('id and vote are required');
                        return;
                    }

                    /* 如果回答已经投过相同的票的时候，就设置vote=3，取消 */
                    answer = answer? answer: me.data[conf.id];
                    var users = answer.users;

                    if (answer.user_id == me.his.id) {
                        console.log('vote not to owner');
                        return false;
                    }

                    for (var i=0; i<users.length; i++) {
                        if (users[i].id == me.his.id && conf.vote == users[i].pivot.vote) {
                            conf.vote = 3;
                        }
                    }

                    return $http.post('api/answer/vote', conf)
                        .then(function (request) {
                            if (request.data.status) {
                                return true;
                            }else if (request.data.msg == 'login required') {
                                $state.go('login');
                            }
                            return false;
                        }, function (error) {
                            return false;
                        });
                }

                /*
                * 判断是数字，使用angular.isNumberic()
                * 判断是数组，angular.isArray()
                * */
                me.update_data = function (id) {
                    return $http.post('api/answer/read', {id: id})
                        .then(function (request) {
                            me.data[id] = request.data.data;
                        }, function (error) {
                            console.log('error', error);
                        });
                }

                me.read = function (params) {
                    return $http.post('api/answer/read', params)
                        .then(function (request) {
                            if (request.data.status) {
                                me.data = angular.merge({}, me.data, request.data.data);
                                return request.data.data;
                            }
                            return false;
                        });
                }
            }
        ])
        .directive('commentBlock', ['$http', 'AnswerService',
            function ($http, AnswerService) {
                var block = {};
                block.scope = {
                    answer_id: '=answerId'
                }

                block.templateUrl = 'comment.tpl';
                block.link = function (scope, ele, attr) {
                    scope._ = {};
                    scope.Answer = AnswerService;
                    scope.data = {};
                    scope.getObjectLength = getObjectLength;
                    ele.on('click', function () {
                    });
                    get_comment_data();
                    scope._.add_comment = function () {
                        AnswerService.new_comment.answer_id = scope.answer_id;
                        AnswerService.add_comment()
                            .then(function (request) {
                                if(request) {
                                    AnswerService.new_comment = {};
                                    get_comment_data();
                                }
                            });
                    }
                    function get_comment_data() {
                        $http.post('api/comment/read', {answer_id: scope.answer_id})
                            .then(function (request) {
                                if (request.data.status) {
                                    scope.data = request.data.data;
                                }
                            });
                    }
                    function getObjectLength(data) {
                        return Object.keys(data).length;
                    }
                };
                return block;
            }
        ])
    ;
})();