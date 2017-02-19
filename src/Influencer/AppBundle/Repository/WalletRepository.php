<?php 
namespace Influencer\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class WalletRepository extends EntityRepository
{
	public function getAllUserPayments($user)
	{
		$em = $this->getEntityManager();
		$dql = 'SELECT w FROM InfluencerAppBundle:Wallet w ';
		$dql .= 'LEFT JOIN InfluencerAppBundle:Payment p WITH p.wallet = w.id ';
		$dql .= 'LEFT JOIN InfluencerAppBundle:Campaign c WITH p.campaign = c.id ';
		$dql .= 'WHERE w.user = :user';
		$params = ['user' => $user];
		$payments = $em->createQuery($dql)->setParameters($params)->getArrayResult();
		
		return $payments;
	}
}