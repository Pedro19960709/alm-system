<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserTypeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $iTypeUser = $request->user()->user_type_id;
        
        switch($iTypeUser)
        {
            case 1:
                return $next($request);
                break;
            case 2:
                return redirect()->to('/dashboard')->with('status', 'No tienes acceso a este apartado!');
                break;
            default:
                return redirect()->to('/dashboard')->with('status', 'No tienes acceso a este apartado!');
                break;
        }
    }
}
