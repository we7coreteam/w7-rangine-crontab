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

namespace W7\Crontab\Task;

class CronTaskManager {
	protected $cronTasks = [];

	public function add(CronTask $task) {
		$this->cronTasks[$task->getName()] = $task;
	}

	public function rm($name) {
		unset($this->cronTasks[$name]);
	}

	public function count() {
		return count($this->cronTasks);
	}

	public function runTask($name) {
		$this->cronTasks[$name]->run = true;
	}

	public function finishTask($name) {
		$this->cronTasks[$name]->run = false;
	}

	public function all() {
		return $this->cronTasks;
	}
}
