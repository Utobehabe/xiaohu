<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    public function add() {
        // 检查用户是否登录
        if (!user_ins()->isLog()) {
            return err('login required');
        }
        // 检查id和content是否存在
        if(!(get_requestDate('question_id') && get_requestDate('content'))) {
            return err('question_id and content are required');
        }
        // 检查问题是否存在
        $question = question_ins()->find(get_requestDate('question_id'));
        if (!$question) {
            return err('question has not exists');
        }
        // 检查是否重复回答
        $answer = $this
            ->where(['user_id'=>session('user_id'), 'question_id'=>get_requestDate('question_id')])
            ->count();
        if ($answer) {
            return err('answer has exists, you has not answer');
        }
        // 保存数据
        $this->content = get_requestDate('content');
        $this->question_id = get_requestDate('question_id');
        $this->user_id = session('user_id');

        return $this->save()?
            suc(['id'=>$this->id]):
            err('db save error');
    }

    /* 修改答案 */
    public function alter() {
        // 查看用户是否登录
        if (!user_ins()->isLog()) {
            return err('login required');
        }
        // 查看修改的id是否存在
        if (!(get_requestDate('id') && get_requestDate('content'))) {
            return err('id and content are required');;
        }
        // 查看修改的答案是否存在
        $answer = $this->find(get_requestDate('id'));
        if (!$answer) {
            return err('answer has not exists');
        }
        // 检测是否有修改权限
        if ($answer->user_id != session('user_id')) {
            return err('limited authority');
        }

        $answer->content = get_requestDate('content');

        // 返回修改的数据
        return $answer->save()?
            suc():
            err('db save error');
    }

    public function read_by_user_id($user_id) {
        $user = user_ins()->find($user_id);
        if (!$user) {
            return err('user is no exists');
        }
        $answer = $this
            ->where('user_id', $user_id)
            ->get()
            ->keyBy('id');

        return suc(['data'=>$answer->toArray()]);
    }


    /* 读取答案 */
    public function read() {
        if(!user_ins()->isLog()) {
            return err('login required');
        }
        if(!(get_requestDate('id') ||
            get_requestDate('question_id') ||
            get_requestDate('user_id'))) {
            return err('either id or question_id or user_id is required');
        }

        // 如果传递的是user_id
        $user_id = get_requestDate('user_id');
        if ($user_id) {
            $user_id = $user_id === 'self'? session('user_id'): $user_id;
            return $this->read_by_user_id($user_id);
        }

        // 如果存在id，检测是否存在回答
        // 查看单一回答
        $id = get_requestDate('id');
        if($id) {
            $answer = $this
                ->with('user')
                ->with('users')
                ->with('question')
                ->find($id);
            if (!$answer) {
                return err('answer has not exists');
            }
            $answer = $this->vote_count($answer);
            // dd($answer->toArray());
            return ['status'=>1, 'data'=>$answer];
        }
        // 查看同一个问题下的所有回答
        $question = question_ins()->find(get_requestDate('question_id'));
        if (!$question) {
            return err('question has not exists');
        }
        $answers = $this
            ->where('question_id', get_requestDate('question_id'))
            ->get()
            ->keyBy('id');
        return suc(['data'=>$answers]);
    }

    public function remove() {
        if (!user_ins()->isLog()) {
            return err('login required');
        }
        $id = get_requestDate('id');
        if (!$id) {
            return err('id is required');
        }
        $answer = $this->find($id);
        if (!$answer) {
            return err('answer is not exists');
        }
        $answer->delete();
        return suc();
    }

    /* 统计票数 */
    public function vote_count($answer) {
        $upvote_count = 0;
        $downvote_count = 0;

        foreach ($answer->users as $user) {
            if ($user->pivot->vote == 1) {
                $upvote_count++;
            }else {
                $downvote_count++;
            }
        }
        $answer->upvote_count = $upvote_count;
        $answer->downvote_count = $downvote_count;
        return $answer;
    }

    /* 投票api */
    public function vote() {
        if (!user_ins()->isLog()) {
            return err('login required');
        }
        if (!get_requestDate('id') || !get_requestDate('vote')) {
            return err('id and vote are required');
        }
        // 查找问题
        $answer = $this->find(get_requestDate('id'));

        if (!$answer) {
            return err('answer has not exists');
        }

        // 投票为1为赞同，2为反对
        $vote = get_requestDate('vote');
        if ($vote != 1 && $vote != 2 && $vote !=3) {
            return err('invalid vote');
        }

        // 检查用户是否在相同问题下投过票，如果投过票就删除投票
        $answer->users()    //调用users()方法
            ->newPivotStatement()   // 切换到中间表answer_user表中
            ->where('user_id', session('user_id'))
            ->where('answer_id', get_requestDate('id'))
            ->delete();

        if ($vote == 3) {
            return suc();
        }

        /* 在连接表中添加数据 */
        $answer->users()
            ->attach(session('user_id'), ['vote'=> $vote]);

        return ['status'=>1];
    }

    /* 添加多对多数据，一个回答能有多个用户投票 */
    public function users() {
        return $this
            ->belongsToMany('App\User') // 多对多
            ->withPivot('vote') // 这个为自定义主键？
            ->withTimestamps(); // 这个字段随数据更新，更新时间
    }

    /* 一个回答属于一个用户 */
    public function user() {
        return $this->belongsTo('App\User');
    }

    /* 一个回答对应一个问题 */
    public function question() {
        return $this->belongsTo('App\Question');
    }
}
