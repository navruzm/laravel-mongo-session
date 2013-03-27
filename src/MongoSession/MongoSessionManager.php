<?php namespace MongoSession;

use Illuminate\Session\SessionManager;

class MongoSessionManager extends SessionManager {

	/**
	 * Create an instance of the database session driver.
	 *
	 * @return MongoSession\MongoStore
	 */
	protected function createMongoDriver()
	{
		$connection = $this->getMongoConnection();

		$collection = $this->app['config']['session.table'];

		return new MongoStore($connection, $this->app['encrypter'], $collection);
	}

	/**
	 * Get the database connection for the database driver.
	 *
	 * @return LMongo\Database
	 */
	protected function getMongoConnection()
	{
		$connection = $this->app['config']['session.connection'];

		return $this->app['lmongo']->connection($connection);
	}

}