<?php 
namespace Influencer\AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Fixtures;
use Symfony\Component\Intl\Intl;


class LoadFixtures implements FixtureInterface
{
	protected static $AUDIENCE = [
		'art-n-design' => [
				'name' => 'Art & Design',
				'icon' => 'fa-paint-brush',
		],
		'beauty' => [
				'name' => 'Beauty',
				'icon' => 'fa-circle',
		],
		'women-fashion-n-jewelry' => [
				'name' => 'Women’s Fashion & Jewelry',
				'icon' => 'fa-female',
		],
		'men-fashion-n-jewelry' => [
				'name' => 'Men’s Fashion & Jewelry',
				'icon' => 'fa-male',
		],
		'travel' => [
				'name' => 'Travel',
				'icon' => 'fa-globe',
		],
		'adventure' => [
				'name' => 'Adventure',
				'icon' => 'fa-circle-o',
		],
		'auto' => [
				'name' => 'Auto',
				'icon' => 'fa-car',
		],
		'sport' => [
				'name' => 'Sports',
				'icon' => 'fa-soccer-ball-o',
		],
		'food' => [
				'name' => 'Food',
				'icon' => 'fa-cutlery',
		],
		'fitness' => [
				'name' => 'Fitness',
				'icon' => 'fa-circle',
		],
		'wellness-n-nutrition' => [
				'name' => 'Wellness & Nutrition',
				'icon' => 'fa-circle',
		],
		'parenting' => [
				'name' => 'Parenting',
				'icon' => 'fa-child',
		],
		'life-coach' => [
				'name' => 'Life Coach',
				'icon' => 'fa-circle',
		],
		'music' => [
				'name' => 'Music',
				'icon' => 'fa-music',
		],
		'nightlife' => [
				'name' => 'Nightlife',
				'icon' => 'fa-circle',
		],
		'entertainment' => [
				'name' => 'Entertainment',
				'icon' => 'fa-circle',
		],
		'pets' => [
				'name' => 'Pets',
				'icon' => 'fa-paw',
		],
		'tech' => [
				'name' => 'Tech',
				'icon' => 'fa-cogs',
		],
	];
	
	public static function ageBracket($age)
	{
		switch ($age) {
			case ($age > 17 && $age < 25):
				$bracket = '18-24';
				break;
			case ($age > 24 && $age < 30):
				$bracket = '25-29';
				break;
			case ($age > 29 && $age < 35):
				$bracket = '30-34';
				break;
			case ($age > 34 && $age < 40):
				$bracket = '35-39';
				break;
			case ($age > 39 && $age < 45):
				$bracket = '40-44';
				break;
			case ($age > 44):
				$bracket = '45+';
				break;
			default:
				$bracket = 0;
		}
		return $bracket;
	}
	
	public static function getLanguageNameByCode($code)
	{
		$name = explode('_', $code);
		if (isset($name[0])) {
			$code = $name[0];
			$sub = isset($name[1])?$name[1]:null;
		} else {
			$code = 'en';
			$sub = null;
		}
		return Intl::getLanguageBundle()->getLanguageName($code, $sub);
	}
	
	public static function getCountryNameByCode($code)
	{
		$country = Intl::getRegionBundle()->getCountryName($code);
		if (!$country) {
			$country = 'Ukraine';
		}
		return $country;
	}
	
	public static function getAudienceCode()
	{
		$faker = \Faker\Factory::create();
		return $faker->randomElement(array_keys(self::$AUDIENCE));
	}
	
	public static function getAudienceName($code)
	{
		return self::$AUDIENCE[$code]['name'];
	}
	
	public static function getAudienceIcon($code)
	{
		return self::$AUDIENCE[$code]['icon'];
	}
	
	public function load(ObjectManager $manager)
	{
		//$provider = new CustomProvider();
		//$loader = new Loader('en_US', $provider);
		$objects = Fixtures::load(__DIR__.'/fixtures.yml', $manager);
		
	}
}
