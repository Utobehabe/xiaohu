
<div ng-controller="UserController">
    <div class="card user container">
        <h2>用户详情</h2>
        <div class="basic">
            <div class="info-item clearfix">
                <div>username</div>
                <div>[: User.self_data.username :]</div>
            </div>
            <div class="info-item clearfix">
                <div>intro</div>
                <div>[: User.self_data.intro || '暂无介绍' :]</div>
            </div>
        </div>
        <div class="hr"></div>
        <h3>用户提问</h3>
        <div ng-repeat="item in User.his_questions" class="question-item-set">
            <div class="item">
                [: item.title :]
            </div>
        </div>

        <h3>用户回答</h3>
        <div ng-repeat="item in User.his_answers" class="answer-item-set">
            <div class="item">
                <div class="title">[: item.question.title :]</div>
                <div class="hr"></div>
                <div class="comment">
                    <div class="comment-content">[: item.content :]</div>
                    <div class="time">更新时间：[: item.question.updated_at :]</div>
                </div>
            </div>
        </div>
    </div>
</div>