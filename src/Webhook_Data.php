<?php
/**
 * Created by PhpStorm.
 * User: jerry
 * Date: 16/6/13
 * Time: 10:12
 */

namespace Jerrybendy\Coding;

/**
 * Class Webhook_Data
 *
 * @package Jerrybendy\Coding\Webhook
 *
 * @property string      $token
 * @property string      $event
 * @property object|null $ref
 * @property object|null $after
 * @property object|null $before
 * @property object|null $repository
 * @property object|null $commit
 * @property object|null $author
 * @property object|null $target_user
 * @property string      $action
 * @property object|null $topic
 * @property object|null $document
 * @property string      $type
 * @property object|null $merge_request
 * @property object|null $pull_request
 */
class Webhook_Data
{
    public function __construct($obj)
    {
        foreach($obj as $k => $v){
            $this->{$k} = $v;
        }
    }

}