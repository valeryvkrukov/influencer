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
					$ids[] = $item->getInternalId();
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
								'id' => implode(',', $ids),
								'key' => $this->container->getParameter('google_id'),
							]
						]);
						$response = json_decode($response->getBody());
						if (isset($response->items)) {
							$count = 0;
							foreach ($response->items as $item) {
								$count += intval($item->statistics->viewCount);
							}
							return $count;
						} else {
							return 0;
						}
					} catch(\Exception $e) {
						var_dump($e->getMessage());
					}
				}
			}
		}
		return 0;
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
						return $response[0]->followers_count;
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
					if (isset($response->data) && is_array($response->data)) {
						return $response->data->counts->followed_by;
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