<?php

namespace App\Vito\Plugins\Kjjd84\VitoExtended\Support;

use Illuminate\Support\Facades\File;

class PublishesSidebarNav
{
    private const string MARKER = 'kjjd84-vito-extended:main-nav';

    private const string IMPORT = "import { vitoExtendedMainNavItems } from '@/pages/plugins/kjjd84-vito-extended/components/nav-items'; // ".self::MARKER;

    public function publishIfMissing(): void
    {
        if (! $this->isPublished()) {
            $this->publish();
        }
    }

    public function publish(): void
    {
        $sidebar = File::get($this->sidebarPath());

        $legacyImport = "import { vitoExtendedMainNavItems } from '@/components/kjjd84-vito-extended/main-nav-items'; // ".self::MARKER;

        if (str_contains($sidebar, $legacyImport)) {
            $sidebar = str_replace($legacyImport, self::IMPORT, $sidebar);
            File::put($this->sidebarPath(), $sidebar);

            $legacyComponentDir = resource_path('js/components/kjjd84-vito-extended');
            if (File::isDirectory($legacyComponentDir)) {
                File::deleteDirectory($legacyComponentDir);
            }

            return;
        }

        if (str_contains($sidebar, self::MARKER)) {
            return;
        }

        $sidebar = str_replace(
            "import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';",
            "import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';\n".self::IMPORT,
            $sidebar,
        );

        $sidebar = str_replace(
            "    {\n      title: 'Domains',\n      href: route('domains'),\n      icon: Globe,\n    },\n    {\n      title: 'Settings',",
            "    {\n      title: 'Domains',\n      href: route('domains'),\n      icon: Globe,\n    },\n    ...vitoExtendedMainNavItems(),\n    {\n      title: 'Settings',",
            $sidebar,
        );

        File::put($this->sidebarPath(), $sidebar);
    }

    public function unpublish(): void
    {
        $legacyComponentDir = resource_path('js/components/kjjd84-vito-extended');
        if (File::isDirectory($legacyComponentDir)) {
            File::deleteDirectory($legacyComponentDir);
        }

        if (! File::exists($this->sidebarPath())) {
            return;
        }

        $sidebar = File::get($this->sidebarPath());

        if (! str_contains($sidebar, self::MARKER)) {
            return;
        }

        $sidebar = preg_replace(
            '/\nimport \{ vitoExtendedMainNavItems \} from \'@\/pages\/plugins\/kjjd84-vito-extended\/components\/nav-items\'; \/\/ '.self::MARKER.'/',
            '',
            $sidebar,
        ) ?? $sidebar;

        $sidebar = preg_replace(
            '/\nimport \{ vitoExtendedMainNavItems \} from \'@\/components\/kjjd84-vito-extended\/main-nav-items\'; \/\/ '.self::MARKER.'/',
            '',
            $sidebar,
        ) ?? $sidebar;

        $sidebar = str_replace("    ...vitoExtendedMainNavItems(),\n", '', $sidebar);

        File::put($this->sidebarPath(), $sidebar);
    }

    private function isPublished(): bool
    {
        return File::exists($this->sidebarPath())
            && str_contains(File::get($this->sidebarPath()), self::MARKER);
    }

    private function sidebarPath(): string
    {
        return resource_path('js/components/app-sidebar.tsx');
    }
}
