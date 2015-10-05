<?php namespace Lanin\Laravel\Hashids;

use Hashids\Hashids;
use Illuminate\Support\ServiceProvider;

class HashidsServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap application service.
	 */
	public function boot()
	{
		$this->publishes([
			__DIR__.'/../config/hashids.php' => config_path('hashids.php'),
		]);

		$salt = config('hashids.salt') !== '' ? config('hashids.salt') : env('APP_KEY');
		$this->app->make('hashids', [$salt, config('hashids.length'),  config('hashids.alphabet')]);

		\Blade::directive('hashids', function($expression) {
			return "<?php echo app('hashids')->encode($expression); ?>";
		});
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerRouter();
		$this->registerUrlGenerator();
		$this->registerHashids();
		$this->registerConfigs();
	}

	/**
	 * Register the router instance.
	 *
	 * @return void
	 */
	protected function registerRouter()
	{
		$this->app['router'] = $this->app->share(function ($app) {
			return new Router($app['events'], $app);
		});
	}

	/**
	 * Register Hashids.
	 */
	protected function registerHashids()
	{
		$this->app->singleton('hashids', Hashids::class);
	}

	/**
	 * Register new UrlGenerator.
	 */
	protected function registerUrlGenerator()
	{
		$this->app['url'] = $this->app->share(function ($app) {
			$routes = $app['router']->getRoutes();

			// The URL generator needs the route collection that exists on the router.
			// Keep in mind this is an object, so we're passing by references here
			// and all the registered routes will be available to the generator.
			$app->instance('routes', $routes);

			$url = new UrlGenerator(
				$routes, $app->rebinding(
					'request', $this->requestRebinder()
				)
			);

			$url->setSessionResolver(function () {
				return $this->app['session'];
			});

			// If the route collection is "rebound", for example, when the routes stay
			// cached for the application, we will need to rebind the routes on the
			// URL generator instance so it has the latest version of the routes.
			$app->rebinding('routes', function ($app, $routes) {
				$app['url']->setRoutes($routes);
			});

			return $url;
		});
	}

	/**
	 * Get the URL generator request rebinder.
	 *
	 * @return \Closure
	 */
	protected function requestRebinder()
	{
		return function ($app, $request) {
			$app['url']->setRequest($request);
		};
	}

	/**
	 * Register default configs.
	 */
	protected function registerConfigs()
	{
		$this->mergeConfigFrom(
			__DIR__.'/../config/hashids.php', 'hashids'
		);
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return [
			'hashids',
			'router',
			'url',
		];
	}
}