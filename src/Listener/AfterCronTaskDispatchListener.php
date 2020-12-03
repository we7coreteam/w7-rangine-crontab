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
use W7\Crontab\Event\AfterCronTaskDispatchEvent;

class AfterCronTaskDispatchListener extends ListenerAbstract {
	public function run(...$params) {
		/**
		 * @var AfterCronTaskDispatchEvent $event
		 */
		$event = $params[0];
		$this->log($event);
	}

	private function log(AfterCronTaskDispatchEvent $event) {
		if (!$event->throwable) {
			$this->getLogger()->channel('crontab')->debug('push crontab task ' . $event->cronTask->getName() . ' success with data ' . $event->cronTask->getTaskMessage()->pack());
		} else {
			$this->getLogger()->channel('crontab')->debug('push crontab task ' . $event->cronTask->getName() . ' fail with data ' . $event->cronTask->getTaskMessage()->pack() . ' with error ' . $event->throwable->getMessage());
		}
	}
}
