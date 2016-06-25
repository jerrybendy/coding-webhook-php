<?php
/**
 * Created by PhpStorm.
 * User: jerry
 * Date: 16/6/13
 * Time: 11:37
 */


/**
 * 演示在 on 函数中通过使用一个数组达到同时绑定多个事件到同一个处理函数的功能,
 * 以及多次调用 on 函数以绑定不同的事件
 */

require '../vendor/autoload.php';

use Jerrybendy\Coding\Webhook;

/*
 * 在这里定义你的 token , 可以为空
 */
define('TOKEN', 'hello-world');



$webHook = new Webhook(TOKEN);

$webHook
    ->on([
        Webhook::EVENT_TYPE_PUSH,
        Webhook::EVENT_TYPE_TEST,
        Webhook::EVENT_TYPE_MR,
    ], function ($data) {

        if ($data->ref === 'refs/heads/master') {
            exec('git pull');
        }

    })
    ->on(Webhook::EVENT_TYPE_TOPIC, function ($data) {

        var_dump($data);

    })
    ->on(Webhook::EVENT_TYPE_MEMBER, function ($data) {

        var_dump($data);

    })

    ->run();
