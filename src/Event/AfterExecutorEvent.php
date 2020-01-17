<?php

namespace W7\Crontab\Event;

use W7\Core\Message\Message;
use W7\Crontab\Message\CrontabMessage;

class AfterExecutorEvent {
	/**
	 * @var CrontabMessage $taskMessage
	 */
	public $taskMessage;

	/**
	 * @var \Throwable $throwable
	 */
	public $throwable;

	public function __construct(string $taskMessage, \Throwable $throwable = null) {
		$this->taskMessage = Message::unpack($taskMessage);
		$this->throwable = $throwable;
	}
}