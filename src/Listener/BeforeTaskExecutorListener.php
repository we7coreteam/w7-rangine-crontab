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

namespace W7\Crontab\Listener;

use W7\Core\Listener\ListenerAbstract;
use W7\Core\Task\Event\BeforeTaskExecutorEvent;

class BeforeTaskExecutorListener extends ListenerAbstract {
	public function run(...$params) {
		/**
		 * @var BeforeTaskExecutorEvent $event
		 */
		$event = $params[0];
		$this->log($event);
	}

	private function log(BeforeTaskExecutorEvent $event) {
		$this->getLogger()->channel('crontab')->debug('exec crontab task ' . $event->taskMessage->params['name'] . ' with data ' . $event->taskMessage->pack());
	}
}
