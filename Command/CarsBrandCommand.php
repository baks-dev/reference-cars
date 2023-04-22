<?php

namespace BaksDev\Reference\Cars\Command;

use BaksDev\Reference\Cars\Entity\Brand\CarsBrand;
use BaksDev\Reference\Cars\Entity\Brand\Event\CarsBrandEvent;
use BaksDev\Reference\Cars\Entity\Brand\Trans\CarsBrandTrans;
use BaksDev\Reference\Cars\Type\Brand\Id\CarsBrandId;
use BaksDev\Reference\Cars\UseCase\Brand\Admin\NewEdit\CarsBrandDTO;
use BaksDev\Reference\Cars\UseCase\Brand\Admin\NewEdit\CarsBrandHandler;
use BaksDev\Reference\Cars\UseCase\Brand\Admin\NewEdit\Trans\CarsBrandTransDTO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
	name: 'baks:cars:brand',
	description: 'Получаем и обновляем базу брендов автомобилей',
)]
class CarsBrandCommand extends Command
{
	
	private EntityManagerInterface $entityManager;
	private HttpClientInterface $client;
	private CarsBrandHandler $handler;
	
	public function __construct(
		EntityManagerInterface $entityManager,
		HttpClientInterface $client,
		CarsBrandHandler $handler
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
		//$cache->delete('auto-cars');
		
		/* Кешируем результат GET */
		$cars = $cache->get('auto-cars', function(ItemInterface $item){
			
			$item->expiresAfter(86400);
			$response = $this->client->request(
				'GET',
				'/Catalog/Global/Cars'
			);
			
			$crawler = new Crawler($response->getContent());
			
			$arr = null;
			
			$nodeValues = $crawler->filter('span#bmVendorTypes');
			
			foreach($nodeValues as $nodeValue)
			{
				/** @var \DOMText $childNode */
				foreach($nodeValue->childNodes as $childNode)
				{
					/** @var \DOMAttr $attribute */
					foreach($childNode->attributes as $attribute)
					{
						if($attribute->name === 'id')
						{
							$type = match ($attribute->value)
							{
								"bmVendorTypesC0" => 'Легковой',
								"bmVendorTypesC1" => 'Грузовые и автобусы',
								"bmVendorTypesC2" => 'Коммерческий транспорт',
								"bmVendorTypesC3" => 'Мотоциклы',
							};
							
							$nodeBrands = $crawler->filter('div#'.$attribute->value.' div.catalog-column ul li a');
							
							foreach($nodeBrands as $nodeBrand)
							{
								$brand = $nodeBrand->textContent;
								$hrefBrand = $nodeBrand->attributes->getNamedItem('href')->nodeValue;
								
								$arr[$type][$brand] = $hrefBrand;
							}
							
						}
						
						
					}
				}
			}
			
			return $arr;
			
		});
		
		
		foreach($cars as $type => $brands)
		{
			foreach($brands as $brand => $href)
			{
				$entityBrand = $this->entityManager->getRepository(CarsBrandTrans::class)->findOneBy(['name' => $brand]);
				
				if(!$entityBrand)
				{
					
					$CarsBrandDTO = new CarsBrandDTO();
					$CarsBrandTransDTO = $CarsBrandDTO->getTranslate();
					
					/** @var CarsBrandTransDTO $trans */
					foreach($CarsBrandTransDTO as $trans)
					{
						$trans->setName($brand);
					}
					
					$CarsBrand = $this->handler->handle($CarsBrandDTO);
					
					if($CarsBrand instanceof CarsBrand)
					{
						$io->success(sprintf('Добавили новый бренд %s', $brand));
					}
					else
					{
						$io->error(sprintf('Ошибка при добавлении бренда %s', $brand));
					}
				}
				else
				{
					$output->writeln(sprintf('Бренд %s уже добавлен', $brand));
				}
				
			}
		}
		
		
		dd($cars);
		
		
		return Command::SUCCESS;
	}
	
}
