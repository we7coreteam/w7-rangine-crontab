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

abstract class StrategyAbstract {
	protected $minWorkerId;

	public function __construct($minWorkerId) {
		$this->minWorkerId = $minWorkerId;
	}

	abstract public function dispatch(Server $server, CrontabMessage $crontabMessage) : bool;
}
