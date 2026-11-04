<?php declare(strict_types=1);

namespace App\Model\Settings;

use App\Model\Settings\SeoSettings;
use App\Model\Settings\ShopInfo;
use App\Model\Settings\SystemSettings;

final class AppSettings
{
    public readonly ShopInfo $info;
    public readonly SeoSettings $seo;
    public readonly SystemSettings $system;

    /**
     * @param array{company: string, name: string, fullName: string, url: string, street: string,
     *     city: string, zip: string, phone: string, mobile: string, email: string, ico: string,
     *     location: array{lon: float, lat: float}
     * } $info
     * @param array{title: string, keywords: string, description: string} $seo
     * @param array{author: string, email: string, yearStart: int, debugMode: bool} $system
     */
    public function __construct(
        array $info,
        array $seo,
        array $system,
    ) {
        $this->info = new ShopInfo(...$info);
        $this->seo = new SeoSettings(...$seo);
        $this->system = new SystemSettings(...$system);
    }
}
