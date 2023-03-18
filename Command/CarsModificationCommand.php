<?php

namespace BaksDev\Reference\Cars\Command;

use BaksDev\Files\Resources\Upload\Image\ImageDownload;
use BaksDev\Reference\Cars\Entity\Brand\CarsBrand;

use BaksDev\Reference\Cars\Entity\Brand\Trans\CarsBrandTrans;
use BaksDev\Reference\Cars\Entity\Model\CarsModel;
use BaksDev\Reference\Cars\Entity\Model\Event\CarsModelEvent;
use BaksDev\Reference\Cars\Entity\Model\Image\CarsModelImage;
use BaksDev\Reference\Cars\Entity\Model\Trans\CarsModelTrans;

use BaksDev\Reference\Cars\Type\Model\Type\CarsModelClassEnum;

use BaksDev\Reference\Cars\UseCase\Brand\Admin\NewEdit\CarsBrandHandler;
use BaksDev\Reference\Cars\UseCase\Brand\Admin\NewEdit\Trans\CarsBrandTransDTO;
use BaksDev\Reference\Cars\UseCase\Model\Admin\NewEdit\CarsModelDTO;
use BaksDev\Reference\Cars\UseCase\Model\Admin\NewEdit\CarsModelHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;

use Symfony\Component\Console\Input\InputInterface;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Symfony\Component\DomCrawler\Crawler;

use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
	name: 'app:products:cars:modification',
	description: 'Получаем модификации моделей автомобилей',
)]
class CarsModificationCommand extends Command
{
	
	private EntityManagerInterface $entityManager;
	private HttpClientInterface $client;
	private CarsModelHandler $handler;
	private ImageDownload $imageDownload;
	
	
	public function __construct(
		EntityManagerInterface $entityManager,
		HttpClientInterface $client,
		CarsModelHandler $handler,
		ImageDownload $imageDownload
	)
	{
		parent::__construct();
		$this->entityManager = $entityManager;
		
		$this->client = $client->withOptions([
			'base_uri' => 'https://exist.ru',
		]);
		$this->handler = $handler;
		$this->imageDownload = $imageDownload;
	}
	
	protected function execute(InputInterface $input, OutputInterface $output) : int
	{
		
		$io = new SymfonyStyle($input, $output);
		
		$cache = new FilesystemAdapter();
		
		if(!$cache->hasItem('car-models'))
		{
			$io->error('Отсутствует массив моделей автомобилей. Запустите комманду php bin/console app:products:cars:model');
			return Command::SUCCESS;
		}
		
		$models = $cache->getItem('car-models')->get();
		
		$modification = null;
		
		foreach($models as $brand => $model)
		{
			foreach($model as $name => $modificator)
			{
				
				$this->entityManager->clear();
				
				$qb = $this->entityManager->createQueryBuilder();
				$qb->select('event');
				$qb->from(CarsModel::class, 'model');
				$qb->join(CarsModelEvent::class, 'event', 'WITH', 'event.id = model.event');
				$qb->join(CarsModelTrans::class, 'trans', 'WITH', 'trans.event = model.event AND trans.name = :name');
				$qb->setParameter('name', $name);
				$qb->setMaxResults(1);
				
				/** @var CarsModelEvent $CarsModelEvent */
				$CarsModelEvent = $qb->getQuery()->getOneOrNullResult();
				
				if(!$CarsModelEvent)
				{
					dump($brand);
					dump($name);
					continue;
				}
				
				dump($brand.' '.$name);
				
				
				if(!$cache->hasItem($modificator['href']))
				{
					$delay = random_int(1, 2);
					sleep($delay);
				}
				
				
//				if($modificator['href'] == '/Catalog/Global/Trucks/Fiat/12054')
//				{
//					$cache->delete($modificator['href']);
//					dd('/Catalog/Global/Trucks/Fiat/12054');
//				}
				
				//dump('Получаем...'.$modificator['href']);
				
				$content = $cache->get($modificator['href'], function(ItemInterface $item) use ($modificator){
					$item->expiresAfter(86400 * random_int(15, 30) + random_int(3600, 86400));

					dump(sprintf('%s: Получаем ... %s', time(), $modificator['href']));
					
					$response = $this->client->request(
						'GET',
						$modificator['href']
					);
					
					return $response->getContent();
				});
				
				
				//dump('Получили контент'.$name);
				
				$carsCrawler = new Crawler($content);
				
				$imgCarNode = $carsCrawler->filter('img#imgCar');
				$imgCar = $imgCarNode->getNode(0)->attributes->getNamedItem('src')->nodeValue;
				
				/* Загружаем фото модели авто */
				if(strripos($imgCar, 'nocar.png') === false)
				{
					$imgCar = str_replace('//img.exist.ru', 'http://img.exist.ru', $imgCar);
					$imgCar = str_replace('&Size=150x100', '', $imgCar);
					
					/* Получаем сущность изображения */
					$CarsModelImageClass = $CarsModelEvent->getUploadClass();
					
					/** @var CarsModelImage $CarsModelImage */
					$CarsModelImage = $this->imageDownload->get($imgCar, $CarsModelImageClass, 'cars_model_dir');
					if($CarsModelImage)
					{
						$this->entityManager->persist($CarsModelImage);
						$this->entityManager->flush();
						
						dump('Сохранили фото'.$name);
					}
					
				}
				
				
				
				
				/* if($cache->hasItem('car-modification'))
				{
					continue;
				} */
				
				$modif = null; // Модификация
				$fuel = null; // Тип двиг
				$drive = null; // Привод
				$motor = null; // Модель двиг
				$engine = null; // Объем
				$power = null; // Мощность
				$year = null; // Даты выпуска
				
				
				
				/* aditional */
				$aditional = $carsCrawler->filter('div.car-info__additional');
				
				/** @var \DOMElement $adit  */
				foreach($aditional->getIterator() as $adit)
				{
					$isdata = false;
					/** @var \DOMText $childNode */
					foreach($adit->childNodes as $childNode)
					{
						$txt = trim($childNode->textContent);
						
						if(strripos($txt, 'Тип') !== false)
						{
							$isdata = 'fuel';
							continue;
						}
						
						if(strripos($txt, 'Модель') !== false)
						{
							$isdata = 'motor';
							continue;
						}
						
						
						if(strripos($txt, 'Привод') !== false)
						{
							$isdata = 'drive';
							continue;
						}
						
						if($isdata === 'fuel')
						{
							$fuel = $txt;
							$isdata = false;
							continue;
						}
						
						if($isdata === 'motor')
						{
							$motor = $txt;
							$isdata = false;
							continue;
						}
						
						if($isdata === 'drive')
						{
							$drive = $txt;
							$isdata = false;
							continue;
						}
					}
				}
				
				
				$table = $carsCrawler->filter('table#gvData tr');
				
				
				$modifHref = null; // ссылка
				$modifkey = null; // Модификация
				$fuelkey = null; // Тип двиг
				$drivekey = null; // Привод
				$motorkey = null; // Модель двиг
				$enginekey = null; // Объем
				$powerkey = null; // Мощность
				$yearkey = null; // Даты выпуска
				
				foreach($table as $key => $tr)
				{

					foreach($tr->childNodes->getIterator() as $type => $item)
					{
	
						$text = trim($item->textContent);
						
						/* Шапка таблицы */
						if($key === 0)
						{
	
							if(strripos($text, 'Модификация') !== false)
							{
								$modifkey = $type;
							}
							
							
							if(strripos($text, 'Тип двиг') !== false)
							{
								$fuelkey = $type;
							}
							
							if(strripos($text, 'Модель') !== false)
							{
								$motorkey = $type;
							}
							
							if(strripos($text, 'Объем') !== false)
							{
								$enginekey = $type;
							}
							
							if(strripos($text, 'Мощность') !== false)
							{
								$powerkey = $type;
							}
							
							if(strripos($text, 'Даты') !== false)
							{
								$yearkey = $type;
							}
							
							if(strripos($text, 'Привод') !== false)
							{
								$drivekey = $type;
							}
							
							continue;
						}
						
						if($type === $modifkey)
						{
							
							$modifHref = $item->childNodes->item(1)->attributes->getNamedItem('href')->nodeValue;
							$modif = $text;
							//dump('Модификация: '.$text);
						}
						
						if($type === $fuelkey)
						{
							$fuel = $text;
						}
						
						if($type === $motorkey)
						{
							$motor = $text;
							//dump('Модель двигателя: '.$text);
						}
						
						if($type === $enginekey)
						{
							$engine = $text;
							//dump('Объем двигателя: '.$text);
						}
						
						if($type === $powerkey)
						{
							$power = $text;
							//dump('Мощность двигателя: '.$text);
						}
						
						if($type === $yearkey)
						{
							$year = $text;
							//dump('Год выпуска: '.$text);
						}
						
						
						if($type === $drivekey)
						{
							$drive = $text;
							//dump('Привод: '.$text);
						}
					}
					
					
					
					if($modifHref)
					{
						
						
						/* Получаем данные с шинами и дисками */
						
						//$modifHref = '/Catalog/Global/Cars/BMW/299/BD300006/?r=1';
						
						$content = $cache->get($modifHref , function(ItemInterface $item) use ($modifHref) {
							$item->expiresAfter(86400 * random_int(15, 30) + random_int(3600, 86400));
							
							$response = $this->client->request(
								'GET',
								$modifHref
							);
							
							return $response->getContent();
						});
						
						
						$detailCrawler = new Crawler($content);
						
						$blocks = $detailCrawler->filter('div.cat-wrap div a');
						
						$tire = null;
						$disc = null;
						
						foreach($blocks as $block)
						{
							if($block->textContent == 'Шины')
							{
								$tire = $block->attributes->getNamedItem('href')->nodeValue;
								
								if(!$cache->hasItem($tire))
								{
									$delay = random_int(1, 2);
									sleep($delay);
								}
								
								$cache->get($tire , function(ItemInterface $item) use ($tire) {
									$item->expiresAfter(86400 * random_int(15, 30) + random_int(3600, 86400));
									
									dump(sprintf('%s: Получаем Шины ... %s', time(), $tire));
									
									$response = $this->client->request(
										'GET',
										$tire
									);
									
									return $response->getContent();
								});
								
							}
							
							if($block->textContent == 'Диски колёсные')
							{
								$disc = $block->attributes->getNamedItem('href')->nodeValue;
								
								if(!$cache->hasItem($disc))
								{
									$delay = random_int(1, 2);
									sleep($delay);
								}
								
								$cache->get($disc , function(ItemInterface $item) use ($disc) {
									$item->expiresAfter(86400 * random_int(15, 30) + random_int(3600, 86400));
									
									dump(sprintf('%s: Получаем Диски ... %s', time(), $disc));
									
									$response = $this->client->request(
										'GET',
										$disc
									);
									
									return $response->getContent();
								});
							}
						}
						
						
						$modification[$brand][$name][] =
							[
								'modif' => $modif,
								'drive' => $drive,
								'fuel' => $fuel,
								'motor' => $motor,
								'engine' => $engine,
								'power' => $power,
								'years' => $year,
								'href' => $modifHref,
								'tire' => $tire,
								'disc' => $disc,
							];
			
					}
				}
			}
		}
		
		//$cache->delete('car-modification');
		
		/* Кешируем результат */
		$cacheModifications = $cache->get('car-modification', function(ItemInterface $item) use ($modification){
			$item->expiresAfter(86400 * random_int(15, 30));
			return $modification;
		});
		
		

		return Command::SUCCESS;
	}
	
}
