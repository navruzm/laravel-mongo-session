<?php

use Mockery as m;
use MongoSession\MongoStore;

class SessionMongoStoreTest extends PHPUnit_Framework_TestCase {

	public function tearDown()
	{
		m::close();
	}


	public function testRetrieveSessionReturnsDecryptedPayload()
	{
		$store = $this->getStore();
		$store->getConnection()->collection = m::mock('StdClass');
		$store->getConnection()->collection->shouldReceive('findOne')->once()->with(array('id' => 1))->andReturn(array('payload' => 'bar'));
		$store->getEncrypter()->shouldReceive('decrypt')->once()->with('bar')->andReturn('decrypted.bar');

		$this->assertEquals('decrypted.bar', $store->retrieveSession(1));
	}


	public function testRetrieveSessionReturnsNullWhenSessionNotFound()
	{
		$store = $this->getStore();
		$store->getConnection()->collection = m::mock('StdClass');
		$store->getConnection()->collection->shouldReceive('findOne')->once()->with(array('id' => 1))->andReturn(null);
		$store->getEncrypter()->shouldReceive('decrypt')->never();

		$this->assertNull($store->retrieveSession(1));
	}


	public function testCreateSessionStoresEncryptedPayload()
	{
		$store = $this->getStore();
		$store->getConnection()->collection = m::mock('StdClass');
		$store->getConnection()->collection->shouldReceive('insert')->once()->with(array('id' => 1, 'payload' => array('encrypted.session'), 'last_activity' => new MongoDate(100)));
		$store->getEncrypter()->shouldReceive('encrypt')->once()->with(array('session', 'last_activity' => 100))->andReturn(array('encrypted.session'));

		$store->createSession(1, array('session', 'last_activity' => 100), new Symfony\Component\HttpFoundation\Response);
	}


	public function testUpdateSessionStoresEncryptedPayload()
	{
		$store = $this->getStore();
		$store->getConnection()->collection = m::mock('StdClass');
		$store->getConnection()->collection->shouldReceive('update')->once()->with(array('id' => 1), array('$set' => array('payload' => array('encrypted.session'), 'last_activity' => new MongoDate(100))));
		$store->getEncrypter()->shouldReceive('encrypt')->once()->with(array('session', 'last_activity' => 100))->andReturn(array('encrypted.session'));

		$store->updateSession(1, array('session', 'last_activity' => 100), new Symfony\Component\HttpFoundation\Response);
	}


	public function testSweepRemovesExpiredSessions()
	{
		$store = $this->getStore();
		$store->getConnection()->collection = m::mock('StdClass');
		$store->getConnection()->collection->shouldReceive('remove')->once()->with(array('last_activity' => array('$lt' => new MongoDate(100))));

		$store->sweep(100);
	}


	protected function getStore()
	{
		return new MongoStore(m::mock('LMongo\DatabaseManager'), m::mock('Illuminate\Encryption\Encrypter'), 'collection');
	}

}