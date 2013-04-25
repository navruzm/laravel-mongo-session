<?php namespace MongoSession;

use Illuminate\Session\SessionServiceProvider as ServiceProvider;

class SessionServiceProvider extends ServiceProvider {

	/**
	 * Register the session manager instance.
	 *
	 * @return void
	 */
	protected function registerSessionManager()
	{
		$this->app['session.manager'] = $this->app->share(function($app)
		{
			return new MongoSessionManager($app);
		});
	}

}