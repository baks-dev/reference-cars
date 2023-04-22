<?php

namespace BaksDev\Reference\Cars\Command;

use BaksDev\Files\Resources\Upload\Image\ImageDownload;
use BaksDev\Reference\Cars\Entity\Brand\CarsBrand;

use BaksDev\Reference\Cars\Entity\Brand\Trans\CarsBrandTrans;
use BaksDev\Reference\Cars\Entity\Model\CarsModel;
use BaksDev\Reference\Cars\Entity\Model\Event\CarsModelEvent;
use BaksDev\Reference\Cars\Entity\Model\Image\CarsModelImage;
use BaksDev\Reference\Cars\Entity\Model\Trans\CarsModelTrans;

use BaksDev\Reference\Cars\Entity\Modification\CarsModification;
use BaksDev\Reference\Cars\Entity\Modification\Characteristics\CarsModificationCharacteristics;
use BaksDev\Reference\Cars\Entity\Modification\Event\CarsModificationEvent;
use BaksDev\Reference\Cars\Type\Brand\Id\CarsBrandUid;
use BaksDev\Reference\Cars\Type\Model\Id\CarsModelUid;
use BaksDev\Reference\Cars\Type\Model\Type\CarsModelClassEnum;

use BaksDev\Reference\Cars\UseCase\Brand\Admin\NewEdit\CarsBrandHandler;
use BaksDev\Reference\Cars\UseCase\Brand\Admin\NewEdit\Trans\CarsBrandTransDTO;
use BaksDev\Reference\Cars\UseCase\Model\Admin\NewEdit\CarsModelDTO;
use BaksDev\Reference\Cars\UseCase\Model\Admin\NewEdit\CarsModelHandler;
use BaksDev\Reference\Cars\UseCase\Modification\NewEdit\CarModificationDTO;
use BaksDev\Reference\Cars\UseCase\Modification\NewEdit\CarModificationHandler;
use BaksDev\Reference\Cars\UseCase\Modification\NewEdit\Characteristic\CarsModificationCharacteristicsDTO;
use BaksDev\Reference\Cars\UseCase\Modification\NewEdit\Characteristic\Chassis\CarsModificationChassisDTO;
use BaksDev\Reference\Cars\UseCase\Modification\NewEdit\Characteristic\Disc\CarsModificationDiscDTO;
use BaksDev\Reference\Cars\UseCase\Modification\NewEdit\Characteristic\Tire\CarsModificationTiresDTO;
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
	name: 'baks:cars:characteristics',
	description: 'Получаем модификации моделей автомобилей',
)]
class CarsCharacteristicCommand extends Command
{
	
	private EntityManagerInterface $entityManager;
	private HttpClientInterface $client;
	private CarModificationHandler $handler;

	
	public function __construct(
		EntityManagerInterface $entityManager,
		HttpClientInterface $client,
		CarModificationHandler $handler,
		ImageDownload $imageDownload
	)
	{
		parent::__construct();
		$this->entityManager = $entityManager;
		
		$this->client = $client->withOptions([
			'base_uri' => 'https://exist.ru',
		]);
		$this->handler = $handler;

	}
	
	protected function execute(InputInterface $input, OutputInterface $output) : int
	{
		$io = new SymfonyStyle($input, $output);
		
		$cache = new FilesystemAdapter();
		
		
		if(!$cache->hasItem('car-modification'))
		{
			$io->error(
				'Отсутствует массив моделей автомобилей. Запустите комманду php bin/console app:products:cars:model'
			);
			return Command::SUCCESS;
		}
		
		
		/* Кешируем результат GET */
		$cacheModifications = $cache->getItem('car-modification')->get();
		
		
		foreach($cacheModifications as $brand => $Modification)
		{
			
			/* Получаем бренд */
			$this->entityManager->clear();
			
			$qb = $this->entityManager->createQueryBuilder();
			$qb->select('brand');
			$qb->from(CarsBrand::class, 'brand');
			$qb->join(CarsBrandTrans::class, 'trans', 'WITH', 'brand.event = trans.event AND trans.name = :name');
			$qb->setParameter('name', $brand);
			$qb->setMaxResults(1);
			
			$CarsBrand = $qb->getQuery()->getOneOrNullResult();
			
			
			foreach($Modification as $model => $modifys)
			{
				
				/* Получаем модель */
				$this->entityManager->clear();
				
				$qb = $this->entityManager->createQueryBuilder();
				$qb->select('model');
				$qb->from(CarsModel::class, 'model');
				$qb->join(CarsModelEvent::class, 'event', 'WITH', 'event.id = model.event');
				$qb->join(CarsModelTrans::class, 'trans', 'WITH', 'trans.event = model.event AND trans.name = :name');
				$qb->where('model.brand = :brand');
				$qb->setParameter('brand', $CarsBrand->getId(), CarsBrandUid::TYPE);
				$qb->setParameter('name', $model);
				$qb->setMaxResults(1);
				
				/** @var CarsModel $CarsModel */
				$CarsModel = $qb->getQuery()->getOneOrNullResult();
				if(empty($CarsModel)) { continue; }
				
				
				foreach($modifys as $modify)
				{
					///dump($brand.' '.$model);
					
					/* Получаем модификацию */
					$this->entityManager->clear();
					
					$qb = $this->entityManager->createQueryBuilder();
					$qb->select('event');
					$qb->from(CarsModification::class, 'modification');
					$qb->join(
						CarsModificationEvent::class,
						'event',
						'WITH',
						'event.id = modification.event AND event.modification = :modify'
					);
					
					$qb->where('modification.model = :model');
					
					$qb->setParameter('model', $CarsModel->getId(), CarsModelUid::TYPE);
					$qb->setParameter('modify', $modify['modif']);
					$qb->setMaxResults(1);
					
					/** @var CarsModel $CarsModel */
					$CarsModification = $qb->getQuery()->getOneOrNullResult();
					$CarModificationDTO = new CarModificationDTO();
					
					
					
//					dump($CarsModel->getId());
//					dump($modify['modif']);
//					dd($CarsModification);
					
					if($CarsModification)
					{
						$CarsModification->getDto($CarModificationDTO);
						
						/* Проверяем, имеется ли такая марка двигателя у модификации */
						$Characteristics = $this->entityManager->getRepository(CarsModificationCharacteristics::class)
							->findOneBy(['event' => $CarsModification->getId(), 'model' => $modify['motor']])
						;
						
						if($Characteristics)
						{
							continue;
						}
					}
					else
					{
						$CarModificationDTO->setModification(trim($modify['modif']));
					}
					
					
					
					

					
					
					//if($CarsModification === null)
					//{
						
						$CarsModificationCharacteristics = new CarsModificationCharacteristicsDTO();
						$CarsModificationCharacteristics->setModel($modify['motor']);
						
						/* Дата выпуска модификации */
						$datum = explode('-', $modify['years']);
						$CarsModificationCharacteristics->setFrom(trim($datum[0]));
						if(($to = trim($datum[1])) !== 'наст. время')
						{
							$CarsModificationCharacteristics->setTo($to);
						}
						
						$CarsModificationMotorDTO = $CarsModificationCharacteristics->getMotor();
						$CarsModificationMotorDTO->setEngine($modify['engine']);
						$CarsModificationMotorDTO->setDrive($modify['drive']);
						$CarsModificationMotorDTO->setPower((int)$modify['power']);
						$CarsModificationMotorDTO->setFuel($modify['fuel']);
						
						$CarModificationDTO->addCharacteristic($CarsModificationCharacteristics);
						
					//}
					
					
				
						/* ШИНЫ */
						if($modify['tire'])
						{
							/* Шины */
							$content = $cache->get($modify['tire'], function(ItemInterface $item) use ($modify){
								$item->expiresAfter(86400 * random_int(15, 30) + random_int(3600, 86400));
								
								$response = $this->client->request(
									'GET',
									$modify['tire']
								);
								
								return $response->getContent();
							});
							
							
							$tireCrawler = new Crawler($content);
							
							$filterTire = $tireCrawler->filter('div.wheels div.wh');
							
							foreach($filterTire as $NodeTires)
							{
								foreach($NodeTires->childNodes as $childNode)
								{
									/* На обе оси */
									if($childNode->nodeName == 'div')
									{
										if($childNode->attributes->getNamedItem('class')->nodeValue == 'middle')
										{
											
											$txt = trim($childNode->textContent);
											
											
											$txtExplode = explode('R', $txt);
											
											
											$txtExpExplode = explode('/', $txtExplode[0]);
											if(count($txtExpExplode) != 2) { continue; }
											if((int)$txtExplode[1] > 30) { continue; }
											
											//dump($txt);
											//dump($modify['tire']);
											$size = $txtExpExplode[0] * 1;
											$profile = $txtExpExplode[1] * 1;
											$radius = $txtExplode[1] * 1;
											
											$CarsModificationTiresDTO = new CarsModificationTiresDTO();
											
											
											
											$CarsModificationTiresDTO->setSize($size);
											$CarsModificationTiresDTO->setProfile($profile);
											$CarsModificationTiresDTO->setRadius($radius);
											
											$CarsModificationCharacteristics->addTire($CarsModificationTiresDTO);
				
										}
									}
								}
							}
						}
						
						
						
						//$modify['disc'] = '/Catalog/Wheels/Disc.aspx?id=3B700021';
						
						/* ДИСКИ */
						if($modify['disc'])
						{
							/* Шины */
							$content = $cache->get($modify['disc'], function(ItemInterface $item) use ($modify){
								$item->expiresAfter(86400 * random_int(15, 30) + random_int(3600, 86400));
								
								$response = $this->client->request(
									'GET',
									$modify['disc']
								);
								
								return $response->getContent();
							});
							
							
							$discCrawler = new Crawler($content);
							
							
							/* Парсим ШАСИ */
							
						
							
							$filterShassi = $discCrawler->filter('div.ZeForm > div');
							
							$type = null;
							$number = null;
							$dia = null;
							$pcd = null;
							$fasteners = null;
							
							foreach($filterShassi as $NodeShassi)
							{
								foreach($NodeShassi->childNodes as $childNodeAttr)
								{
									$text = trim($childNodeAttr->textContent);
									
									if($text)
									{
										
										if(stripos($text, 'PCD') !== false || stripos($text, 'Крепление'))
										{
											$type = 'pcd';
											continue;
										}
										
										if(stripos($text, 'DIA') !== false || stripos($text, 'Ступич'))
										{
											$type = 'dia';
											continue;
										}
										
										if(stripos($text, 'Крепёж') !== false)
										{
											$type = 'fasteners';
											continue;
										}
										
										//dump('------------'.$type);
										
										/* Крепление колеса (PCD) */
										if($type == 'pcd')
										{
											$explodetext = explode('x', $text);
											
											if(count($explodetext) != 2)
											{
												dump($text);
												throw new \InvalidArgumentException('Ошибка парсинга Крепление колеса (PCD)');
											}
											
											$number = (int) $explodetext[0];
											$pcd = (float) $explodetext[1] * 1;
											
											continue;
										}
										
										/* Ступичное отверстие (DIA) */
										if($type == 'dia')
										{
											$dia = (float) $text;
											continue;
										}
										
										/* Крепёж */
										if($type == 'fasteners')
										{
											$fasteners = $text;
											continue;
										}
										
										//dump($text);
									}
								}
								
								//dump($NodeShassi->lastChild->textContent);
							}
							
							$CarsModificationChassisDTO = new CarsModificationChassisDTO();
							$CarsModificationChassisDTO->setDia($dia);
							$CarsModificationChassisDTO->setPcd($pcd);
							$CarsModificationChassisDTO->setFastener($fasteners);
							$CarsModificationChassisDTO->setNumber($number);
							$CarsModificationCharacteristics->setChassi($CarsModificationChassisDTO);
							
							
							
							$filterDisc = $discCrawler->filter('div.wheels div.wheels__wheel-block');
							
							foreach($filterDisc as $NodeDisc)
							{
								
								if($NodeDisc->nodeName == 'div')
								{
									foreach($NodeDisc->childNodes as $childNodeDisc)
									{
										if($childNodeDisc->nodeName == 'div')
										{
											
											$txt = trim($childNodeDisc->textContent);
											$explodeTxt = explode('ET', $txt);
											
											if(count($explodeTxt) === 2)
											{
												$explodeSize = explode('x', $explodeTxt[0]);
												
												if(count($explodeSize) !== 2)
												{
													dump($explodeTxt[0]);
													throw new \InvalidArgumentException('Ошибка при получении размеров');
												}
												
												
												$diameter = (float) $explodeSize[1];
												$width = (float) $explodeSize[0];
												$et = (float) $explodeTxt[1];
												
												$CarsModificationDiscDTO = new CarsModificationDiscDTO();
												$CarsModificationDiscDTO->setEt($et);
												$CarsModificationDiscDTO->setWidth($width);
												$CarsModificationDiscDTO->setDiameter($diameter);
												$CarsModificationCharacteristics->addDisc($CarsModificationDiscDTO);
											}
										}
									}
								}
							}
							
							
						}
					

						
						
						$Car = $this->handler->handle($CarModificationDTO);
						
						if($Car instanceof CarsModification)
						{
							/* $CarsModel->getId() */
							$Car->setModel($CarsModel->getId());
							$this->entityManager->flush();
							
							$io->success(sprintf('Добавили Модификацию %s %s %s', $brand, $model, $modify['modif']));
						}
						else
						{
							$io->error(sprintf('Ошибка при добавлении бренда %s %s %s', $brand, $model, $modify['modif']));
						}
						
						//sleep(1);
						//dd();
					
					
					
					//dump($CarsBrand->getId());
					//dump($CarsModel->getId());
					//dd($CarsModification);
				}
				
			}
			
		}
		
		
		return Command::SUCCESS;
	}
	
}
