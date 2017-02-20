<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    // 添加评论
    public function add() {
        // 判断是否登录
        if (!user_ins()->isLog()) {
            return err('login required');
        }

        // 检查评论是否存在
        if (!get_requestDate('content')) {
            return err('content is required');
        }
        $this->content = get_requestDate('content');

        // 判断question_id和answer_id是否存在
        // question_id和answer_id只能存在一个
        if (!(get_requestDate('question_id') || get_requestDate('answer_id')) &&
            (get_requestDate('question_id') && get_requestDate('answer_id'))) {
            return err('either question_id or answer_id is required');
        }

        // question_id存在，保存到数据库中
        $question_id = get_requestDate('question_id');
        if($question_id) {
            // 检查问题是否存在
            $question = question_ins()->find($question_id);
            if(!$question) {
                return err('question has not exists');
            }
            $this->question_id = $question_id;
        }else {
            // 检查答案是否存在
            // answer_id存在，保存到数据库中
            $answer_id = get_requestDate('answer_id');
            $answer = answer_ins()->find($answer_id);
            if(!$answer) {
                return err('answer has not exists');
            }
            $this->answer_id = $answer_id;
        }

        // 检查是否在回复评论
        if (get_requestDate('reply_to')) {
            $target = $this->find(get_requestDate('reply_to'));
            if (!$target) {
                return err('comment of target has not exists');
            }
            // 不能回复自己的评论
            if ($target->user_id == session('user_id')) {
                return err('you do not comment yourself');
            }
            $this->reply_to = get_requestDate('reply_to');
        }
        $this->user_id = session('user_id');

        return $this->save()?
            suc(['id'=>$this->id]):
            err('db save error');
    }

    /* 读取评论数据 */
    public function read() {
        // answer_id和question_id必须存在一个
        if (!get_requestDate('answer_id') && !get_requestDate('question_id')) {
            return err('either answer_id or question_id is required');
        }
        // 根据answer_id来获取数据
        $answer_id = get_requestDate('answer_id');
        if ($answer_id) {
            if (!answer_ins()->find($answer_id)) {
                return err('answer has not exists');
            }
            $data = $this
                ->with('user')
                ->where('answer_id', $answer_id);
        }
        // 根据question_id获取数据
        $question_id = get_requestDate('question_id');
        if ($question_id) {
            if (!question_ins()->find($question_id)) {
                return err('question has not exists');
            }
            $data = $this
                ->with('user')
                ->where('question_id', $question_id);
        }
        $data = $data->get()->keyBy('id');
        return suc(['data'=>$data]);
    }

    public function remove() {
        if(!user_ins()->isLog()) {
            return err('login required');
        }
        if (!get_requestDate('id')) {
            return err('id is required');
        }
        $comment = $this->find(get_requestDate('id'));
        if(!$comment) {
            return err('comment has not exists');
        }

        /* 只有拥有评论者才能删除数据 */
        if ($comment->user_id != session('user_id')) {
            return err('limited authority');
        }

        // 先删除此评论下的所有回复
        $this->where('reply_to', get_requestDate('id'))->delete();

        return $comment->delete()?
            suc():
            err('db save error');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }
}
