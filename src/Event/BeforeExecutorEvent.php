<?php

namespace W7\Crontab\Event;

use W7\Core\Message\Message;
use W7\Crontab\Message\CrontabMessage;

class BeforeExecutorEvent {
	/**
	 * @var CrontabMessage $taskMessage
	 */
	public $taskMessage;

	public function __construct(string $taskMessage) {
		$this->taskMessage = Message::unpack($taskMessage);
	}
}