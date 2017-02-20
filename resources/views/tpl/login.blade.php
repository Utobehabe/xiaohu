{{-- 登录页面模板 --}}
<div class="login container" ng-controller="LoginController">
    <div class="card">
        <h2>登录</h2>
        {{--[: User.login_data :]--}}
        <form name="login_form" ng-submit="User.login()" role="form">
            <div class="input-group">
                <label for="username">用户名：</label>
                <input type="text" name="username" id="username" class="i-data"
                       ng-model="User.login_data.username" required>
            </div>
            <div class="input-group">
                <label for="password">密码：</label>
                <input type="password" name="password" id="password" class="i-data"
                       ng-model="User.login_data.password" required>
            </div>
            <div ng-if="User.login_fail" class="input-error-set">
                用户名或密码有误
            </div>
            <div class="input-group">
                <button type="submit" class="primary"
                        ng-disabled="login_form.username.$error.required ||
                    login_form.password.$error.required">登录</button>
            </div>
        </form>
    </div>
</div>
