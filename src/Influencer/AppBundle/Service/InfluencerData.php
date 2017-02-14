<?php 
namespace Influencer\AppBundle\Service;

use Influencer\AppBundle\Service\AbstractData;
use GuzzleHttp;

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
	
	public function getKloutScore($kloutId)
	{
		$client = $this->getClient();
		try {
			$res = $client->get('http://api.klout.com/v2/user.json/'.$kloutId, [
				'query' => [
					'key' => $this->getParameter('klout_key')
				]
			]);
			if ($res->getStatusCode() == 200) {
				return json_decode($res->getBody(), true);
			}
		} catch(\Exception $e) {
			//var_dump($e->getMessage());die();
		}
	}
	
	public function getFacebookStats($id)
	{
		return false;
	}
	
	public function getGoogleStats($id)
	{
		$em = $this->getEntityManager();
		$user = $em->getRepository('InfluencerAppBundle:User')->find($id);
		if ($user) {
			$google = $user->getGoogle();
			if ($google) {
				$token = $google->getToken();
				$feeds = $em->getRepository('InfluencerAppBundle:Feed')->loadSavedFeedsFor($id, 'google');
				$ids = [];
				foreach ($feeds as $item) {
					$_id = $item->getInternalId();
					if (!in_array($_id, $ids)) {
						$ids[] = $_id;
					}
				}
				if (sizeof($ids) > 0) {
					try {
						$client = $this->getClient();
						$response = $client->request('GET', 'https://www.googleapis.com/youtube/v3/videos', [
							'headers' => [
								'Authorization' => 'Bearer ' . $token
							],
							'query' => [
								'part' => 'statistics',
								'chart' => 'mostPopular'
								//'id' => implode(',', $ids),
								//'key' => $this->container->getParameter('google_id'),
							]
						]);
						$response = json_decode($response->getBody());
						if (isset($response->items)) {
							$viewsCount = 0;
							$commentsCount = 0;
							$likesCount = 0;
							foreach ($response->items as $item) {
								$viewsCount += intval($item->statistics->viewCount);
								$commentsCount += intval($item->statistics->commentCount);
								$likesCount += intval($item->statistics->likeCount);
							}
							$data = [
								'viewCount' => $viewsCount,
								'commentCount' => $commentsCount,
								'likeCount' => $likesCount,
							];
							return $data;
						} else {
							return false;
						}
					} catch(\Exception $e) {
						var_dump($e->getMessage());
						return false;
					}
				}
			}
		}
		return false;
	}
	
	public function getTwitterStats($id)
	{
		$em = $this->getEntityManager();
		$user = $em->getRepository('InfluencerAppBundle:User')->find($id);
		if ($user) {
			$twitter = $user->getTwitter();
			if ($twitter) {
				$username = $twitter->getUserName();
				try {
					$client = $this->getClient();
					$response = $client->get('https://cdn.syndication.twimg.com/widgets/followbutton/info.json', [
						'query' => [
							'screen_names' => $username,
						]
					]);
					$response = json_decode($response->getBody());
					if (isset($response[0])) {
						$data = [
							'followersCount' => $response[0]->followers_count,
						];
						return $data;
					} else {
						return 0;
					}
				} catch(\Exception $e) {
					var_dump($e->getMessage());
				}
			}
		}
		return 0;
	}
	
	public function getInstagramStats($id)
	{
		$em = $this->getEntityManager();
		$user = $em->getRepository('InfluencerAppBundle:User')->find($id);
		if ($user) {
			$instagram = $user->getInstagram();
			if ($instagram) {
				$token = $instagram->getToken();
				try {
					$client = $this->getClient();
					$response = $client->get('https://api.instagram.com/v1/users/'.$instagram->getUserId(), [
						'query' => [
							'access_token' => $token,
						]
					]);
					$response = json_decode($response->getBody());
					if (isset($response->data)) {
						return $response->data->counts;
					}
					return 0;
				} catch(\Exception $e) {
					var_dump($e->getMessage());
				}
			}
		}
		return 0;
	}
}