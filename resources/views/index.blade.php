<!doctype html>
<html lang="zh" ng-app="xiaohu" ng-controller="BaseController" user-id="{{ session('user_id') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="\node_modules\normalize-css\normalize.css">
    <link rel="stylesheet" href="\css\main.css">
    <script src="\node_modules\jquery\dist\jquery.min.js"></script>
    <script src="\node_modules\angular\angular.min.js"></script>
    <script src="\node_modules\angular-ui-router\release\angular-ui-router.min.js"></script>
    <script src="\js\main.js"></script>
    <script src="\js\user.js"></script>
    <script src="\js\answer.js"></script>
    <script src="\js\question.js"></script>
    <script src="\js\common.js"></script>
    <title>晓乎</title>
</head>
<body>
    <nav class="navbar clearfix">
        <div class="container">
            <div class="fl">
                <div class="navbar-item brand">
                    <h1 ui-sref="home">晓乎</h1>
                </div>
                <div class="navbar-item search" ng-controller="QuestionController">
                    <form ng-submit="Question.go_add_question()" id="quick_ask">
                        <input type="text" ng-model="Question.new_question.title">
                        <button type="submit">提问</button>
                    </form>
                </div>
            </div>
            <div class="fr">
                <a href="" class="navbar-item" ui-sref="home">首页</a>
                @if (isLog())
                    <a href="" ui-sref="user" class="navbar-item">{{ session('user_name') }}</a>
                    <a href="{{ url('api/user/logout') }}" class="navbar-item">登出</a>
                @else
                    <a href="" class="navbar-item" ui-sref="login">登录</a>
                    <a href="" class="navbar-item" ui-sref="signup">注册</a>
                @endif
            </div>
        </div>
    </nav>
    <div class="page">
        <div ui-view></div>
    </div>
    <script type="text/ng-template" id="comment.tpl">
        <div class="comment-block">
            <div class="comment-item-set">
                <div class="hr"></div>
                <div class="rect"></div>
                <div ng-if="!getObjectLength(data)" class="no-data">暂无评论</div>
                <div ng-if="getObjectLength(data)">
                    <div ng-repeat="item in data" class="comment-item clearfix">
                        <div class="user">[: item.user.username :]：</div>
                        <div class="comment-content">
                            [: item.content :]
                        </div>
                    </div>
                </div>
            </div>
            <form ng-submit="_.add_comment()" class="comment-form clearfix">
                <div class="input-group comment-input">
                    <input type="text" name="content" class="i-data"
                        ng-model="Answer.new_comment.content">
                </div>
                <div class="input-group comment-btn">
                    <button type="submit" class="primary">提交</button>
                </div>
            </form>
        </div>
    </script>
</body>
</html>