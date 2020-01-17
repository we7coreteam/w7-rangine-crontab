<?php

namespace W7\Crontab\Listener;

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
			ilogger()->channel('crontab')->debug('push crontab task ' . $event->task->getName() . ' success with data ' . $event->task->getTaskMessage()->pack());
		} else {
			ilogger()->channel('crontab')->debug('push crontab task ' . $event->task->getName() . ' fail with data ' . $event->task->getTaskMessage()->pack() . ' with error ' . $event->throwable->getMessage());
		}
	}
}