
;(function () {
    'use strict';
    angular.module('common', [])
        .service('TimelineService', ['$http', 'AnswerService',
            function ($http, AnswerService) {
                var me = this;
                me.data = [];
                me.get = function (conf) {
                    if (me.pending || me.no_more_data) return;
                    me.pending = true;
                    conf = conf || { page: me.current_page }

                    // 这个值是判断是滚动页面发出的请求还是页面跳转时的页面刷新
                    // 如果是滚动页面发出的，就在me.data上混加数据，否则重写数据
                    var current_scrollTop = $(window).scrollTop();

                    $http.post('api/timeline', conf)
                        .then(function (request) {
                            if (request.data.status) {
                                /* 当服务器刷新页面时，添加数据到data */
                                if (request.data.data) {
                                    if (request.data.data.length) {
                                        // 滚动刷新数据请求和刷新页面数据请求
                                        if ($(window).scrollTop() !== current_scrollTop) {
                                            me.data = me.data.concat(request.data.data);
                                        }else {
                                            me.data = request.data.data;
                                        }
                                        me.data = AnswerService.vote_count(me.data);
                                        me.current_page++;
                                    }else {
                                        me.no_more_data = true;
                                    }
                                }
                            }else {
                                console.error('network error');
                            }
                        }, function (error) {
                            console.log(error);
                            console.error('network error');
                        })
                        .finally(function () {
                            me.pending = false;
                        });
                }
                me.reset = function () {
                    me.pending = false;     //判断是否在工作
                    me.no_more_data = false;
                    me.current_page = 1;
                }
                /* 在时间线上投票 */
                me.vote = function (conf) {
                    var $r = AnswerService.vote(conf) //调用核心功能
                    // 判断是不是自己为自己点赞，$r不存在则是
                    if ($r) {
                        $r.then(function (request) {
                            if (request) {
                                AnswerService.update_data(conf.id); // 投票成功更新数据
                            }
                        }, function (error) {
                            console.log(error);
                        })
                    }
                }
            }
        ])
        .controller('HomeController', [
            '$scope', 'TimelineService', 'AnswerService',
            function ($scope, TimelineService, AnswerService) {
                var $win = $(window);
                TimelineService.reset();
                $scope.Timeline = TimelineService;
                TimelineService.get();
                $win.scroll(function () {
                    if($win.scrollTop() - ($(document).height()-$win.height()) > -30) {
                        TimelineService.get();
                    }
                });

                /* 监控数据变化 */
                $scope.$watch(function () {
                    return AnswerService.data;
                }, function (new_value, old_value) {
                    var timeline_data = TimelineService.data;
                    for (var k in new_value) {
                        for (var i=0; i<timeline_data.length; i++) {
                           if (k == timeline_data[i].id && timeline_data[i].question_id) {
                               timeline_data[i] = new_value[k];
                           }
                        }
                    }
                    TimelineService.data = AnswerService.vote_count(TimelineService.data);
                }, true);
            }
        ])
        .controller('BaseController', ['$scope', 'AnswerService',
            function ($scope, AnswerService) {
                $scope.self_id = AnswerService.his.id;
            }
        ])
    ;
})();