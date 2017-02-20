<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommonController extends Controller
{
    // 时间线
    public function timeline()
    {
        list($limit, $skip) =
            paginate(get_requestDate('page'), get_requestDate('limit'));

        /* 获取时间的数据 */
        $questions = question_ins()
            ->with('user')//找出与user方法，一个问题对应一个用户
            ->limit($limit)// 每页限制多少条数据
            ->skip($skip)// 从第几条数据开始计算
            ->orderBy('updated_at', 'desc')//倒序排序
            ->get();

        /* 获取答案的数据 */
        $answers = answer_ins()
            ->with('question')// 哪个问题
            ->with('user')// 这个是谁回答了
            ->with('users')// 这个是谁提问了
            ->limit($limit)
            ->skip($skip)
            ->orderBy('updated_at', 'desc')
            ->get();
        //dd($answers->toArray());
        // 合并数据
        $data = $answers->merge($questions);

        // 将合并的数据按时间排序
        $data->sortByDesc(function ($item) {
            return $item->created_at;
        });

        $data = $data->values()->all();

        return suc(['data' => $data]);
    }
}
