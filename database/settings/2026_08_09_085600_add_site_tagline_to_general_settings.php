<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_tagline', null);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('general.site_tagline');
    }
};
