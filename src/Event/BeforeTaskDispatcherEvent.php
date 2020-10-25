<?php

namespace W7\Crontab\Event;

use W7\Crontab\Task\CronTask;

class BeforeTaskDispatcherEvent {
	/**
	 * @var CronTask
	 */
	public $cronTask;

	public function __construct(CronTask $cronTask) {
		$this->cronTask = $cronTask;
	}
}