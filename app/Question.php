<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    // 添加问题 - 注意这里还没添加验证！！！
    public function add() {
        // 查看是否登录了
        if (!user_ins()->isLog()) {
            return err('login required');
        }

        // 查看是否填写了标题
        if (!get_requestDate('title')) {
            return err('title is required');
        }
        $this->title = get_requestDate('title');

        // 查看是否填写了描述
        if (get_requestDate('desc')) {
            $this->desc = get_requestDate('desc');
        }
        $this->user_id = session('user_id');
        return $this->save()?
            suc(['question add successfully, id: ' . $this->id]):
            err('db save error');
    }

    // 用户修改问题
    public function alter() {
        /* 检查是否登录 */
        if (!user_ins()->isLog()) {
            return err('login required');
        }

        /* 检查是否存在修改指定的id */
        $id = get_requestDate('id');
        if(!$id) {
            return err('id is required');
        }
        /* 查看问题是否存在 */
        $question = $this->find($id);
        if (!$question) {
            return err('question has not exists');
        }

        // 查看修改的id是不是本人
        if ($question->user_id != session('user_id')) {
            return err('limited authority');
        }

        /* 修改数据 */
        $title = get_requestDate('title');
        $desc = get_requestDate('desc');
        if ($title && $title != $question->title) {
            $question->title = $title;
        }
        if ($desc && $desc != $question->desc) {
            $question->desc = $desc;
        }

        return $question->save()?
            suc(['id'=>$id]):
            err('db save error');
    }

    public function read_by_user_id($user_id) {
        $user = user_ins()->find($user_id);
        if (!$user) {
            return err('user is no exists');
        }
        $question = $this->where('user_id', $user_id)->get()->keyBy('id');

        return suc(['data'=>$question->toArray()]);
    }

    /* 查询数据 */
    public function read() {
        if (!user_ins()->isLog()) {
            return err('login required');
        }
        // 查看是否有id参数，如果有直接返回数据
        $id = get_requestDate('id');
        // ini_set('memory_limit', '-1');
        if ($id) {
            $r = $this
                ->with('answers_user')
                ->find($id);
            return suc(['data'=>$r]);
        }

        $user_id = get_requestDate('user_id');
        if ($user_id) {
            if ($user_id === 'self' && !session('user_id')) {
                return err('login required');
            }
            $user_id = $user_id === 'self'? session('user_id'): $user_id;
            return $this->read_by_user_id($user_id);
        }

        // 分页
//        $limit = get_requestDate('limit') ? :15;
//        $skip = (get_requestDate('page') ? get_requestDate('page')-1 : 0) * 15;
        list($limit, $skip) =
            paginate(get_requestDate('page'), get_requestDate('limit'));

        /* 如果不传参，则返回所有数据 */
        $data = $this
            ->orderBy('created_at')
            ->limit($limit)
            ->skip($skip)
            ->get(['id','title','desc','user_id','created_at','updated_at'])
            ->keyBy('id');
        return  suc(['data'=>$data]);
    }

    public function remove() {
        // 检查是否登录
        if (!user_ins()->isLog()) {
            return err('login required');
        }
        // 检查是否存在id
        $id = get_requestDate('id');
        if (!$id) {
            return err('id is required');
        }

        // 获取model，检查是否存在
        $question = $this->find($id);
        if (!$question) {
            return err('question has not exists');
        }
        // 检查问题的所有者是否是创建者
        if ($question->user_id != session('user_id')) {
            return err('limited authority');
        }

        return  $question->delete() ?
            suc():
            err('db save error');
    }

    /* 一个问题属于一个用户 */
    public function user() {
        return $this->belongsTo('App\User');
    }

    /* 一个问题有多个答案 */
    public function answers() {
        return $this->hasMany('App\Answer');
    }

    public function answers_user() {
        return $this
            ->answers()
            ->with('user')
            ->with('users');
    }
}
