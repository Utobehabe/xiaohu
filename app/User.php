<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

//header('Content-type: application/json; charset=utf-8');
class User extends Model
{
    public function signUp() {
        //dd(Request::all()); // 调试使用的输出语句
        /* 判断用户名、密码是否为空 */
        if(!$this->has_username_password()) {
            return err('username and password are null');
        }
        // 如果用户名和密码不为空，则获取到用户名密码数据
        $username = $this->has_username_password()['username'];
        $password = $this->has_username_password()['password'];

        /* 判断用户名是否存在 */
        $user_exists = $this
            ->where('username', $username)
            ->exists();

        if ($user_exists) {
            return err('username has exists');
        }

        /* 对密码进行加密 */
        // 使用Hash::make()方法，或bcrypt()方法加密
        $hash_pwd = bcrypt($password);

        /* 保存到数据库中 */
        $this->username = $username;
        $this->password = $hash_pwd;
        return $this->save()?
            suc(['id'=> $this->id]):
            err('db save error');
    }

    public function login() {
        // 检查用户名和密码是否存在
        if (!$this->has_username_password()) {
            return err('username and password are null');
        }
        $username = $this->has_username_password()['username'];
        $password = $this->has_username_password()['password'];
        // 检查用户名是否正确
        $user = $this->where('username', $username)
            ->first();
        if (!$user) {
            return err('username has exists');
        }

        // 检查密码是否正确['status'=>0, 'msg'=>'密码不正确'];
        if(!Hash::check($password, $user->password)) {
            return err('invalid password');
        }

        // 将用户信息写入session中
        session()->put('user_name', $user->username);
        session()->put('user_id', $user->id);

        return suc(['id'=>$user->id]);
    }

    public function has_username_password() {
        $username = get_requestDate('username');
        $password = get_requestDate('password');
        if (!($username && $password)) {
            return false;
        }else {
            return ['username'=>$username, 'password'=>$password];
        }
    }

    // 登出
    public function logout() {
        // 清除session
        // 使用设置session()->put('user_name', null)
        // 使用session()->pull('user_name')
        // 使用session()->forget('user_name')
        session()->forget('user_name');
        session()->forget('user_id');

        return suc();
    }

    // 检测用户是否登录
    public function isLog() {
        return isLog();
    }

    // 读取用户数据
    public function read() {
        $id = get_requestDate('id');
        if (!$id) {
            return err('id is required');
        }

        if ($id === 'self') {
            if (!session('user_id')) {
                return err('login required');
            }
            $id = session('user_id');
        }

        $get = ['username', 'avatar_url', 'intro'];
        $user = $this->find($id, $get);   // 根据字段返回集合
        $data = $user->toArray();
        $answer_count = answer_ins()->where('user_id', $id)->count();
        $question_count = question_ins()->where('user_id', $id)->count();
        $data['answer_count'] = $answer_count;  //回答次数
        $data['question_count'] = $question_count;  // 提问次数

        return suc(['data'=>$data]);
    }


    // 修改密码
    public function changePassword() {
        if (!$this->isLog()) {
            return err('login required');
        }
        // 新密码和老密码都必须填写
        $new_password = get_requestDate('new_password');
        $old_password = get_requestDate('old_password');
        if (!$new_password || !$old_password) {
            return err('new_password and old_password are required');
        }
        $user = $this->find(session('user_id'));
        // 老密码解密
        if (!Hash::check($old_password, $user->password)) {
            return err('invalid old_password');
        }
        // 新密码加密
        $user->password = bcrypt($new_password);
        return $user->save()?
            suc():
            err('db save error');
    }

    /* 找回密码 */
    public function reset_password() {
        if($this->is_robot()) {
            return err('max frequency reached');
        }

        // 判断phone字段是否存在
        if (!get_requestDate('phone')) {
            return err('phone is required');
        }
        // 判断用户是否存在
        $user = $this->where('phone', get_requestDate('phone'))->first();

        if (!$user) {
            return err('invalid phone number');
        }
        /* 生成随机数，并加入到数据库中 */
        $captcha = $this->generate_captcha();
        $user->phone_captcha = $captcha;

        if ($user->save()) {
            $this->send_sms();  // 通过第三方发送信息已经验证信息
            $this->update_robot_time();  //为下次机器人调用检查做准备
            return suc();
        }
        return err('db update failed ');
    }

    /* 验证找回密码api */
    public function validate_reset_password() {
        if($this->is_robot(2)) {
            return err('max frequency reached');
        }

        if (!(get_requestDate('phone') &&
            get_requestDate('phone_captcha') &&
            get_requestDate('new_password'))) {
            return err('phone, phone_captcha and new_password are required');
        }

        /* 检查用户是否存在 */
        $user = $this->where([
            'phone' => get_requestDate('phone'),
            'phone_captcha' => get_requestDate('phone_captcha')
        ])->first();

        if(!$user) {
            return err('invalid phone or invalid phone_captcha');
        }

        $user->password = bcrypt(get_requestDate('new_password'));
        $this->update_robot_time();
        return $user->save()?
            suc():
            err('db update failed');
    }
    /* 检查是否是机器人 */
    public function is_robot($time =10) {
        /* 如果session没被调用过，说明接口从没被调用过 */
        if (!session('last_active_time')) {
            return false;
        }
        
        $current_time = time();
        $last_active_time = session('last_active_time');

        return !($current_time - $last_active_time > $time);
    }

    /* 更新最后一个操作的时间 */
    public function update_robot_time() {
        session()->set('last_active_time', time());
        // session('last_active_time', time()) 是如果没有last_active_time值，则返回默认值time()
    }

    public function send_sms() {
        return true;
    }

    /* 生成随机数 */
    public function generate_captcha() {
        return rand(1000, 9999);
    }

    /* 添加多对多数据 */
    public function answers() {
        return $this
            ->belongsToMany('App\Answer') // 多对多
            ->withPivot('vote') // 这个为自定义主键？
            ->withTimestamps(); // 这个字段随数据更新，更新时间
    }

    public function exists() {
        return suc(['count' => $this->where(get_requestDate())->count()]);
    }
}
