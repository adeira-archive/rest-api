<?php

namespace Adeira\Api;

use Tracy\Debugger;

class ErrorPresenter extends BasePresenter //TODO
{

	/**
	 * @param \Exception $exception
	 */
	public function actionDefault($exception)
	{
		if ($exception instanceof \Nette\Application\BadRequestException) {
			$this->payload->error = [
				'message' => 'This endpoint does not exist.',
			];
		} else {
			Debugger::log($exception, Debugger::ERROR);
			$this->payload->error = [
				'message' => Debugger::$productionMode ? 'Internal Server Error' : $exception->getMessage(),
			];
		}
	}

	/**
	 * @param \Exception $exception
	 */
	public function renderDefault($exception)
	{
		$this->payload->status = 'error';
		$this->sendResponse(new JsonResponsePretty($this->payload));
	}

}
