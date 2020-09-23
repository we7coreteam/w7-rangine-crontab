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

namespace W7\Crontab\Trigger;

use W7\Crontab\Task\Task;

abstract class TriggerAbstract {
	protected $task;

	public function __construct(Task $task) {
		$this->task = $task;
	}

	abstract public function trigger(int $time) : bool;
}
