<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $site_name;

    public ?string $npsn;

    public ?string $jenjang;

    public ?string $address;

    public ?string $phone;

    public ?string $email;

    public ?string $headmaster_name;

    public ?string $headmaster_greeting;

    public ?string $facebook;

    public ?string $instagram;

    public ?string $youtube;

    public ?string $twitter;

    public ?string $meta_description;

    public ?string $meta_keywords;

    public string $active_theme;

    public static function group(): string
    {
        return 'general';
    }
}
