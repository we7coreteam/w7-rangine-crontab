<?php

namespace W7\Crontab\Scheduler;

use W7\Crontab\Task\Task;

class LoopScheduler extends SchedulerAbstract {
	public function schedule(){
		itimeTick(1000, function () {
			$time = time();
			$tasks = $this->taskManager->all();

			/**
			 * @var Task $task
			 */
			foreach ($tasks as $task) {
				if ($task->check($time)) {
					$tasks[$task->getName()] = $task;
					$this->scheduleTask($task);
				}
			}
		});
	}
}