<?php 
namespace Influencer\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CampaignRepository extends EntityRepository
{
	public function getStatisticsForUser($user)
	{
		$em = $this->getEntityManager();
		$statistics = [];
		$dql = 'SELECT COUNT(c) AS num ';
		$dql .= 'FROM InfluencerAppBundle:Campaign c LEFT JOIN InfluencerAppBundle:User u WITH u.id = c.client ';
		$dql .= 'WHERE u.username IS NOT NULL AND :user MEMBER OF c.influencers AND c.status = :status';
		foreach (['new', 'finished', 'pending', 'live', 'rejected'] as $status) {
			$res = $em->createQuery($dql)
				->setParameters([
					'user' => $user,
					'status' => $status
				])
				->getSingleResult();
			$statistics[$status] = $res['num'];
		}
		return $statistics;
	}
	
	public function getCampaignsRelatedToUser($user, $status = null)
	{
		$em = $this->getEntityManager();
		$campaigns = [];
		$dql = 'SELECT c.id,c.name,c.budget,c.status,c.deadline,u.firstName,u.lastName ';
		$dql .= 'FROM InfluencerAppBundle:Campaign c LEFT JOIN InfluencerAppBundle:User u WITH u.id = c.client ';
		$dql .= 'WHERE u.username IS NOT NULL AND :user MEMBER OF c.influencers ';
		$params = ['user' => $user];
		if ($status && $status != 'all') {
			$dql .= 'AND c.status = :status ';
			$params['status'] = $status;
		}
		$data = $em->createQuery($dql)
			->setParameters($params)
			->getArrayResult();
		
		foreach ($data as $item) {
			$campaigns[] = [
				'name' => $item['name'],
				'client' => implode(' ', [$item['firstName'], $item['lastName']]),
				'budget' => $item['budget'],
				'status' => $item['status'],
				'deadline' => $item['deadline']->format('d M Y'),
			];
		}
		
		return $campaigns;
	}
}