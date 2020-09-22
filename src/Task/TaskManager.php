<?php

namespace W7\Crontab\Task;

class TaskManager{
	private $tasks = [];

	public function __construct($config) {
		foreach ($config as $name => $task) {
			$this->add($name, $task);
		}
	}

	public function add($name, $config){
		$this->tasks[$name] = new Task($name, $config);
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