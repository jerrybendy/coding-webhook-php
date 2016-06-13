<?php
/**
 * Created by PhpStorm.
 * User: jerry
 * Date: 16/6/12
 * Time: 20:29
 */

namespace Jerrybendy\Coding;


class Webhook
{


    /**
     * define some support message types
     * you'd better use these constants instead of string when
     * you bind message callback
     */
    const MESSAGE_TYPE_TEST     = 'ping';   // This is for test event
    const MESSAGE_TYPE_PUSH     = 'push';
    const MESSAGE_TYPE_TOPIC    = 'topic';
    const MESSAGE_TYPE_MEMBER   = 'member';
    const MESSAGE_TYPE_TASK     = 'task';
    const MESSAGE_TYPE_DOCUMENT = 'document';
    const MESSAGE_TYPE_WATCH    = 'watch';
    const MESSAGE_TYPE_STAR     = 'star';
    const MESSAGE_TYPE_PR       = 'pull_request';
    const MESSAGE_TYPE_MR       = 'merge_request';

    const MESSAGE_TYPE_FAIL       = 'fail';
    const MESSAGE_TYPE_TOKEN_FAIL = 'token_fail';


    /**
     * @var string
     */
    protected $token;


    /**
     * @var array
     */
    protected $callback_container = [];


    /**
     * Webhook constructor.
     *
     * @param string $token
     */
    public function __construct($token = '')
    {
        $this->token = $token;
    }


    /**
     * @param string|array   $messageType
     * @param callable $callback
     * @return $this
     */
    public function on($messageType, callable $callback)
    {
        if (is_array($messageType)) {
            foreach ($messageType as $mt) {
                $this->on($mt, $callback);
            }

            return $this;
        }
        
        if (isset($this->callback_container[$messageType]) && !empty($this->callback_container[$messageType])) {
            $this->callback_container [$messageType] [] = $callback;

        } else {
            $this->callback_container [$messageType] = [$callback];
        }

        return $this;
    }


    /**
     * A short method to bind fail callback
     *
     * @param callable $callback
     * @return $this
     */
    public function onFail(callable $callback)
    {
        return $this->on(self::MESSAGE_TYPE_FAIL, $callback);
    }


    /**
     * A short method to bind token fail callback,
     * it will called when token is not match $this->token
     *
     * @param callable $callback
     * @return $this
     */
    public function onTokenFail(callable $callback)
    {
        return $this->on(self::MESSAGE_TYPE_TOKEN_FAIL, $callback);
    }


    /**
     * Run the application and prepare to receive requests
     *
     */
    public function run()
    {
        /*
         * Check header and get the message type
         * if the message type is not registered, then return it with do nothing
         */
        $message_type = isset($_SERVER['HTTP_X_CODING_EVENT']) ? $_SERVER['HTTP_X_CODING_EVENT'] : false;

        if (!$message_type) {
            $this->_invokeMessageType(self::MESSAGE_TYPE_FAIL, [new Webhook_Header_Error_Exception('Cannot find event header')]);

            return;
        }

        if (!isset($this->callback_container[$message_type])) {
            return;
        }

        /*
         * Get and parse the post body
         */
        $post = trim(file_get_contents('php://input'));
        
        if ($post === false) {
            $this->_invokeMessageType(self::MESSAGE_TYPE_FAIL, [new Webhook_Post_Content_Error_Exception('Cannot read post content')]);

            return;
        }

        $post_parsed = json_decode($post);

        if ($post_parsed === null) {
            $this->_invokeMessageType(self::MESSAGE_TYPE_FAIL, [new Webhook_Post_Parse_Error_Exception('Cannot parse post content')]);

            return;
        }

        /*
         * Check token
         */
        if (! empty($this->token) && (! isset($post_parsed->token) || $post_parsed->token !== $this->token)) {
            $this->_invokeMessageType(self::MESSAGE_TYPE_TOKEN_FAIL, [new Webhook_Token_Error_Exception('Wrong token'), $post_parsed]);

            return;
        }

        $this->_invokeMessageType($message_type, [$post_parsed]);

    }


    /**
     * invoke all registered message if type matched
     *
     * @param string $type
     * @param array  $params
     */
    protected function _invokeMessageType($type, array $params)
    {
        $callbacks = isset($this->callback_container[$type]) ? $this->callback_container[$type] : [];

        foreach ($callbacks as $callback) {
            call_user_func_array($callback, $params);
        }
    }

}
