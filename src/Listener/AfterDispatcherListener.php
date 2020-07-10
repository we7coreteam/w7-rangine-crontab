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

use W7\Core\Facades\Logger;
use W7\Core\Listener\ListenerAbstract;
use W7\Crontab\Event\AfterDispatcherEvent;

class AfterDispatcherListener extends ListenerAbstract {
	public function run(...$params) {
		/**
		 * @var AfterDispatcherEvent $event
		 */
		$event = $params[0];
		$this->log($event);
	}

	private function log(AfterDispatcherEvent $event) {
		if (!$event->throwable) {
			Logger::channel('crontab')->debug('push crontab task ' . $event->task->getName() . ' success with data ' . $event->task->getTaskMessage()->pack());
		} else {
			Logger::channel('crontab')->debug('push crontab task ' . $event->task->getName() . ' fail with data ' . $event->task->getTaskMessage()->pack() . ' with error ' . $event->throwable->getMessage());
		}
	}
}
