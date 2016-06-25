<?php
/**
 * Created by PhpStorm.
 * User: jerry
 * Date: 16/6/13
 * Time: 10:46
 */

/**
 * 演示基本的使用方法
 * 在接到 master 分支的推送时拉取最新的代码
 */

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
    ->run();
