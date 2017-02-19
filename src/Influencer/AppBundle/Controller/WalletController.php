<?php
namespace Influencer\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Influencer\AppBundle\Controller\BaseController;

/**
 * @Route("/user")
 */
class WalletController extends BaseController
{
	/**
	 * @Route("/wallet", name="inf_wallet_get_info", options={"expose"=true})
	 */
	public function getUserWalletDataAction(Request $request)
	{
		$user = $this->getUser();
		if ($user) {
			$em = $this->getDoctrine()->getManager();
			$dql = 'SELECT w.id, SUM(p1.amount) AS balance, SUM(p2.amount) AS escrow, SUM(p3.amount) AS payout ';
			$dql .= 'FROM InfluencerAppBundle:Wallet w ';
			$dql .= 'LEFT JOIN InfluencerAppBundle:Payment p1 WITH p1.wallet = w.id ';
			$dql .= 'LEFT JOIN InfluencerAppBundle:Payment p2 WITH p2.wallet = w.id ';
			$dql .= 'LEFT JOIN InfluencerAppBundle:Payment p3 WITH p3.wallet = w.id ';
			$dql .= 'WHERE p1.type = :balance AND p2.type = :escrow AND p3.type = :payout AND w.user = :user ';
			$dql .= 'GROUP BY w.id';
			$params = ['balance' => 'balance', 'escrow' => 'escrow', 'payout' => 'payout', 'user' => $user];
			$data = $em->createQuery($dql)->setParameters($params)->getArrayResult();
			if ($data[0]) {
				$data = $data[0];
			} else {
				$data = ['balance' => 0, 'escrow' => 0, 'payout' => 0];
			}
			return new JsonResponse($data);
		}
	}
	
	/**
	 * @Route("/payments/{type}", name="inf_wallet_get_payments", options={"expose"=true})
	 */
	public function getUserPaymentsAction(Request $request, $type)
	{
		$user = $this->getUser();
		if ($user) {
			$em = $this->getDoctrine()->getManager();
			//$payments = $em->getRepository('InfluencerAppBundle:Wallet')->getAllUserPayments($user);
			$params = ['user' => $user];
			$dql = 'SELECT w.id AS wallet_id,p.id AS payment_id,p.amount,p.type,p.direction,p.status,p.paidAt,c.id AS campaign_id,c.name AS campaign_name,c.status AS campaign_status,c.budget AS campaign_budget ';
			$dql .= 'FROM InfluencerAppBundle:Wallet w ';
			$dql .= 'LEFT JOIN InfluencerAppBundle:Payment p WITH p.wallet = w.id ';
			$dql .= 'LEFT JOIN InfluencerAppBundle:Campaign c WITH p.campaign = c.id ';
			$dql .= 'WHERE w.user = :user ';
			if ($type == 'payments') {
				$dql .= 'AND p.type = :type';
				$params['type'] = 'balance';
			} elseif ($type == 'payouts') {
				$dql .= 'AND p.type = :type';
				$params['type'] = 'payout';
			} elseif ($type == 'escrow') {
				$dql .= 'AND p.type = :type';
				$params['type'] = 'escrow';
			}
			$payments = $em->createQuery($dql)->setParameters($params)->getArrayResult();
			return new JsonResponse($payments);
		}
	}
}