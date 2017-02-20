{{-- 注册页面模板 --}}
<div class="signup container" ng-controller="SignupController">
    <div class="card">
        <h2>注册</h2>
        {{--[: User.signup_data :]--}}
        <form name="signup_form" ng-submit="User.signup()">
            <div class="input-group">
                <label for="username">用户名：</label>
                {{-- ng-model-options为设置项，debounce: 500，500毫秒请求一次 --}}
                <input type="text" id="username" name="username" class="i-data"
                       ng-minlength="4" ng-maxlength="24"
                       ng-model="User.signup_data.username"
                       ng-model-options="{ debounce: 500 }"
                       required>
                <div ng-if="signup_form.username.$touched" class="input-error-set">
                    <div ng-if="signup_form.username.$error.required">用户名为必填项</div>
                    <div ng-if="signup_form.username.$error.minlength">用户名长度为4-24之间</div>
                    <div ng-if="User.signup_username_exists">用户名已存在</div>
                </div>
            </div>
            <div class="input-group">
                <label for="password">密码：</label>
                <input type="password" id="password" name="password" class="i-data"
                       ng-minlength="6" ng-maxlength="255"
                       ng-model="User.signup_data.password" required>
                <div ng-if="signup_form.password.$touched" class="input-error-set">
                    <div ng-if="signup_form.password.$error.required">密码为必填项</div>
                </div>
            </div>
            <div class="input-group">
                <button type="submit" class="primary"
                        ng-disabled="signup_form.$invalid">注册</button>
            </div>
        </form>
    </div>
</div>