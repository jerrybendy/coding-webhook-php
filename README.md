[![Latest Stable Version](https://poser.pugx.org/jerrybendy/coding-webhook-php/version)](https://packagist.org/packages/jerrybendy/coding-webhook-php)
[![Total Downloads](https://poser.pugx.org/jerrybendy/coding-webhook-php/downloads)](https://packagist.org/packages/jerrybendy/coding-webhook-php)
[![Latest Unstable Version](https://poser.pugx.org/jerrybendy/coding-webhook-php/v/unstable)](//packagist.org/packages/jerrybendy/coding-webhook-php)
[![License](https://poser.pugx.org/jerrybendy/coding-webhook-php/license)](https://packagist.org/packages/jerrybendy/coding-webhook-php)

[Coding.net](https://coding.net) 是目前中国用户量最大的 Git 代码托管平台，并为开发者提供了很多方便易用的功能。

这个项目主要用于通过 Coding 的 WebHook 功能实现自动化测试、自动化部署的功能，还可以使用 WebHook 提供的其它如 `topic`、`document` 等类型实现更多的定制化内容。

设置项目启用 WebHook 功能请参考官方帮助文档：[https://coding.net/help/doc/git/webhook.html](https://coding.net/help/doc/git/webhook.html)

## 安装方法
推荐使用 `composer` 方式安装，要求 PHP 版本不低于 v5.4：

```bash
composer require jerrybendy/coding-webhook-php
```

## 使用方法
Coding-webhook-php 使用类似于 javascript 注册事件的方式，需要给监听的事件注册一个回调函数，并在回调函数内处理具体的动作（如 `git pull`）。

```php
require '../vendor/autoload.php';

use Jerrybendy\Coding\Webhook;

/*
 * 在这里定义你的 token , 可以为空
 */
define('TOKEN', 'hello-world');

$webHook = new Webhook(TOKEN);

$webHook
    ->on(Webhook::EVENT_TYPE_PUSH, function ($data) {

        if ($data->ref === 'refs/heads/master') {
            exec('git pull');
        }
    })
    ->run();  // 最后一定要调用一次 run() 函数
```

在上面的例子中，定义了一个 `$webHook` 对象，并使用 `on` 方法“监听”类型为 `EVENT_TYPE_PUSH` 的事件，并在接收到此类事件时执行 `function`（闭包）里面的内容。例子中是判断如果推送的分支是 `master` 就执行 `git pull` 的操作。

（更多示例代码请查看 `example` 目录）

## APIs
`Webhook` 类除构造函数外共开放三个函数：

### 构造函数
构造函数接收可选的 `token` 为唯一参数，`token` 需在 coding 的 webhook 页面中设置，并保持一致即可。

### on()
> on(string|array $type, callable $callback)

`on()` 方法用于绑定一个或多个事件处理函数到具体的事件类型中。

一个事件可以绑定多个处理函数，同样也可以把多个事件绑定到同一个处理函数中。

`on()` 有两个参数，`$type` 为要绑定处理函数的类型，可以是字符串或者以 `EVENT_TYPE_` 开头的一个预定义常量，也可以是包含多个类型常量的数组。当 `$type` 为数组时，将会对数组内的所有事件类型绑定相同的处理函数。

`$callback` 参数接收一个 `callable` 类型的回调函数（关于 callable 的更多信息可参见 [PHP 官网文档](http://php.net/manual/en/language.types.callable.php)）。也就是说 `$callback` 的值可以是一个表示函数名的字符串、包含对象和方法名的一维数组、使用双冒号语法表示的类 static 方法，或者是一个闭包（个人推荐使用闭包的方式）。

回调函数需要接收一个对象类型的参数，参数包含了和此次事件相关的所有信息。关于信息中可能包含的数据内容，请参考[官方文档](https://open.coding.net/webhook.html#webhook)。

函数返回 Webhook 对象本身，可用于链式操作。

### onFail()
> onFail(callable $callback）

`onFail()` 方法用于处理一些常规的错误信息，如请求头错误、解析错误、token 不符等。

和 `on()` 方法一样， `onFail()` 也接收一个 `callable` 类型的回调函数作为参数。

回调函数需要接收两个参数。第一个为 `\Exception` 类型的参数，参数包含了和异常相关的一些信息。可以使用 `instanceof` 关键字来判断异常的类型。第二个参数包含了错误发生时当前的一些数据信息，如 header 错误或读取 post 原文出错时信息为空字符串，解析错误时为 post 原文的内容，token 错误时为已经解析后的 post 信息。（参考 [02-exception.php](example/02-exception.php)）

函数返回 Webhook 对象本身，可用于链式操作。

### run()
> run()

在配置完所有需要的回调后必须调用一次 `run()` 方法以处理具体的事件。

### 常量定义
```php
const EVENT_TYPE_TEST     = 'ping';  // webHook 页面点击测试时发出的请求
const EVENT_TYPE_PUSH     = 'push';  // 推送事件
const EVENT_TYPE_TOPIC    = 'topic';  // 讨论相关事件
const EVENT_TYPE_MEMBER   = 'member';  // 用户相关事件
const EVENT_TYPE_TASK     = 'task';   // 任务操作事件
const EVENT_TYPE_DOCUMENT = 'document';  // 文档操作事件
const EVENT_TYPE_WATCH    = 'watch';   // 仓库被关注/取消关注时的事件
const EVENT_TYPE_STAR     = 'star';   // 仓库被收藏/取消收藏时的事件
const EVENT_TYPE_PR       = 'pull_request';  // 暂不知道在哪里会触发
const EVENT_TYPE_MR       = 'merge_request';  // 发起MR与合并MR时的事件
```


## LICENSE
The MIT License (MIT)

Copyright (c) 2016 Jerry Bendy

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
