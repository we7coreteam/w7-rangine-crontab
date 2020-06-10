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

namespace W7\Crontab\Strategy;

use Swoole\Server;
use W7\Crontab\Message\CrontabMessage;

class WorkerStrategy extends StrategyAbstract {
	/**
	 * @var int
	 */
	protected $currentWorkerId;

	public function __construct($minWorkerId) {
		parent::__construct($minWorkerId);
		$this->currentWorkerId = $this->minWorkerId;
	}

	public function dispatch(Server $server, CrontabMessage $crontabMessage): bool {
		return $server->sendMessage($crontabMessage->pack(), $this->getNextWorkerId($server));
	}

	protected function getNextWorkerId(Server $server): int {
		++$this->currentWorkerId;
		$maxWorkerId = $server->setting['worker_num'] - 1;
		if ($this->currentWorkerId > $maxWorkerId) {
			$this->currentWorkerId = $this->minWorkerId + 1;
		}
		return $this->currentWorkerId;
	}
}
