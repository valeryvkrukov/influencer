<?php 
namespace Influencer\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CampaignRepository extends EntityRepository
{
	public function getStatisticsForUser($user, $period = null)
	{
		$em = $this->getEntityManager();
		$statistics = [
			'count' => [],
			'paymentsData' => [],
		];
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
			$statistics['count'][$status] = $res['num'];
		}
		$dql = 'SELECT w FROM InfluencerAppBundle:Wallet w WHERE w.user = :user';
		$res = $em->createQuery($dql)->setParameters(['user' => $user])->getSingleResult();
		if ($res) {
			$statistics['walletInfo'] = [
				'balance' => $res->getBalance(),
				'escrow' => $res->getEscrow(),
			];
		}
		$dql = 'SELECT c.status,c.budget,c.createdAt ';
		$dql .= 'FROM InfluencerAppBundle:Campaign c LEFT JOIN InfluencerAppBundle:User u WITH u.id = c.client ';
		$dql .= 'WHERE u.username IS NOT NULL AND :user MEMBER OF c.influencers ORDER BY c.createdAt ASC';
		$res = $em->createQuery($dql)->setParameters(['user' => $user])->getResult();
		if ($res) {
			$begin = new \DateTime('-1 month');
			$end = new \DateTime();
			$end = $end->modify('+1 month');
			$interval = new \DateInterval('P1D');
			$_period = new \DatePeriod($begin, $interval, $end);
			$data = [];
			foreach ($res as $item) {
				$data[$item['status']][$item['createdAt']->format('Y-m-d')][] = $item['budget'];/*[
					'x' => $item['createdAt']->getTimestamp(),
					'y' => $item['budget'],
				];*/
			}
			
			foreach ($data as $status => $item) {
				$values = [];
				foreach ($_period as $date) {
					$x = $date->getTimestamp();
					$y = 0;
					if (isset($data[$status][$date->format('Y-m-d')])) {
						$y = array_sum($data[$status][$date->format('Y-m-d')]);
					}
					$values[] = ['x' => $x, 'y' => $y];
				}
				$statistics['campaignsInfo'][] = [
					'data' => $values,
				];
			}
		}
		$dql = 'SELECT p ';
		$dql .= 'FROM InfluencerAppBundle:Wallet w ';
		$dql .= 'JOIN InfluencerAppBundle:Payment p WITH p.wallet = w.id ';
		$dql .= 'JOIN InfluencerAppBundle:User u WITH u.id = w.user ';
		$dql .= 'WHERE w.user = :user AND p.paidAt BETWEEN :begin AND :end ORDER BY p.paidAt DESC';
		switch($period) {
			case '1D':
				$bp = 'day';
				$mod = 'day';
				$key = 'Y-m-d@H';
				$per = '1H';
				break;
			case '1W':
				$bp = 'week';
				$mod = 'week';
				$key = 'Y-m-d';
				$per = '1D';
				break;
			case '1M':
				$bp = 'month';
				$mod = 'month';
				$key = 'Y-m-d';
				$per = '1D';
				break;
			case '1Y':
			default:
				$bp = 'year';
				$mod = 'month';
				$key = 'Y-m';
				$per = '1M';
		}
		$begin = new \DateTime('-1 '.$bp);
		$end = new \DateTime();
		$end = $end->modify('+1 '.$mod);
		$params = [
			'user' => $user, 
			'begin' => $begin,
			'end' => $end,
		];
		//var_dump($begin, $end);die();
		$res = $em->createQuery($dql)->setParameters($params)->getResult();
		if ($res) {
			$data = [];
			foreach ($res as $payment) {
				$type = $payment->getType();
				if (!isset($data[$type])) {
					$data[$type] = [];
				}
				$data[$type][$payment->getPaidAt()->format($key)][] = floatval($payment->getAmount());
			}
			
			$interval = new \DateInterval('P'.$per);
			$_period = new \DatePeriod($begin, $interval, $end);
			
			foreach ($data as $type => $val) {
				$values = [];
				foreach ($_period as $date) {
					$x = $date->getTimestamp();
					$y = 0;
					if (isset($data[$type][$date->format($key)])) {
						$y = array_sum($data[$type][$date->format($key)]);
					}
					$values[] = ['x' => $x, 'y' => $y];
				}
				//sort($values);
				$statistics['paymentsData'][] = [
					'key' => ucfirst($type),
					'values' => $values,
				];
			}
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