<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return view('index');
});

function isLog() {
    $user_id = session('user_id');
    return $user_id? $user_id:false;
}
// 分页
function paginate($page, $limit) {
    $limit = $limit? $limit:16;   // 默认是$limie=16，$page=1
    $skip = ($page ? $page-1 : 0) * $limit;
    return [$limit, $skip];
}

function err($msg) {
    return ['status'=>0, 'msg'=>$msg];
}
function suc($data_to_merge = null) {
    $data = ['status'=>1];
    if ($data_to_merge) {
        $data = array_merge($data, $data_to_merge);
    }
    return $data;
}
function user_ins() {
    return new App\User();    // 创建视图实例
}
function question_ins() {
    return new App\Question(); //创建问题实例
}
function answer_ins() {
    return new App\Answer(); //创建回答实例
}
function comment_ins() {
    return new App\Comment();   //创建评论实例
}
function get_requestDate($key=null, $default=null) {
    if (!$key)  return Request::all();
    return Request::get($key, $default);
}

// 注册用户
Route::any('api/user/signup', function () {
    return user_ins()->signUp();    // 并返回调用的方法
});
// 登录用户
Route::any('api/user/login', function () {
    return user_ins()->login();
});
// 登出
Route::any('api/user/logout', function () {
    return user_ins()->logout();
});
// 修改密码
Route::any('api/user/change_password', function () {
    return user_ins()->changePassword();
});
// 检查用户是否存在
Route::any('api/user/exists', function () {
    return user_ins()->exists();
});
// 找回密码
Route::any('api/user/reset_password', function () {
    return user_ins()->reset_password();
});
// 验证找回密码
Route::any('api/user/validate_reset_password', function () {
    return user_ins()->validate_reset_password();
});
// 读取数据
Route::any('api/user/read', function () {
    return user_ins()->read();
});
// test，检查用户是否登录
Route::any('test', function () {
    dd(user_ins()->isLog());
});
// 添加问题
Route::any('api/question/add', function () {
    return question_ins()->add();
});
// 修改问题
Route::any('api/question/alert', function () {
    return question_ins()->alter();
});
// 查询问题
Route::any('api/question/read', function () {
    return question_ins()->read();
});
// 删除问题
Route::any('api/question/remove', function () {
    return question_ins()->remove();
});
// 添加回答
Route::any('api/answer/add', function () {
    return answer_ins()->add();
});
// 修改答案
Route::any('api/answer/alter', function () {
    return answer_ins()->alter();
});
// 查看答案
Route::any('api/answer/read', function () {
    return answer_ins()->read();
});
// 删除答案
Route::any('api/answer/remove', function () {
    return answer_ins()->remove();
});
// 答案投票
Route::any('api/answer/vote', function () {
    return answer_ins()->vote();
});
// 评论添加
Route::any('api/comment/add', function () {
    return comment_ins()->add();
});
// 评论读取
Route::any('api/comment/read', function () {
    return comment_ins()->read();
});
// 评论删除
Route::any('api/comment/remove', function () {
    return comment_ins()->remove();
});
// 通用api，时间线
Route::any('api/timeline', ['uses'=>'CommonController@timeline']);

Route::any('tpl/home', function () {
    return view('tpl.home');
});
Route::any('tpl/login', function () {
    return view('tpl.login');
});
Route::any('tpl/signup', function () {
    return view('tpl.signup');
});
Route::any('tpl/question_add', function () {
    return view('tpl.question_add');
});
Route::any('tpl/question_detail', function () {
    return view('tpl.question_detail');
});
Route::any('tpl/user', function () {
    return view('tpl.user');
});


