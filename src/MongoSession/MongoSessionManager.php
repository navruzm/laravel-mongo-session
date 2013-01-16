<?php namespace MongoSession;

use Illuminate\Session\SessionManager;

class MongoSessionManager extends SessionManager {

	/**
	 * Create an instance of the database session driver.
	 *
	 * @return Illuminate\Session\DatabaseStore
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
	 * @return Illuminate\Database\Connection
	 */
	protected function getMongoConnection()
	{
		$connection = $this->app['config']['session.connection'];

		return $this->app['mongo']->connection($connection);
	}

}