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

use W7\App;
use W7\Tcp\Server\Server as TcpServer;

class Server extends TcpServer {
	private static $dispatcherWorkerId;
	private static $maxExecuteWorkerId;

	public static $aloneServer = true;

	public function __construct() {
		if (!$setting = $this->getConfig()->get($this->getType() . '.setting')) {
			throw new \RuntimeException(sprintf('缺少服务配置 %s,请在config/crontab.php中添加setting配置项', $this->getType()));
		}
		$this->getConfig()->set('server.' . $this->getType(), $setting);
		parent::__construct();
	}

	public function getType() {
		return 'crontab';
	}

	protected function checkSetting() {
		parent::checkSetting();

		$tasks = $this->getConfig()->get('crontab.task', []);
		foreach ($tasks as $name => $task) {
			if (empty($task['class'])) {
				throw new \RuntimeException('task ' . $name . ' config error : class, please check the configuration in config/crontab.php');
			}
			if (empty($task['rule'])) {
				throw new \RuntimeException('task ' . $name . ' config error : rule, please check the configuration in config/crontab.php');
			}
		}

		//派发任务的进程加用户配置的执行任务进程数量
		$this->setting['worker_num'] += 1;
		self::$dispatcherWorkerId = 0;
		self::$maxExecuteWorkerId = $this->setting['worker_num'] - 1;
	}

	public function listener(\Swoole\Server $server) {
		if ($server->port != $this->setting['port']) {
			$this->server = $server->addListener($this->setting['host'], $this->setting['port'], $this->setting['sock_type']);
			//tcp需要强制关闭其它协议支持，否则继续父服务
			$this->server->set([
				'open_http2_protocol' => false,
				'open_http_protocol' => false,
				'open_websocket_protocol' => false,
			]);
		} else {
			$this->server = $server;
		}

		self::$dispatcherWorkerId = App::$server->setting['worker_num'];
		App::$server->setting['worker_num'] += $this->setting['worker_num'];
		self::$maxExecuteWorkerId = App::$server->setting['worker_num'] - 1;

		$this->registerService();
	}

	public static function getDispatcherWorkerId() {
		return self::$dispatcherWorkerId;
	}

	public static function getMaxExecuteWorkerId() {
		return self::$maxExecuteWorkerId;
	}
}
