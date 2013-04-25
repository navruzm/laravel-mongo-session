<?php namespace MongoSession;

use Illuminate\Session\SessionManager;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\MongoDbSessionHandler;

class MongoSessionManager extends SessionManager {

	/**
	 * Create an instance of the database session driver.
	 *
	 * @return \Illuminate\Session\Store
	 */
	protected function createMongoDriver()
	{
		$connection = $this->getMongoDbConnection();

		$collection = $this->app['config']['session.table'];

		$database = (string) $connection->getMongoDB();

		return $this->buildSession(new MongoDbSessionHandler($connection->getMongoClient(), $this->getMongoDbOptions($database, $collection)));
	}

	/**
	 * Get the database connection for the MongoDB driver.
	 *
	 * @return LMongo\Connection
	 */
	protected function getMongoDbConnection()
	{
		$connection = $this->app['config']['session.connection'];

		return $this->app['lmongo']->connection($connection);
	}

	/**
	 * Get the database session options.
	 *
	 * @return array
	 */
	protected function getMongoDbOptions($database, $collection)
	{
		return array('database' => $database,'collection' => $collection, 'id_field' => '_id', 'data_field' => 'payload', 'time_field' => 'last_activity');
	}

}