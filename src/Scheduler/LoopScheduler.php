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

namespace W7\Crontab\Scheduler;

use W7\Crontab\Task\Task;

class LoopScheduler extends SchedulerAbstract {
	public function schedule() {
		itimeTick(1000, function () {
			$time = time();
			$tasks = $this->taskManager->all();

			/**
			 * @var Task $task
			 */
			foreach ($tasks as $task) {
				if ($task->check($time)) {
					$this->scheduleTask($task);
				}
			}
		});
	}
}
