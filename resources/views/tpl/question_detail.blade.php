
<div ng-controller="QuestionDetailController">
    <div class="container question-detail card">
        <h2>[: Question.data.title :]</h2>
        <div class="desc">[: Question.data.desc || '问题暂无描述' :]</div>
        <div>
            <span class="comment-num">回答数：[: Question.data.answers_user.length :]</span>
        </div>
        <div class="hr"></div>
        <div class="answer-block">
            <div ng-if="!detail_answer_id || detail_answer_id==item.id"
                    ng-repeat="item in Question.data.answers_user" class="feed item">
                <div class="clearfix">
                    <div class="vote">
                        <div ng-click="Question.vote({id: item.id, vote: 1 })" class="up">赞 [: item.upvote_count :]</div>
                        <div ng-click="Question.vote({id: item.id, vote: 2 })" class="down">踩 [: item.downvote_count :]</div>
                    </div>
                    <div class="item-content">
                        <div class="info">
                            <div class="user-info">[: item.user.username :]</div>
                            <div class="desc">[: item.user.intro || '用户信息暂无描述' :]</div>
                        </div>
                        <div class="answer-content">[: item.content :]</div>
                        <div class="action-set">
                            <span ng-click="item.show_block=!item.show_block"><span ng-if="item.show_block">取消</span>评论</span>
                            <span ng-if="item.user_id === self_id">
                                 <a ng-click ="Answer.answer_form = item" href="">编辑</a>
                                 <a ng-click ="Answer.delete(item.id)" href="">删除</a>
                            </span>
                            <span ui-sref="question.detail({id: item.question_id, answer_id: item.id})"
                                 class="time">[: item.updated_at :]</span>
                        </div>
                    </div>
                </div>
                <comment-block ng-if="item.show_block" answer_id="item.id"></comment-block>
                <div class="hr"></div>
            </div>
            <form name="answer_form" ng-submit="Answer.add_or_update(Question.data.id)" class="answer-form">
                <div class="input-group">
                    <textarea name="content" class="i-data" rows="5"
                              ng-model="Answer.answer_form.content"
                              required></textarea>
                </div>
                <div class="input-group">
                    <button type="submit" class="primary"
                        ng-disabled="answer_form.$invalid">提交</button>
                </div>
            </form>
        </div>
    </div>
</div>