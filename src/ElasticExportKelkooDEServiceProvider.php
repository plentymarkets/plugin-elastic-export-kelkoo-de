<?php

namespace ElasticExportKelkooDE;

use Plenty\Modules\DataExchange\Services\ExportPresetContainer;
use Plenty\Plugin\DataExchangeServiceProvider;

/**
 * Class ElasticExportKelkooDEServiceProvider
 *
 * @package ElasticExportKelkooDE
 */
class ElasticExportKelkooDEServiceProvider extends DataExchangeServiceProvider
{
    public function register()
    {

    }

    public function exports(ExportPresetContainer $container)
    {
        $container->add(
            'KelkooDE-Plugin',
			'ElasticExportKelkooDE\ResultField\KelkooDE',
			'ElasticExportKelkooDE\Generator\KelkooDE',
            '',
            true,
			true
        );
    }
}