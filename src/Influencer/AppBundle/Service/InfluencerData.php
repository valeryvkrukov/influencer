<?php 
namespace Influencer\AppBundle\Service;

use Influencer\AppBundle\Service\AbstractData;

class InfluencerData extends AbstractData
{
	public function getDashboardData($id)
	{
		$em = $this->getEntityManager();
		$data = $em->getRepository('InfluencerAppBundle:Feed')->loadLatestFromFeedsFor($id, 'array');
		return $data;
	}
	
	public function getFeeds($id)
	{
		$em = $this->getEntityManager();
		$data = $em->getRepository('InfluencerAppBundle:Feed')->loadSavedFeedsFor($id, null, 'array');
		return $data;
	}
}