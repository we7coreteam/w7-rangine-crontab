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

namespace W7\Crontab\Event;

use W7\Core\Task\Event\AfterTaskExecutorEvent;
use W7\Crontab\Message\CrontabMessage;

class AfterCronTaskExecutorEvent extends AfterTaskExecutorEvent {
	/**
	 * @var CrontabMessage
	 */
	public $taskMessage;
}
