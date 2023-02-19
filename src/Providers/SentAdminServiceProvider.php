<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace Sent\Providers;

use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class SentAdminServiceProvider  extends ServiceProvider{

	/**
	 * boot
	 *
	 * @return void
	 * @throws NotFoundExceptionInterface
	 * @throws ContainerExceptionInterface
	 */
	public function boot(): void{
		$this->registerEvents();
	}

	/**
	 * register
	 *
	 * @return void
	 * @throws ReflectionException
	 */
	public function register(): void{
	}

	/**
	 * register events
	 *
	 * @return void
	 */
	protected function registerEvents(): void{
		Event::listen(RequestHandled::class, \Sent\Listeners\RequestHandledListener::class);
	}
}
