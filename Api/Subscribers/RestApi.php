<?php

namespace Adeira\Api\Subscribers;

use Nette\Application\Application;
use Nette\Application\Request;

class RestApi
{

	private $errorPresenter;

	private $enableForModules;

	/**
	 * @var \Nette\Http\Session | \Kdyby\FakeSession\Session
	 */
	private $session;

	public function __construct($errorPresenter, $enableForModules, \Nette\Http\Session $session)
	{
		$this->errorPresenter = $errorPresenter;
		$this->enableForModules = $enableForModules;
		$this->session = $session;
	}

	/**
	 * Nette\Application\Application::onRequest
	 */
	public function __invoke(Application $app, Request $request)
	{
		$module = substr($request->getPresenterName(), 0, strpos($request->getPresenterName(), ':'));
		if (in_array($module, $this->enableForModules, TRUE)) {

			if (class_exists(\Tracy\Debugger::class)) {
				\Tracy\Debugger::$productionMode = TRUE; // enforce
			}
			$app->catchExceptions = TRUE; // always
			$app->errorPresenter = $this->errorPresenter;

			try { // disable session
				$this->session->disableNative();
			} catch (\Exception $exc) {
				if (class_exists(\Tracy\Debugger::class)) {
					\Tracy\Debugger::log($exc->getMessage(), 'error.session-storage');
				} else {
					throw $exc;
				}
			}
		}
	}

}
