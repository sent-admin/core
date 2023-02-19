<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace Sent\Listeners;

use Illuminate\Foundation\Http\Events\RequestHandled;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Illuminate\Http\JsonResponse;

class RequestHandledListener{
	/**
	 * Handle the event.
	 *
	 * @param RequestHandled $event
	 * @return void
	 */
	public function handle(RequestHandled $event): void{
		$response = $event->response;

		if ($response instanceof JsonResponse) {
			$exception = $response->exception;

			if ($response->getStatusCode() == SymfonyResponse::HTTP_OK && ! $exception) {
				$response->setData($this->formatData($response->getData()));
			}
		}
	}

	/**
	 * @param mixed $data
	 * @return array
	 */
	protected function formatData(mixed $responseData): array{
		$result = [];
		$data = isset($responseData->data) ? $responseData->data : '';

		if (is_object($data) && property_exists($data, 'per_page')
			&& property_exists($data, 'total')
			&& property_exists($data, 'current_page')) {
			$result['data'] = $data->data;
			$result['total'] = $data->total;
			$result['limit'] = $data->per_page;
			$result['page'] = $data->current_page;
		}else{
			$result = [
				'code' => isset($responseData->code) ? $responseData->code : 0,
				'message' => isset($responseData->message) ? $responseData->message : '',
				'data' => isset($responseData->data) ? $responseData->data : '',
			];
		}

		return $result;
	}
}
