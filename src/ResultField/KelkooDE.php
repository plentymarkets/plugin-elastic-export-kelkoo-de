<?php

namespace ElasticExportKelkooDE\ResultField;

use Plenty\Modules\DataExchange\Contracts\ResultFields;
use Plenty\Modules\DataExchange\Models\FormatSetting;
use Plenty\Modules\Helper\Services\ArrayHelper;
use Plenty\Modules\Item\Search\Mutators\BarcodeMutator;
use Plenty\Modules\Item\Search\Mutators\ImageMutator;
use Plenty\Modules\Cloud\ElasticSearch\Lib\Source\Mutator\BuiltIn\LanguageMutator;
use Plenty\Modules\Item\Search\Mutators\DefaultCategoryMutator;
use Plenty\Modules\Item\Search\Mutators\KeyMutator;

/**
 * Class KelkooDE
 *
 * @package ElasticExportKelkooDE\ResultField
 */
class KelkooDE extends ResultFields
{
    const KELKOO_DE = 6.00;

    /*
	 * @var ArrayHelper
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
     * Generate result fields.
	 *
     * @param  array $formatSettings = []
     * @return array
     */
    public function generateResultFields(array $formatSettings = []):array
    {
        $settings = $this->arrayHelper->buildMapFromObjectList($formatSettings, 'key', 'value');

        $reference = $settings->get('referrerId') ? $settings->get('referrerId') : self::KELKOO_DE;

        $itemDescriptionFields = ['texts.urlPath'];
        $itemDescriptionFields[] = 'texts.keywords';

        switch($settings->get('nameId'))
        {
            case 1:
                $itemDescriptionFields[] = 'texts.name1';
                break;
            case 2:
                $itemDescriptionFields[] = 'texts.name2';
                break;
            case 3:
                $itemDescriptionFields[] = 'texts.name3';
                break;
            default:
                $itemDescriptionFields[] = 'texts.name1';
                break;
        }

        if($settings->get('descriptionType') == 'itemShortDescription' || $settings->get('previewTextType') == 'itemShortDescription')
        {
            $itemDescriptionFields[] = 'texts.shortDescription';
        }

        if($settings->get('descriptionType') == 'itemDescription'
            || $settings->get('descriptionType') == 'itemDescriptionAndTechnicalData'
            || $settings->get('previewTextType') == 'itemDescription'
            || $settings->get('previewTextType') == 'itemDescriptionAndTechnicalData')
        {
            $itemDescriptionFields[] = 'texts.description';
        }

        $itemDescriptionFields[] = 'texts.technicalData';
        $itemDescriptionFields[] = 'texts.lang';

        // Mutator

		/**
		 * @var KeyMutator $keyMutator
		 */
		$keyMutator = pluginApp(KeyMutator::class);

		if($keyMutator instanceof KeyMutator)
		{
			$keyMutator->setKeyList($this->getKeyList());
			$keyMutator->setNestedKeyList($this->getNestedKeyList());
		}

        /**
         * @var ImageMutator $imageMutator
         */
        $imageMutator = pluginApp(ImageMutator::class);

        if($imageMutator instanceof ImageMutator)
        {
            $imageMutator->addMarket($reference);
        }

		/**
		 * @var BarcodeMutator $barcodeMutator
		 */
		$barcodeMutator = pluginApp(BarcodeMutator::class);

		if($barcodeMutator instanceof BarcodeMutator)
		{
			$barcodeMutator->addMarket($reference);
		}

        /**
         * @var LanguageMutator $languageMutator
         */
        $languageMutator = pluginApp(LanguageMutator::class, ['languages' => [$settings->get('lang')]]);

        /**
         * @var DefaultCategoryMutator $defaultCategoryMutator
         */
        $defaultCategoryMutator = pluginApp(DefaultCategoryMutator::class);

        if($defaultCategoryMutator instanceof DefaultCategoryMutator)
        {
            $defaultCategoryMutator->setPlentyId($settings->get('plentyId'));
        }

        $fields = [
            [
                //item
                'item.id',
                'item.manufacturer.id',

                //variation
                'id',
                'variation.availability.id',
                'variation.stockLimitation',
                'variation.vatId',
                'variation.model',

                //images
                'images.all.urlMiddle',
                'images.all.urlPreview',
                'images.all.urlSecondPreview',
                'images.all.url',
                'images.all.path',
                'images.all.position',

                'images.variation.urlMiddle',
                'images.variation.urlPreview',
                'images.variation.urlSecondPreview',
                'images.variation.url',
                'images.variation.path',
                'images.variation.position',

                //unit
                'unit.content',
                'unit.id',

                //defaultCategories
                'defaultCategories.id',

                //barcodes
                'barcodes.code',
                'barcodes.type',

                //attributes
                'attributes.attributeValueSetId',
            ],
            [
            	$keyMutator,
                $languageMutator,
                $defaultCategoryMutator,
				$barcodeMutator
            ],
        ];

        if($reference != -1)
        {
            $fields[1][] = $imageMutator;
        }

        foreach($itemDescriptionFields as $itemDescriptionField)
        {
            $fields[0][] = $itemDescriptionField;
        }

        return $fields;
    }

	/**
	 * @return array
	 */
	private function getKeyList()
	{
		$keyList = [
			//item
			'item.id',
			'item.manufacturer.id',
			'item.rakutenCategoryId',

			//variation
			'variation.availability.id',
			'variation.stockLimitation',
			'variation.vatId',
			'variation.model',
			'variation.isMain',
			'variation.id',

			//unit
			'unit.content',
			'unit.id',
		];

		return $keyList;
	}

	/**
	 * @return array
	 */
	private function getNestedKeyList()
	{
		$nestedKeyList['keys'] = [
			//images
			'images.all',
			'images.variation',

			//sku
			'skus',

			//texts
			'texts',

			//defaultCategories
			'defaultCategories',

			//barcodes
			'barcodes',

			//attributes
			'attributes',
		];

		$nestedKeyList['nestedKeys'] = [
			'images.all' => [
				'urlMiddle',
				'urlPreview',
				'urlSecondPreview',
				'url',
				'path',
				'position',
			],

			'images.variation' => [
				'urlMiddle',
				'urlPreview',
				'urlSecondPreview',
				'url',
				'path',
				'position',
			],

			'skus' => [
				'sku'
			],

			'texts'  => [
				'description',
				'lang',
				'name1',
				'name2',
				'name3',
				'shortDescription',
				'technicalData',
				'urlPath',
			],

			'defaultCategories' => [
				'id'
			],

			'barcodes'  => [
				'code',
				'type',
			],

			'attributes'   => [
				'attributeValueSetId',
			],

			'properties'    => [
				'property.id',
			]
		];

		return $nestedKeyList;
	}
}