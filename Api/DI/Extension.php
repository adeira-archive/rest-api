<?php

namespace Adeira\Api\DI;

class Extension extends \Adeira\CompilerExtension
{

	private $defaults = [
		'errorPresenter' => 'Api:Error',
		'enableForModules' => [
			'Api',
		],
		'presenterMapping' => [
			'Api' => 'App\ApiModule\Presenters\*Presenter',
		],
	];

	public function loadConfiguration()
	{
		$config = $this->validateConfig($this->defaults);
		$builder = $this->getContainerBuilder();

		$restApiSubscriber = $builder
			->addDefinition($this->prefix('restApiSubscriber'))
			->setClass(\Adeira\Api\Subscribers\RestApi::class, [
				$config['errorPresenter'],
				$config['enableForModules'],
			]);

		$application = $builder->getDefinition('application.application');
		$application->addSetup('?->onRequest[] = ?', [
			'@self',
			$restApiSubscriber,
		]);
	}

	public function beforeCompile()
	{
		$config = $this->getConfig();
		$presenterMapping = $config['presenterMapping'];
		if (!!$presenterMapping) {
			$this->setMapping($config['presenterMapping']);
		}
	}

}
