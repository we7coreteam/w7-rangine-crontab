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

use W7\Crontab\Message\CrontabMessage;
use W7\Crontab\Trigger\CronTrigger;
use W7\Crontab\Trigger\TriggerAbstract;

class Task {
	private $name;
	private $config;
	/**
	 * @var TriggerAbstract
	 */
	protected $trigger;

	public function __construct($name, array $config) {
		$this->name = $name;
		$this->config = $config;
	}

	public function setConfig(array $config) {
		$this->config = $config;
	}

	public function getConfig() {
		return $this->config;
	}

	public function getName() {
		return $this->name;
	}

	public function getTask() {
		return $this->config['class'];
	}

	public function getRule() {
		return $this->config['rule'];
	}

	public function setTrigger(TriggerAbstract $trigger) {
		$this->trigger = $trigger;
	}

	public function getTrigger() {
		if ($this->trigger) {
			return $this->trigger;
		}

		$trigger = $this->config['trigger'] ?? CronTrigger::class;
		$this->trigger = new $trigger($this);
		return $this->trigger;
	}

	public function getTaskMessage() {
		$message = new CrontabMessage();
		$message->task = $this->getTask();
		$message->params['name'] = $this->getName();
		$message->params['config'] = $this->config;

		return $message;
	}
}
