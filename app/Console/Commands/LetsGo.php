<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class LetsGo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:lets-go {--fresh : Drop all tables and re-run all migrations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup the application with super admin and 10 tenants';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Starting application setup...');
        $this->newLine();

        // Check if --fresh flag is provided
        if ($this->option('fresh')) {
            if ($this->confirm('This will drop all tables and re-run migrations. Are you sure?', false)) {
                $this->info('Running migrations:fresh...');
                Artisan::call('migrate:fresh', [], $this->getOutput());
                $this->info('✓ Migrations completed');
                $this->newLine();
            } else {
                $this->warn('Operation cancelled.');

                return Command::FAILURE;
            }
        } else {
            $this->info('Running migrations...');
            Artisan::call('migrate', [], $this->getOutput());
            $this->info('✓ Migrations completed');
            $this->newLine();
        }

        // Run seeders
        $this->info('Seeding database...');
        Artisan::call('db:seed', [], $this->getOutput());
        $this->newLine();

        // Display success message
        $this->info('✓ Application setup completed successfully!');
        $this->newLine();

        $this->comment('═══════════════════════════════════════');
        $this->info('Login Credentials:');
        $this->comment('═══════════════════════════════════════');
        $this->line('Email:    admin@admin.com');
        $this->line('Password: password');
        $this->comment('═══════════════════════════════════════');
        $this->newLine();

        $this->info('10 tenants have been created:');
        $this->line('- tenant_001 to tenant_010');
        $this->line('- Each with domain: tenant1.localhost, tenant2.localhost, etc.');
        $this->newLine();

        return Command::SUCCESS;
    }
}
