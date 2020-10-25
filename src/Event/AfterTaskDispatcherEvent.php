<?php

namespace W7\Crontab\Event;

use W7\Crontab\Task\CronTask;

class AfterTaskDispatcherEvent {
	/**
	 * @var CronTask
	 */
	public $cronTask;

	/**
	 * @var \Throwable
	 */
	public $throwable;


	public function __construct(CronTask $cronTask, \Throwable $throwable = null) {
		$this->cronTask = $cronTask;
		$this->throwable = $throwable;
	}
}