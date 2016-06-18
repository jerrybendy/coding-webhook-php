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
    ->on(Webhook::MESSAGE_TYPE_PUSH, function ($data) {

        if ($data->ref === 'refs/heads/master') {
            exec('git pull');
        }

    })
    ->onTokenFail(function(Webhook_Token_Error_Exception $e, $data) {
        /*
         * 函数在 token 验证失败时被触发
         *
         * 接收两个参数
         *      $e   Exception 的信息
         *      $data  接收到的 post 信息
         */
        echo $e->getMessage(), "\n";

        var_dump($data);

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

        } else {
            echo $e->getMessage();
        }

        echo "\n";

        var_dump($data);

    })
    ->run();
