<?php namespace MongoSession;

use LMongo\DatabaseManager;
use Illuminate\Encryption\Encrypter;
use Illuminate\Session\Store;
use Illuminate\Session\Sweeper;
use Symfony\Component\HttpFoundation\Response;

class MongoStore extends Store implements Sweeper {

	/**
	 * The database connection instance.
	 *
	 * @var \LMongo\DatabaseManager
	 */
	protected $connection;

	/**
	 * The encrypter instance.
	 *
	 * @var Illuminate\Encrypter
	 */
	protected $encrypter;

	/**
	 * The session collection name.
	 *
	 * @var string
	 */
	protected $collection;

	/**
	 * Create a new database session store.
	 *
	 * @param  LMongo\DatabaseManager  $connection
	 * @param  Illuminate\Encrypter  $encrypter
	 * @param  string  $collection
	 * @return void
	 */
	public function __construct(DatabaseManager $connection, Encrypter $encrypter, $collection)
	{
		$this->collection = $collection;
		$this->encrypter = $encrypter;
		$this->connection = $connection;
	}

	/**
	 * Retrieve a session payload from storage.
	 *
	 * @param  string  $id
	 * @return array|null
	 */
	public function retrieveSession($id)
	{
		$session = $this->collection()->findOne(array('id' => $id));

		if ( ! is_null($session))
		{
			return $this->encrypter->decrypt($session['payload']);
		}
	}

	/**
	 * Create a new session in storage.
	 *
	 * @param  string  $id
	 * @param  array   $session
	 * @param  Symfony\Component\HttpFoundation\Response  $response
	 * @return void
	 */
	public function createSession($id, array $session, Response $response)
	{
		$payload = $this->encrypter->encrypt($session);

		$last_activity = new \MongoDate($session['last_activity']);

		$this->collection()->insert(compact('id', 'payload', 'last_activity'));
	}

	/**
	 * Update an existing session in storage.
	 *
	 * @param  string  $id
	 * @param  array   $session
	 * @param  Symfony\Component\HttpFoundation\Response  $response
	 * @return void
	 */
	public function updateSession($id, array $session, Response $response)
	{
		$payload = $this->encrypter->encrypt($session);

		$last_activity = new \MongoDate($session['last_activity']);

		$update_data = array('payload' => $payload, 'last_activity' => $last_activity);

		$this->collection()->update(array('id' => $id), array('$set' => $update_data));
	}

	/**
	 * Remove session records older than a given expiration.
	 *
	 * @param  int   $expiration
	 * @return void
	 */
	public function sweep($expiration)
	{
        $this->collection()->remove(array('last_activity' => array('$lt' => new \MongoDate($expiration))));
	}

	/**
	 * Get a MongoCollection instance.
	 *
	 * @return \MongoCollection
	 */
	protected function collection()
	{
		return $this->connection->{$this->collection};
	}

	/**
	 * Get the database connection instance.
	 *
	 * @return \LMongo\DatabaseManager
	 */
	public function getConnection()
	{
		return $this->connection;
	}

	/**
	 * Get the encrypter instance.
	 *
	 * @return Illuminate\Encrypter
	 */
	public function getEncrypter()
	{
		return $this->encrypter;
	}

}