<?php

namespace BaksDev\Reference\Cars\Command;

use BaksDev\Reference\Cars\Entity\Brand\CarsBrand;

use BaksDev\Reference\Cars\Entity\Brand\Trans\CarsBrandTrans;
use BaksDev\Reference\Cars\Entity\Model\CarsModel;
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
	name: 'baks:cars:model',
	description: 'Получаем модели автомобилей по списку брендов',
)]
class CarsModelCommand extends Command
{
	
	private EntityManagerInterface $entityManager;
	private HttpClientInterface $client;
	private CarsModelHandler $handler;
	
	public function __construct(
		EntityManagerInterface $entityManager,
		HttpClientInterface $client,
		CarsModelHandler $handler
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
		
		
		
		
		/*		$response = $this->client->request(
					'GET',
					'/Catalog/Global/Cars'
				);
				
				dd($response->getContent());*/
		
		
		$io = new SymfonyStyle($input, $output);
		
		$cache = new FilesystemAdapter();
		$carsBrands = $cache->getItem('auto-cars')->get();
		$models = null;
		
		if(!$cache->hasItem('car-models'))
		{
			foreach($carsBrands as $type => $brands)
			{
				
				$class = match ($type)
				{
					'Легковой' => CarsModelClassEnum::C0,
					'Грузовые и автобусы' => CarsModelClassEnum::C1,
					'Коммерческий транспорт' => CarsModelClassEnum::C2,
					'Мотоциклы' => CarsModelClassEnum::C3,
				};
				
				
				foreach($brands as $brand => $href)
				{
					
					
					//$cache->delete($href);
					//continue;
					
					if(!$cache->hasItem($href))
					{
						$delay = random_int(1, 2);
						sleep($delay);
					}
					
					$content = $cache->get($href, function(ItemInterface $item) use ($href){
						$item->expiresAfter(86400 * random_int(15, 30));
						
						$response = $this->client->request(
							'GET',
							$href.'?all=1'
						);
						
						return $response->getContent();
					});
					
					
					dump($brand);
					
					$brandCrawler = new Crawler($content);
					//$brandValues = $brandCrawler->filter('div.car-info__car-name a');
					//$brandModels = $brandCrawler->filter('div.car-info__car-models');
					
					
					$brandValues = $brandCrawler->filter('div.car-info__description');
					
					foreach($brandValues as $brandValue)
					{
						
						foreach($brandValue->childNodes as $childNode)
						{
							if($childNode->attributes?->getNamedItem('class')->value == 'car-info__car-name')
							{
								foreach($childNode->childNodes as $hrefChildNode)
								{
									$hrefModel = $hrefChildNode->attributes->getNamedItem('href')->nodeValue;
								}
								
								$model = $childNode->textContent;
							}
							
							
							if($childNode->attributes?->getNamedItem('class')->value == 'car-info__car-models')
							{
								$brandModels = $childNode->textContent;
							}
							
							if($childNode->attributes?->getNamedItem('class')->value == 'car-info__car-years')
							{
								$years = explode('-', $childNode->textContent);
							}
							
							//dump($childNode->attributes?->getNamedItem('class')->value);
						}
						
						
						
						//$name = $brandValues->attributes->getNamedItem('car-info__car-name')->nodeValue;
						
						//$model = $brandValue->textContent;
						//$hrefModel = $brandValue->attributes->getNamedItem('href')->nodeValue;
						
						if(empty($years[0])) { continue; }
						
						$models[$brand][$model]['href'] = $hrefModel;
						$models[$brand][$model]['class'] = $class->value;
						$models[$brand][$model]['code'] = $brandModels;
						$models[$brand][$model]['year'] = $years;
					}
				}
				
				
				
				//dump($href);
				//dd($brand);
				
			}
		}
		
		$models = $cache->get('car-models', function(ItemInterface $item) use ($models){
			$item->expiresAfter(86400 * random_int(15, 30));
			return $models;
		});
		
		if($models === null)
		{
			$cache->delete('car-models');
			$io->warning('Сбросили кеш моделей автомобилей. Перезапустите комманду');
			return Command::SUCCESS;
		}
	
		foreach($models as $model => $data)
		{
			dump($model);
			
			/* Получаем бренд */
			$this->entityManager->clear();
			
			$qb = $this->entityManager->createQueryBuilder();
			$qb->select('brand');
			$qb->from(CarsBrand::class, 'brand');
			$qb->join(CarsBrandTrans::class, 'trans', 'WITH', 'brand.event = trans.event AND trans.name = :name');
			$qb->setParameter('name', $model);
			$qb->setMaxResults(1);
			
			$CarsBrand = $qb->getQuery()->getOneOrNullResult();
			
			
			if($CarsBrand)
			{
				foreach($data as $name => $datum)
				{
					$CarsModelTrans = $this->entityManager->getRepository(CarsModelTrans::class)->findOneBy(['name' => $name]);
					
					
					if(!$CarsModelTrans)
					{
						
						
						$CarsModelDTO = new CarsModelDTO();
						$CarsModelDTO->setClass(CarsModelClassEnum::from($datum['class']));
						$CarsModelDTO->setCode($datum['code']);
						$CarsModelDTO->setFrom(trim($datum['year'][0]));
						
						if(($to = trim($datum['year'][1])) !== 'наст. время')
						{
							$CarsModelDTO->setTo($to);
						}
						
						$CarsModelTransDTO = $CarsModelDTO->getTranslate();
						
						/** @var CarsBrandTransDTO $trans */
						foreach($CarsModelTransDTO as $trans)
						{
							$trans->setName($name);
						}
						
						$CarsModel = $this->handler->handle($CarsModelDTO);
						
						if($CarsModel instanceof CarsModel)
						{
							$CarsModel->setBrand($CarsBrand);
							$this->entityManager->flush();
							
							$io->success(sprintf('Добавили новую модель %s', $name));
						}
						else
						{
							$io->error(sprintf('Ошибка %s при добавлении модели автомобиля %s', $CarsModel, $name));
							return Command::FAILURE;
						}
						
						
						
						
						//dump($name);
						///dd($datum);
					}
					
					
					
					
				}
				
				
				//
			}
			
			$io->text($model);
			
		}
		
		//dd();
		

		
		return Command::SUCCESS;
	}
	
}
