<?php

namespace App\Vito\Plugins\Kjjd84\VitoExtended\Http\Controllers;

use App\Enums\HostedDomainType;
use App\Http\Controllers\Controller;
use App\Models\HostedDomain;
use App\Vito\Plugins\Kjjd84\VitoExtended\Tables\PrimaryDomainsTable;
use Inertia\Inertia;
use Inertia\Response;

class PrimaryDomainsController extends Controller
{
    public function index(): Response
    {
        $project = user()->currentProject;

        $this->authorize('view', $project);

        $primaryDomains = HostedDomain::query()
            ->where('type', HostedDomainType::PRIMARY)
            ->whereHas('site.server', fn ($query) => $query->where('project_id', $project->id));

        return Inertia::render('plugins/kjjd84-vito-extended/primary-domains/index', [
            'primaryDomains' => PrimaryDomainsTable::make($primaryDomains)->simplePaginate(),
        ]);
    }
}
