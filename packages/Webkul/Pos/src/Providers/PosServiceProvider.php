<?php

namespace Webkul\Pos\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Webkul\Pos\Http\Middleware\RedirectIfNotPosUser;
use Illuminate\Foundation\AliasLoader;
use Webkul\Pos\Facades\Pos as PosFacade;
use Webkul\Core\Tree;

/**
 * PosServiceProvider ServiceProvider
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        include __DIR__ . '/../Http/helpers.php';

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->loadRoutesFrom(__DIR__ . '/../Http/admin-routes.php');

        $this->loadRoutesFrom(__DIR__.'/../Http/routes.php');

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'pos');

        $this->publishes([
            __DIR__ . '/../../publishable/assets' => public_path('vendor/webkul/pos/assets'),
        ], 'public');

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'pos');
        
        $router->aliasMiddleware('posuser', RedirectIfNotPosUser::class);
        
        $this->composeView();
    }

    /**
     * Bind the the data to the views
     *
     * @return void
     */
    protected function composeView()
    {
        view()->composer('pos::shop.layouts.nav-left', function ($view) {
            $tree = Tree::create();

            foreach (config('menu.posuser') as $item) {
                $item['route'] = 'pos.user.session.index';

                $tree->add($item, 'menu');
            }

            $tree->items = core()->sortItems($tree->items);

            $view->with('pos_menu', $tree);
        });
    }
    
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(EventServiceProvider::class);
        
        $this->app->register(ModuleServiceProvider::class);

        $this->registerConfig();

        $this->registerFacades();
    }

    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/system.php', 'core'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/admin-menu.php', 'menu.admin'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/front-menu.php', 'menu.posuser'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/acl.php', 'acl'
        );
    }

    /**
     * Register Bouncer as a singleton.
     *
     * @return void
     */
    protected function registerFacades()
    {
        //to make the pos facade and bind the
        //alias to the class needed to be called.
        $loader = AliasLoader::getInstance();
        $loader->alias('pos', PosFacade::class);
        
        $this->app->singleton('pos', function () {
            return new Pos();
        });

        $this->app->bind('pos', 'Webkul\Pos\Pos');
    }
}
