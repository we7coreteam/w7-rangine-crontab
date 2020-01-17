<?php

namespace W7\Crontab\Event;

use W7\Crontab\Task\Task;

class BeforeDispatcherEvent {
	/**
	 * @var Task $task
	 */
	public $task;

	public function __construct(Task $task) {
		$this->task = $task;
	}
}