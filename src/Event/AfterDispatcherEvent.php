<?php

namespace W7\Crontab\Event;

use W7\Crontab\Task\Task;

class AfterDispatcherEvent {
	/**
	 * @var Task $task
	 */
	public $task;

	/**
	 * @var \Throwable $throwable
	 */
	public $throwable;


	public function __construct(Task $task, \Throwable $throwable = null) {
		$this->task = $task;
		$this->throwable = $throwable;
	}
}