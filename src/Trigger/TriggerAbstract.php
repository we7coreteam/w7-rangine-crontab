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

use W7\Crontab\Task\CronTask;

abstract class TriggerAbstract {
	protected $cronTask;

	public function __construct(CronTask $cronTask) {
		$this->cronTask = $cronTask;
	}

	abstract public function trigger(int $time) : bool;
}
