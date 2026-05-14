<?php

namespace App\Livewire\ChangeLog;

use Livewire\Component;
use App\Models\Versions;
use Illuminate\Support\Carbon;

class Index extends Component
{
    public $expandedVersions = [];

    public $versions;

    public function mount()
    {
        $this->versions = Versions::with('changeLogs')
        ->orderByDesc('is_latest')      // latest first
        ->orderBy('release_date', 'desc') // then ascending by date
        ->get()
        ->map(function ($version) {

            $changes = $version->changeLogs
                ->groupBy('type')
                ->map(fn ($items) => $items->pluck('description')->toArray())
                ->toArray();

            return [
                'id'        => $version->version,
                'date'      => \Carbon\Carbon::parse($version->release_date)->format('F d, Y'),
                'is_latest' => (bool) $version->is_latest,
                'changes'   => $changes,
            ];
        })
        ->toArray();
    }

    public function toggle($versionId)
    {
        if (in_array($versionId, $this->expandedVersions)) {
            $this->expandedVersions = array_diff($this->expandedVersions, [$versionId]);
        } else {
            $this->expandedVersions[] = $versionId;
        }
    }
    
    public function render()
    {
        return view('livewire.change-log.index');
    }
}
