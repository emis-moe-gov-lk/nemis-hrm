<?php

namespace App\Livewire\MainTables;

use App\Models\Versions;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;

class MainTablesVersions extends Component
{
    public $showModelNewVersion = false;
    public $versionId, $version, $releaseDate, $title, $description, $isLatest = false;

    public $showModelEditVersion = false;
    public $editVersionId, $updateVersionId, $updateVersion, $updateReleaseDate, $updateTitle, $updateDescription, $updateIsLatest;

    protected function rules()
    {
        if ($this->editVersionId) {
            return [
                'updateVersionId' => [
                    'required',
                    'string',
                    'regex:/^VER\d{3,4}$/',
                    Rule::unique('versions', 'version_id')->ignore($this->editVersionId),
                ],
                'updateVersion' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('versions', 'version')->ignore($this->editVersionId),
                ],
                'updateReleaseDate' => 'required|date',
                'updateTitle' => 'required|string|max:50',
                'updateDescription' => 'required|string',
                'updateIsLatest' => 'required|boolean',
            ];
        }

        return [
            'versionId' => [
                'required',
                'string',
                'regex:/^VER\d{3,4}$/',
                Rule::unique('versions', 'version_id'),
            ],
            'version' => [
                'required',
                'string',
                'max:50',
                Rule::unique('versions', 'version'),
            ],
            'releaseDate' => 'required|date',
            'title' => 'required|string|max:50',
            'description' => 'required|string',
            'isLatest' => 'required|boolean',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function addNewVersion()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                $version = Versions::create([
                    'version_id' => $this->versionId,
                    'version' => $this->version,
                    'release_date' => $this->releaseDate,
                    'title' => $this->title,
                    'description' => $this->description,
                    'is_latest' => (bool) $this->isLatest,
                ]);

                $this->normalizeLatestVersion($version->id, (bool) $this->isLatest);
            });

            session()->flash('message', 'Version added successfully.');

            $this->showModelNewVersion = false;
            $this->resetNewVersionForm();

            return $this->redirectRoute('main-tables.versions', navigate: false);
        } catch (\Throwable $e) {
            Log::error('Version creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            session()->flash('error', 'System error: ' . $e->getMessage());
        }
    }

    public function deleteVersion($id)
    {
        $version = Versions::find($id);

        if (! $version) {
            session()->flash('error', 'Version not found.');

            return null;
        }

        try {
            DB::transaction(function () use ($version) {
                $wasLatest = (bool) $version->is_latest;

                $version->delete();

                if (! $wasLatest) {
                    return;
                }

                $fallback = $this->latestFallbackCandidate();

                if (! $fallback) {
                    return;
                }

                Versions::query()->update(['is_latest' => 0]);
                $fallback->update(['is_latest' => 1]);
            });

            session()->flash('message', 'Version deleted successfully.');

            return $this->redirectRoute('main-tables.versions', navigate: false);
        } catch (QueryException $e) {
            session()->flash('error', 'This version cannot be deleted while related change logs still exist.');
        } catch (\Throwable $e) {
            Log::error('Version delete error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            session()->flash('error', 'System error: ' . $e->getMessage());
        }

        return null;
    }

    public function toggleStatus($id)
    {
        $version = Versions::find($id);

        if (! $version) {
            session()->flash('error', 'Version not found.');

            return null;
        }

        if ($version->is_latest) {
            session()->flash('error', 'A latest version must always remain active. Mark another version as latest instead.');

            return null;
        }

        DB::transaction(function () use ($version) {
            Versions::query()->update(['is_latest' => 0]);
            $version->update(['is_latest' => 1]);
        });

        session()->flash('message', 'Latest version updated successfully.');

        return $this->redirectRoute('main-tables.versions', navigate: false);
    }

    public function editVersion($id)
    {
        $version = Versions::findOrFail($id);

        $this->editVersionId = $version->id;
        $this->updateVersionId = $version->version_id;
        $this->updateVersion = $version->version;
        $this->updateReleaseDate = $version->release_date->format('Y-m-d');
        $this->updateTitle = $version->title;
        $this->updateDescription = $version->description;
        $this->updateIsLatest = $version->is_latest;

        $this->showModelEditVersion = true;
    }

    public function updateVersionList()
    {
        $this->validate([
            'updateVersionId' => [
                'required',
                'string',
                'regex:/^VER\d{3,4}$/',
                Rule::unique('versions', 'version_id')->ignore($this->editVersionId),
            ],
            'updateVersion' => [
                'required',
                'string',
                'max:50',
                Rule::unique('versions', 'version')->ignore($this->editVersionId),
            ],
            'updateReleaseDate' => 'required|date',
            'updateTitle' => 'required|string|max:50',
            'updateDescription' => 'required|string',
            'updateIsLatest' => 'required|boolean',
        ]);

        try {
            DB::transaction(function () {
                Versions::where('id', $this->editVersionId)->update([
                    'version_id' => $this->updateVersionId,
                    'version' => $this->updateVersion,
                    'release_date' => $this->updateReleaseDate,
                    'title' => $this->updateTitle,
                    'description' => $this->updateDescription,
                    'is_latest' => (bool) $this->updateIsLatest,
                ]);

                $this->normalizeLatestVersion($this->editVersionId, (bool) $this->updateIsLatest);
            });

            $this->showModelEditVersion = false;

            session()->flash('message', 'Version updated successfully.');

            $this->resetEditVersionForm();

            return $this->redirectRoute('main-tables.versions', navigate: false);
        } catch (\Throwable $e) {
            Log::error('Version update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            session()->flash('error', 'System error: ' . $e->getMessage());
        }

        return null;
    }

    public function render()
    {
        $versions = Versions::query()
            ->orderByDesc('is_latest')
            ->orderByDesc('release_date')
            ->orderByDesc('id')
            ->paginate(50);

        return view('livewire.main-tables.main-tables-versions', compact('versions'));
    }

    protected function normalizeLatestVersion(int $versionId, bool $requestedLatest): void
    {
        if ($requestedLatest) {
            Versions::where('id', '!=', $versionId)->update(['is_latest' => 0]);
            Versions::where('id', $versionId)->update(['is_latest' => 1]);

            return;
        }

        $otherLatestExists = Versions::where('id', '!=', $versionId)
            ->where('is_latest', 1)
            ->exists();

        if ($otherLatestExists) {
            Versions::where('id', $versionId)->update(['is_latest' => 0]);

            return;
        }

        Versions::where('id', $versionId)->update(['is_latest' => 1]);
    }

    protected function latestFallbackCandidate(): ?Versions
    {
        return Versions::query()
            ->orderByDesc('release_date')
            ->orderByDesc('id')
            ->first();
    }

    protected function resetNewVersionForm(): void
    {
        $this->reset(['versionId', 'version', 'releaseDate', 'title', 'description', 'isLatest']);
        $this->isLatest = false;
    }

    protected function resetEditVersionForm(): void
    {
        $this->reset([
            'editVersionId',
            'updateVersionId',
            'updateVersion',
            'updateReleaseDate',
            'updateTitle',
            'updateDescription',
            'updateIsLatest',
        ]);

        $this->updateIsLatest = false;
    }
}
