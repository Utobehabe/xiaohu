{{-- 添加问题模板 --}}
<div class="question_add container" ng-controller="QuestionAddController">
    <div class="card">
        <form ng-submit="Question.go()" name="question_add_form">
            <div class="input-group">
                <label for="title">问题标题</label>
                <input type="text" id="title" name="title" class="i-data"
                       ng-model="Question.new_question.title"
                       ng-minlength="5" ng-maxlength="255"
                       required>
            </div>
            <div class="input-group">
                <label for="title">问题描述</label>
                <textarea id="desc" name="desc" class="i-data"
                          ng-model="Question.new_question.desc"></textarea>
            </div>
            <div class="input-group">
                <button type="submit" class="primary"
                        ng-disabled="question_add_form.title.$error.required ||
                        question_add_form.title.$error.minlength ||
                        question_add_form.title.$error.maxlength">提交</button>
            </div>
        </form>
    </div>
</div>