<?php 
namespace Influencer\AppBundle\Service;

use GuzzleHttp\Client;

abstract class AbstractData
{
	protected $container;
	
	public function __construct($container)
	{
		$this->container = $container;
	}
	
	protected function getEntityManager()
	{
		return $this->container->get('doctrine.orm.entity_manager');
	}
	
	protected function getClient()
	{
		return new Client();
	}
	
	abstract public function getDashboardData($id);
}