<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace Sent\Middleware;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Throwable;

class AuthMiddleware{

	public function handle(Request $request, \Closure $next, ...$guards){
		try {
			if (! $user = Auth::guard($guards)->user()) {
				throw new AuthenticationException();
			}
			return $next($request);
		} catch (Exception|Throwable $e) {
			return response()->json(['code' => 2000, 'message' => '请重新登录！']);
		}
	}
}
