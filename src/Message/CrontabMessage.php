<?php
/**
 * @author donknap
 * @date 18-11-26 下午7:47
 */

namespace W7\Crontab\Message;

use W7\Core\Message\TaskMessage;

/**
 * 计划任务消息包
 */
class CrontabMessage extends TaskMessage {
	public $messageType = 'crontab';

	public $name = '';
}