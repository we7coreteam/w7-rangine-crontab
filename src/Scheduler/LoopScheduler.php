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

			/**
			 * @var Task $task
			 */
			foreach ($this->taskManager->all() as $task) {
				//获取该任务的触发器，触发任务
				if ($task->getTrigger()->trigger($time)) {
					$this->scheduleTask($task);
				}
			}
		});
	}
}
