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
use W7\Crontab\Event\AfterCronTaskExecutorEvent;

class AfterCronTaskExecutorListener extends ListenerAbstract {
	public function run(...$params) {
		/**
		 * @var AfterCronTaskExecutorEvent $event
		 */
		$event = $params[0];
		$this->log($event);
	}

	public function log(AfterCronTaskExecutorEvent $event) {
		if (!$event->throwable) {
			$this->getLogger()->channel('crontab')->debug('exec crontab task ' . $event->taskMessage->params['name'] . ' success with data ' . $event->taskMessage->pack());
		} else {
			$this->getLogger()->channel('crontab')->debug('exec crontab task ' . $event->taskMessage->params['name'] . ' fail with data ' . $event->taskMessage->pack() . ' with error ' . $event->throwable->getMessage());
		}
	}
}
