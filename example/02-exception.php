<?php
/**
 * Created by PhpStorm.
 * User: jerry
 * Date: 16/6/13
 * Time: 11:19
 */


/**
 * 演示如何使用基本的错误处理
 * 如 token 错误、返回内容无法解析等
 */

require '../vendor/autoload.php';

use Jerrybendy\Coding\Webhook;
use Jerrybendy\Coding\Webhook_Header_Error_Exception;
use Jerrybendy\Coding\Webhook_Post_Content_Error_Exception;
use Jerrybendy\Coding\Webhook_Post_Parse_Error_Exception;
use Jerrybendy\Coding\Webhook_Token_Error_Exception;

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
    ->onFail(function(\Exception $e, $data) {
        /*
         * 函数在出现异常时被触发
         *
         * 参数 $e 包含了此次错误相关的部分信息, 可以根据不同的错误类型
         * 做出相应的处理
         */
        if ($e instanceof Webhook_Header_Error_Exception) {
            echo $e->getMessage();

        } elseif ($e instanceof Webhook_Post_Content_Error_Exception) {
            echo $e->getMessage();

        } elseif ($e instanceof Webhook_Post_Parse_Error_Exception) {
            echo $e->getMessage();

        } elseif ($e instanceof Webhook_Token_Error_Exception) {
            echo $e->getMessage();

        } else {
            echo $e->getMessage();
        }

        echo "\n";

        var_dump($data);

    })
    ->run();
