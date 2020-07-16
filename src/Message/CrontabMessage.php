<?php

/**
 * Rangine crontab server
 *
 * (c) We7Team 2019 <https://www.rangine.com>
 *
 * document http://s.w7.cc/index.php?c=wiki&do=view&id=317&list=2284
 *
 * visited https://www.rangine.com for more details
 */

namespace W7\Crontab\Message;

use W7\Core\Message\TaskMessage;

/**
 * 计划任务消息包
 */
class CrontabMessage extends TaskMessage {
	const CRONTAB_MESSAGE = 'crontab';

	public $messageType = self::CRONTAB_MESSAGE;

	public $name = '';
}
