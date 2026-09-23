<?php

namespace App\Vito\Plugins\Kjjd84\VitoExtended\Support;

use Illuminate\Support\Facades\File;

class PublishesInertiaPages
{
    private const string PAGE_NAMESPACE = 'plugins/kjjd84-vito-extended';

    public function publishIfMissing(): void
    {
        if (! File::isDirectory($this->destination())) {
            $this->publish();
        }
    }

    public function publish(): void
    {
        File::ensureDirectoryExists(dirname($this->destination()));
        File::copyDirectory($this->source(), $this->destination());
    }

    public function unpublish(): void
    {
        if (File::isDirectory($this->destination())) {
            File::deleteDirectory($this->destination());
        }
    }

    private function source(): string
    {
        return __DIR__.'/../resources/js/pages/'.self::PAGE_NAMESPACE;
    }

    private function destination(): string
    {
        return resource_path('js/pages/'.self::PAGE_NAMESPACE);
    }
}
