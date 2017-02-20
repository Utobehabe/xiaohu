# 晓乎后端API文档 （v1.0.0）
## 常规API调用原则
- 调用API的方式：`domain.com/part1/part2`
	- part1 为model
	- part2 为方法
- 特殊API
	- timeline（时间栈）：调用方式`domain.com/api/timeline`
	- test（测试用户是否登录）：调用方式`domain.com/test`

- CRUD
	- 每个模型都有增删改查的方法，`add`, `remove`, `alert`, `read`

## Model
### 字段解释
#### User
- `id`：用户id
- `username`：用户名
- `password`：密码
- `old_password`：修改的老密码
- `new_password`：修改的新密码
- `phone`：手机号码
- `phone_captcha`：验证码


#### Question
- `id`：问题id
- `title`：标题
- `desc`：描述
- `page`：页数
- `limit`：每页限制的数量

#### Answer
- `id`：回答id
- `question_id`：问题id
- `content`：内容
- `vote`：是否投票

#### comment
- `id`：评论id
- `content`：内容
- `question_id`：问题id
- `answer_id`：回答id
- `reply_to`：依赖的评论id



