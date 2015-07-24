<?php

namespace Todo\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Tymon\JWTAuth\JWTAuth;

//use Tymon\jwtAuthAuth\Exceptions\jwtAuthException;
//use Tymon\jwtAuthAuth\Exceptions\TokenExpiredException;

class SpirantJwtGetUserFromToken {

	protected $jwtAuth;

	public function __construct(JWTAuth $jwtAuth) {
		$this->jwtAuth = $jwtAuth;
	}
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, \Closure $next) {

		if (!$token = $request->input('token')) {
			//if (!$token = $this->jwtAuth->setRequest($request)->getToken()) {
			$status = Response::HTTP_BAD_REQUEST;
			return response()->json(['error' => 'Token Not provided', 'status' => $status], $status);
		}

		try {
			$user = $this->jwtAuth->authenticate($token);
		} catch (TokenExpiredException $e) {
			$status = Response::HTTP_BAD_REQUEST;
			return response()->json(['error' => 'Token Expired', 'status' => $status], $status);
			//return $this->respond('tymon.jwtAuth.expired', 'token_expired', $e->getStatusCode(), [$e]);
		} catch (jwtAuthException $e) {
			$status = Response::HTTP_BAD_REQUEST;
			return response()->json(['error' => 'Token Invalid', 'status' => $status], $status);
			//return $this->respond('tymon.jwtAuth.invalid', 'token_invalid', $e->getStatusCode(), [$e]);
		}

		if (!$user) {
			$status = Response::HTTP_NOT_FOUND;
			return response()->json(['error' => 'Token Invalid', 'status' => $status], $status);
			//return $this->respond('tymon.jwtAuth.user_not_found', 'user_not_found', 404);
		}

		//$this->events->fire('tymon.jwtAuth.valid', $user);

		return $next($request);
	}
}
