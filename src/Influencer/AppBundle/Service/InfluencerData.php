<?php 
namespace Influencer\AppBundle\Service;

use Influencer\AppBundle\Service\AbstractData;
use GuzzleHttp;
use Firebase\JWT\JWT;

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
				$userId = '111479814385038186244';
				$email = 'influencerapp@stakeholders-153612.iam.gserviceaccount.com';
				$key = "-----BEGIN PRIVATE KEY-----\nMIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCcdJSf6eoz5gbk\n/iguJ42HOdM55MOZxYgQQ0r9SI72dtvJilXl3C8SfcN7f29HUVpE02/d1I3z6p6w\n0uGIAn/fzl90mIv5JUmefyXiqaR1jRrAPcW2Z7B3YHa6OY5g9gINNUvifeJsGZy+\nUc3naAOjb0pw4TuyEGEqilAH4iDPrXOc7nCatzpkoeOEtUxCbmg+Vwx+ENntg8ww\n26xkXbjJqtIAqTFssGJyva5hcRt+VpfINX1iJTHUvCjYpHVtWGPvcpOpVNCSbshK\nyXdNuzZq1l2EZgcm5MePAO2dOOnAfHNTWLOIjukE1PbL8DTlOSkj0gyHIsAx6pa5\nmMFbgmYxAgMBAAECggEAD9PjbUIWxWVR8TydB/5MXQ/GhKbV+up2115XacQ7Yken\n+H2cLwLVaEN80TOVKFvci/Xv7TshmTl1EGJlGoNWOGgCVCNSlcPl7LjkNkf/MT0z\nJZuaMtfOjGUf3bsQ4lJA5uEraiBeFZ2Js99Gu1BUfeY2W7ENfvgVPF59ti50L04u\nU0VGFhmrKJ/iAzqWcJ1bUCil6XVJFS0eOxHImohX4kjcXW8Hf5+Mi8bQ7vbhTX/L\n9hJFiwwEvD6vf2jQG+5dxtg/FG2hVd3kecxV2JdES+CtATdomFnN+ZcoXUddX9Zj\nAFYL5+HisEILjcemjO3SpveFtzwn3ovILeFNJdM3oQKBgQDKOosz1cujY3C5fTZY\nezvBhmPAFLqTKkUaQIZjcRy8OtOPNkC9y41jUZeSi0jSdAmKIKya61PxyMURl7Mq\nXZlEsT6LJAanP6pj8/h6YnDN17zJUC1qtFVJxuqqFpYDP2sf5uUUQSL84awhPufh\n7wYW/561sYa4FBPdiqP3e4aPvQKBgQDGDk4uCyCEdZtS1pbnm38bRPbZHF/8YJ8E\naFb/HHSOfQzoMdXX14opjUnCV3BTX6tJ0U3mWgeMLh7y4I3YqbbeynFGgZI0nhdR\nblatfw5LG5iAl9M46gMByDKbZAzT2LobVKA/rXGJq5ZYm6ihPsa9oIaPpH/hF0nC\nni4MtUOthQKBgHHsuS7iam4JPx+/4jXGdst3Igq7or/1OCip4Qt0VXPU0AoFzkMd\nwPEKrhDoVoX4yMb/0T0VBZ1YNWzLwStEv2trToikKvtdamgUsQ6BFntf5gKjIHLU\nVJfTb6+SOcBqoba/JJ5cc5FysL2p6oflV9DtAEPrVrGs3eQYjnaFKDEhAoGBAMKR\nhS5okf5vBzM/SyqnMRyaCKNL4+QtC6sp0eV3j+33XbmyU7FitwGRItgATIIrpzuy\nmoPlCCsnmBHklpU2TUfbu6KXYgm9EY1XB2IUQvd4Wb5i6QetpVyXz1em1487B8IX\nxgIoozSrU90itmwKHvZlgCwXX+XCmhzOhtDHNoB9AoGBAK3Wdu0tPwaFTJH48who\nUnbY7XzYcasTaUfSxqQAeAsOQ8XdYoTfWufGumD8o4e+FqDX6rWOxoUionp2OTjy\nSNgi4tyETPwMi/hLlTVvCMoJh/mdqaqHz4Rju5RgD7tac6QkVU2WnSgpxhJwm+sv\nBlCA5kOs/pm5p7Q0DICVljin\n-----END PRIVATE KEY-----\n";
				
				$payload = [
					'iss' => $email,
					'sub' => $email,
					'scope' => 'https://www.googleapis.com/auth/youtube https://www.googleapis.com/auth/youtube.force-ssl https://www.googleapis.com/auth/youtube.readonly https://www.googleapis.com/auth/youtubepartner',
					'aud' => 'https://www.googleapis.com/oauth2/v4/token',
					'iat' => time(),
					'exp' => time() + 60 * 60,
					'uid' => $userId,
					'claims' => [
						'uid' => $userId,
					],
				];
				
				$token = JWT::encode($payload, $key, 'RS256');
				
				try {
					$client = $this->getClient();
					$response = $client->request('POST', 'https://www.googleapis.com/oauth2/v4/token', [
						'form_params' => [
							'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
							'assertion' => $token,
						]
					]);
					$response = json_decode($response->getBody());
					if (isset($response->access_token)) {
						try {
							$client = $this->getClient();
							$response = $client->request('GET', 'https://www.googleapis.com/youtube/v3/channels', [
								'headers' => [
									'Authorization' => 'Bearer ' . $response->access_token
								],
								'query' => [
									'part' => 'contentDetails,statistics',
									'forUsername' => 'Coursera'
										//'id' => implode(',', $ids),
										//'key' => $this->container->getParameter('google_id'),
								]
							]);
							$response = json_decode($response->getBody());
							return $response;
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
							}
						} catch(\GuzzleHttp\Exception\RequestException $e) {
							echo \GuzzleHttp\Psr7\str($e->getRequest());
							if ($e->hasResponse()) {
								echo \GuzzleHttp\Psr7\str($e->getResponse());
							}
						} catch(\GuzzleHttp\Exception\ClientException $e) {
							echo \GuzzleHttp\Psr7\str($e->getRequest());
							if ($e->hasResponse()) {
								echo \GuzzleHttp\Psr7\str($e->getResponse());
							}
						}
					}
					return false;
				} catch(\GuzzleHttp\Exception\RequestException $e) {
					echo \GuzzleHttp\Psr7\str($e->getRequest());
					if ($e->hasResponse()) {
						echo \GuzzleHttp\Psr7\str($e->getResponse());
					}
				} catch(\GuzzleHttp\Exception\ClientException $e) {
					echo \GuzzleHttp\Psr7\str($e->getRequest());
					if ($e->hasResponse()) {
						echo \GuzzleHttp\Psr7\str($e->getResponse());
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
					//var_dump($e->getMessage());
					return false;
				}
			}
		}
		return false;
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
					return false;
				} catch(\Exception $e) {
					//var_dump($e->getMessage());
					return false;
				}
			}
		}
		return false;
	}
}