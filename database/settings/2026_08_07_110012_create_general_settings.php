<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_name', 'Sekolahku');
        $this->migrator->add('general.npsn', null);
        $this->migrator->add('general.jenjang', null);
        $this->migrator->add('general.address', null);
        $this->migrator->add('general.phone', null);
        $this->migrator->add('general.email', null);
        $this->migrator->add('general.headmaster_name', null);
        $this->migrator->add('general.headmaster_greeting', null);
        $this->migrator->add('general.facebook', null);
        $this->migrator->add('general.instagram', null);
        $this->migrator->add('general.youtube', null);
        $this->migrator->add('general.twitter', null);
        $this->migrator->add('general.meta_description', null);
        $this->migrator->add('general.meta_keywords', null);
        $this->migrator->add('general.active_theme', 'default');
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('general.site_name');
        $this->migrator->deleteIfExists('general.npsn');
        $this->migrator->deleteIfExists('general.jenjang');
        $this->migrator->deleteIfExists('general.address');
        $this->migrator->deleteIfExists('general.phone');
        $this->migrator->deleteIfExists('general.email');
        $this->migrator->deleteIfExists('general.headmaster_name');
        $this->migrator->deleteIfExists('general.headmaster_greeting');
        $this->migrator->deleteIfExists('general.facebook');
        $this->migrator->deleteIfExists('general.instagram');
        $this->migrator->deleteIfExists('general.youtube');
        $this->migrator->deleteIfExists('general.twitter');
        $this->migrator->deleteIfExists('general.meta_description');
        $this->migrator->deleteIfExists('general.meta_keywords');
        $this->migrator->deleteIfExists('general.active_theme');
    }
};
