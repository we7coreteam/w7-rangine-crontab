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

class TaskManager {
	protected $tasks = [];

	public function add(Task $task) {
		$this->tasks[$task->getName()] = $task;
	}

	public function rm($name) {
		unset($this->tasks[$name]);
	}

	public function count() {
		return count($this->tasks);
	}

	public function runTask($name) {
		$this->tasks[$name]->run = true;
	}

	public function finishTask($name) {
		$this->tasks[$name]->run = false;
	}

	public function all() {
		return $this->tasks;
	}
}
