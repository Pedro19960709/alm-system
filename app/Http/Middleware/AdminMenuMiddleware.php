<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Symfony\Component\HttpFoundation\Response;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;

class AdminMenuMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $iUserType = $request->user()->user_type_id;

        switch($iUserType)
        {
            case 1:
                Event::listen(BuildingMenu::class, function (BuildingMenu $event) {
                    $event->menu->add([
                        'text' => 'Panel Principal',
                        'route' => 'home',
                        'icon' => 'fas fa-home'
                    ]);
        
                    $event->menu->add('CONFIGURACIÓN DE CUENTA');
                    $event->menu->add([
                        'text' => 'Usuarios',
                        'icon' => 'fas fa-fw fa-user',
                        'route' => 'getUserIndex',                        
                    ]);

                    $event->menu->add('CATALOGOS');
                    $event->menu->add([
                        'text' => 'Áreas',
                        'icon' => 'fas fa-shapes',
                        'route' => 'getAreaIndex',
                    ]);

                    $event->menu->add([
                        'text' => 'Departamentos',
                        'icon' => 'fas fa-building',
                        'route' => 'getDepartmentIndex',
                    ]);

                    $event->menu->add([
                        'text' => 'Artículos',
                        'icon' => 'fas fa-toolbox',
                        'route' => 'getArticlesIndex',
                    ]);

                    $event->menu->add('MODULOS PRINCIPALES');
                    $event->menu->add([
                        'text' => 'Peticiones',
                        'icon' => 'fas fa-shopping-cart',
                        'route' => 'getPetitionIndex',
                    ]);

                });
                return $next($request);
                break;
            case 2:
                Event::listen(BuildingMenu::class, function (BuildingMenu $event) {
                    $event->menu->add([
                        'text' => 'Panel Principal',
                        'route' => 'home',
                        'icon' => 'fas fa-home'
                    ]);

                    $event->menu->add([
                        'text' => 'Peticiones',
                        'icon' => 'fas fa-shopping-cart',
                        'route' => 'getPetitionIndex',
                    ]);
                });
                return $next($request);
                break;
        } 
    }
}
