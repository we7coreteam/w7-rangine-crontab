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

namespace W7\Crontab\Server;

use W7\Core\Process\ProcessServerAbstract;
use W7\Crontab\Process\DispatcherProcess;
use W7\Crontab\Process\ExecutorProcess;

class Server extends ProcessServerAbstract {
	public static $aloneServer = true;

	public function __construct() {
		$crontabConfig = iconfig()->getUserConfig($this->getType());
		$supportServers = iconfig()->getServer();
		$supportServers[$this->getType()] = $crontabConfig['setting'] ?? [];
		iconfig()->setUserConfig('server', $supportServers);

		parent::__construct();
	}

	public function getType() {
		return 'crontab';
	}

	protected function checkSetting() {
		parent::checkSetting();
		$tasks = \iconfig()->getUserConfig('crontab')['task'] ?? [];
		foreach ($tasks as $name => $task) {
			if (empty($task['class'])) {
				throw new \RuntimeException('task ' . $name . ' config error : class, please check the configuration in config/crontab.php');
			}
			if (empty($task['rule'])) {
				throw new \RuntimeException('task ' . $name . ' config error : rule, please check the configuration in config/crontab.php');
			}
		}

		$this->setting['ipc_type'] = SWOOLE_IPC_MSGQUEUE;
		$this->setting['message_queue_key'] =(int)($this->setting['message_queue_key'] ?? 0);
		$this->setting['message_queue_key'] = $this->setting['message_queue_key'] > 0 ? $this->setting['message_queue_key'] : irandom(6, true);
	}

	protected function register() {
		$this->pool->registerProcess('crontab_dispatch', DispatcherProcess::class, 1);
		$this->pool->registerProcess('crontab_executor', ExecutorProcess::class, $this->setting['worker_num']);
	}
}
