{{-- 主页面 --}}
<div ng-controller="HomeController" class="home container">
    <div class="card">
        <h2>最新动态</h2>
        <div class="item-set">
            {{-- item in Timeline.data track by $index 可以遍历重复的字段 --}}
            <div ng-repeat="item in Timeline.data" class="feed item clearfix">
                <div class="hr"></div>
                <div ng-if="item.question_id" class="vote">
                    <div ng-click="Timeline.vote({id: item.id, vote: 1 })" class="up">赞 [: item.upvote_count :]</div>
                    <div ng-click="Timeline.vote({id: item.id, vote: 2 })" class="down">踩 [: item.downvote_count :]</div>
                </div>
                <div class="item-content">
                    <div ng-if="item.question_id" class="content-act">[: item.user.username :] 回答问题</div>
                    <div ng-if="!item.question_id" class="content-act">[: item.user.username :] 提出了问题</div>
                    <div ng-if="item.question_id" ui-sref="question.detail({id: item.question_id})">[: item.question.title :]</div>
                    <div ui-sref="question.detail({id: item.id})" class="title">[: item.title :]</div>
                    <div class="content-owner">
                        [: item.user.username :]
                        <span ng-if="item.user.intro" class="desc">，[: item.user.intro :]</span>
                    </div>
                    <div class="content-main">
                        [: item.content :]
                    </div>
                    <div class="action-set">
                        <div ng-if="!item.question_id" class="time">[: item.updated_at :]</div>
                        <div ng-if="item.question_id" ui-sref="question.detail({id: item.question_id, answer_id: item.id})"
                             class="time">[: item.updated_at :]</div>
                        <div class="comment">175 条评论</div>
                    </div>
                </div>
            </div>
            <div ng-if="Timeline.pending" class="tac">数据加载中...</div>
            <div ng-if="Timeline.no_more_data" class="tac">没有更多数据了</div>
        </div>
    </div>
</div>