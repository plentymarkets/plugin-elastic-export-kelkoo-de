<?php

namespace ElasticExportKelkooDE\Generator;

use ElasticExport\Helper\ElasticExportCoreHelper;
use ElasticExport\Helper\ElasticExportPriceHelper;
use ElasticExport\Helper\ElasticExportStockHelper;
use Plenty\Modules\DataExchange\Contracts\CSVPluginGenerator;
use Plenty\Modules\Helper\Services\ArrayHelper;
use Plenty\Modules\DataExchange\Models\FormatSetting;
use Plenty\Modules\Helper\Models\KeyValue;
use Plenty\Modules\Item\Manufacturer\Contracts\ManufacturerRepositoryContract;
use Plenty\Modules\Item\Search\Contracts\VariationElasticSearchScrollRepositoryContract;
use Plenty\Plugin\Log\Loggable;

/**
 * Class KelkooDE
 *
 * @package ElasticExportKelkooDE\Generator
 */
class KelkooDE extends CSVPluginGenerator
{
	use Loggable;

	const KELKOO_DE = 6.00;

	/**
	 * @var ElasticExportCoreHelper $elasticExportHelper
	 */
	private $elasticExportHelper;

	/**
	 * @var ElasticExportStockHelper $elasticExportStockHelper
	 */
	private $elasticExportStockHelper;

	/**
	 * @var ElasticExportPriceHelper $elasticExportPriceHelper
	 */
	private $elasticExportPriceHelper;

	/**
	 * @var ArrayHelper $arrayHelper
	 */
	private $arrayHelper;

	/**
	 * KelkooDE constructor.
	 *
	 * @param ArrayHelper $arrayHelper
	 */
	public function __construct(ArrayHelper $arrayHelper)
	{
		$this->arrayHelper = $arrayHelper;
	}

	/**
	 * @param VariationElasticSearchScrollRepositoryContract $elasticSearch
	 * @param array $formatSettings
	 * @param array $filter
	 */
	protected function generatePluginContent($elasticSearch, array $formatSettings = [], array $filter = [])
	{
		$this->elasticExportHelper = pluginApp(ElasticExportCoreHelper::class);
		$this->elasticExportStockHelper = pluginApp(ElasticExportStockHelper::class);
		$this->elasticExportPriceHelper = pluginApp(ElasticExportPriceHelper::class);

		$settings = $this->arrayHelper->buildMapFromObjectList($formatSettings, 'key', 'value');
		$this->setDelimiter("	");		// tab

		$this->addCSVContent([
			'offer-id',
			'title',
			'product-url',
			'price',
			'brand',
			'description',
			'image-url',
			'ean',
			'merchant-category',
			'availability',
			'delivery-cost',
			'delivery-time',
			'ecotax',
			'mpn',
			'unit-price',
			'image-url-2',
			'image-url-3',
			'image-url-4',
		]);

		$limitReached = false;
		$lines = 0;
		$startTime = microtime(true);

		if($elasticSearch instanceof VariationElasticSearchScrollRepositoryContract)
		{
			do
			{
				if($limitReached === true)
				{
					break;
				}

				$this->getLogger(__METHOD__)->debug('ElasticExportKelkooDE::log.writtenlines', ['lines written' => $lines]);

				$esStartTime = microtime(true);

				$resultList = $elasticSearch->execute();

				$this->getLogger(__METHOD__)->debug('ElasticExportKelkooDE::log.esDuration', [
					'Elastic Search duration' => microtime(true) - $esStartTime,
				]);

				if(count($resultList['error']) > 0)
				{
					$this->getLogger(__METHOD__)->error('ElasticExportKelkooDE::log.occurredElasticSearchErrors', [
						'error message' => $resultList['error'],
					]);
				}

				$buildRowStartTime = microtime(true);

				if(is_array($resultList['documents']) && count($resultList['documents']) > 0)
				{
					foreach($resultList['documents'] as $item)
					{
						if($this->elasticExportStockHelper->isFilteredByStock($item, $filter))
						{
							continue;
						}

						try
						{
							$this->buildRow($item, $settings);
							$lines++;
						}
						catch(\Throwable $exception)
						{
							$this->getLogger(__METHOD__)->error('ElasticExportKelkooDE::log.buildRowError', [
								'error' => $exception->getMessage(),
								'line' => $exception->getLine(),
								'variation ID' => $item['id']
							]);
						}

						$this->getLogger(__METHOD__)->debug('ElasticExportKelkooDE::log.buildRowDuration', [
							'Build Row duration' => microtime(true) - $buildRowStartTime,
						]);

						if($lines == $filter['limit'])
						{
							$limitReached = true;
							break;
						}
					}
				}
			}
			while($elasticSearch->hasNext());
		}

		$this->getLogger(__METHOD__)->debug('ElasticExportKelkooDE::log.fileGenerationDuration', [
			'Whole file generation duration' => microtime(true) - $startTime,
		]);
	}

	/**
	 * @var array $item
	 * @var KeyValue $settings
	 */
	private function buildRow($item, $settings)
	{
		$deliveryCost = $this->elasticExportHelper->getShippingCost($item['data']['item']['id'], $settings);

		if(!is_null($deliveryCost))
		{
			$deliveryCost = number_format((float)$deliveryCost, 2, '.', '');
		}
		else
		{
			$deliveryCost = '';
		}

		$priceList = $this->elasticExportPriceHelper->getPriceList($item, $settings, 2, '.');

		$data = [
			'offer-id'			=> $this->elasticExportHelper->generateSku($item['id'], $settings->get('referrerId') ? $settings->get('referrerId') : self::KELKOO_DE),
			'title'		 		=> $this->elasticExportHelper->getMutatedName($item, $settings, 80),
			'product-url'	    => $this->elasticExportHelper->getMutatedUrl($item, $settings, true, false),
			'price'				=> $priceList['price'],
			'brand'				=> $this->elasticExportHelper->getExternalManufacturerName((int)$item['data']['item']['manufacturer']['id'], true),
			'description'   	=> $this->elasticExportHelper->getMutatedDescription($item, $settings, 300),
			'image-url'			=> $this->getImageByPosition($item, 0),
			'ean'				=> $this->elasticExportHelper->getBarcodeByType($item, $settings->get('barcode')),
			'merchant-category'	=> $this->elasticExportHelper->getSingleCategory((int)$item['data']['defaultCategories'][0]['id'], (string)$settings->get('lang'), (int)$settings->get('plentyId')),
			'availability'		=> $this->elasticExportHelper->getAvailability($item, $settings, false),
			'delivery-cost'		=> $deliveryCost,
			'delivery-time'		=> $this->elasticExportHelper->getAvailability($item, $settings),
			'ecotax'			=> '',
			'mpn'				=> $priceList['recommendedRetailPrice'],
			'unit-price'		=> $this->elasticExportPriceHelper->getBasePrice($item, $priceList['price']),
			'image-url-2'		=> $this->getImageByPosition($item, 1),
			'image-url-3'		=> $this->getImageByPosition($item, 2),
			'image-url-4'		=> $this->getImageByPosition($item, 3),
		];

		$this->addCSVContent(array_values($data));
	}

	/**
	 * Returns the URL of an image depending on the configured position.
	 *
	 * Fallback in case of no found image with position x to entry x in list.
	 *
	 * @param array $item
	 * @param int $position
	 * @return string
	 */
	private function getImageByPosition($item, int $position):string
	{
		$images = [];
		$count = 0;

		// prio 1 - variation images
		if(is_array($item['data']['images']['variation']) && count($item['data']['images']['variation']) > 0)
		{
			foreach($item['data']['images']['variation'] as $image)
			{
				if(!array_key_exists($image['position'], $images))
				{
					$images[$image['position']] = $image;
				}
				else
				{
					$count++;
					$images[$image['position'].'_'.$count] = $image;
				}
			}
		}

		// prio 2 - "all" images
		if(is_array($item['data']['images']['all']) && count($item['data']['images']['all']) > 0)
		{

			foreach($item['data']['images']['all'] as $image)
			{
				if(!array_key_exists($image['position'], $images))
				{
					$images[$image['position']] = $image;
				}
				else
				{
					$count++;
					$images[$image['position'].'_'.$count] = $image;
				}
			}
		}

		// sort by key and return image URL
		if(count($images))
		{
			ksort($images);
			$images = array_values($images);

			if(isset($images[$position]))
			{
				return (string)$this->elasticExportHelper->getImageUrlBySize($images[$position]);
			}
		}

		return '';
	}
}