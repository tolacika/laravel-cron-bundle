<?php


namespace Tolacika\CronBundle\Http\Middleware;


use Tolacika\CronBundle\CronBundle;

class Authenticate
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        return CronBundle::check($request) ? $next($request) : abort(403, "You don't have permission for this feature.");
    }
}
