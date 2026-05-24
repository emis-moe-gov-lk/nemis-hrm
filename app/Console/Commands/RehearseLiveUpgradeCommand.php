<?php

namespace App\Console\Commands;

use App\Services\DatabaseUpgrade\LiveUpgradeRehearsalService;
use Illuminate\Console\Command;

class RehearseLiveUpgradeCommand extends Command
{
    protected $signature = 'db:rehearse-live-upgrade
        {target : Fresh rehearsal database name, for example cemisold_rehearsal_01}
        {--source=cemisold : Source live-derived database to clone}
        {--reference=cemisnew : Reference upgraded database used for schema comparison}
        {--connection=mysql : Base Laravel connection to reuse for the rehearsal}
        {--fresh : Drop the target database first if it already exists}
        {--dry-run : Validate inputs and write a preflight report without cloning or migrating}
        {--keep-failed-target : Keep the failed rehearsal clone for manual inspection}
        {--patch-dir= : Directory containing optional table-specific patch files}
        {--report-dir= : Directory where rehearsal reports should be written}';

    protected $description = 'Clone a live-derived database, reconcile legacy migration drift, run migrations, seed reference data, and write a rehearsal report.';

    public function __construct(protected LiveUpgradeRehearsalService $service)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $patchDirectory = $this->option('patch-dir')
            ?: base_path('database/live-upgrade/patches');
        $reportDirectory = $this->option('report-dir')
            ?: storage_path('app/testing/live-upgrade/reports');

        $this->line('Preparing live upgrade rehearsal...');
        $this->line('Source: '.$this->option('source'));
        $this->line('Target: '.$this->argument('target'));
        $this->line('Reference: '.$this->option('reference'));

        $report = $this->service->rehearse([
            'connection' => (string) $this->option('connection'),
            'source' => (string) $this->option('source'),
            'target' => (string) $this->argument('target'),
            'reference' => (string) $this->option('reference'),
            'fresh' => (bool) $this->option('fresh'),
            'dry_run' => (bool) $this->option('dry-run'),
            'keep_failed_target' => (bool) $this->option('keep-failed-target'),
            'patch_dir' => $patchDirectory,
            'report_dir' => $reportDirectory,
        ]);

        $this->newLine();
        $this->line('JSON report: '.$report['report_paths']['json']);
        $this->line('Markdown report: '.$report['report_paths']['markdown']);

        foreach ($report['commands'] as $command) {
            $status = $command['exit_code'] === 0 ? 'OK' : 'FAIL';
            $this->line(sprintf(
                '[%s] %s',
                $status,
                $command['command']
            ));
        }

        if ($report['status'] === 'dry-run') {
            $this->info('Dry run completed. No databases were modified.');

            return self::SUCCESS;
        }

        if ($report['status'] === 'success') {
            $failures = $report['validation']['failures'] ?? [];
            $warnings = $report['validation']['warnings'] ?? [];

            foreach ($warnings as $warning) {
                $this->warn($warning);
            }

            if ($failures !== []) {
                foreach ($failures as $failure) {
                    $this->error($failure);
                }

                return self::FAILURE;
            }

            $this->info('Live upgrade rehearsal completed successfully.');
            $this->line('Next step: point the local app at the rehearsal DB and run the manual smoke checklist in the report.');

            return self::SUCCESS;
        }

        $this->error($report['error'] ?? 'The rehearsal failed.');

        if (! empty($report['cleanup'])) {
            $this->warn($report['cleanup']);
        }

        if (! empty($report['drift'])) {
            foreach ($report['drift'] as $entry) {
                $this->warn(sprintf(
                    'Schema drift: %s via %s',
                    $entry['table'],
                    $entry['migration']
                ));
            }
        }

        $failures = $report['validation']['failures'] ?? [];

        foreach ($failures as $failure) {
            $this->error($failure);
        }

        return self::FAILURE;
    }
}
