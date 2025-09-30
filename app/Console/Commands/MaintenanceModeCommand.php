<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MaintenanceModeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenance {action} {--message=} {--secret=} {--render=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable or disable maintenance mode for EMS system using Laravel built-in maintenance mode';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        
        switch ($action) {
            case 'on':
            case 'enable':
                $this->enableMaintenance();
                break;
            case 'off':
            case 'disable':
                $this->disableMaintenance();
                break;
            case 'status':
                $this->checkStatus();
                break;
            default:
                $this->error('Invalid action. Use: on, off, or status');
                return 1;
        }

        return 0;
    }

    private function enableMaintenance()
    {
        $message = $this->option('message') ?: 'The EMS system is currently under maintenance. Please try again later.';
        $secret = $this->option('secret');
        $render = $this->option('render') ?: 'maintenance';

        // Generate secret if not provided
        if (!$secret) {
            $secret = 'ems-' . bin2hex(random_bytes(8));
        }

        // Build artisan down command
        $command = "php artisan down --message=\"{$message}\" --secret=\"{$secret}\"";
        
        if ($render) {
            $command .= " --render=\"{$render}\"";
        }

        // Execute the command
        exec($command, $output, $returnCode);

        if ($returnCode === 0) {
            $this->info('✅ EMS Maintenance mode enabled successfully!');
            $this->line("📝 Message: {$message}");
            $this->line("🔑 Secret: {$secret}");
            $this->line("");
            $this->comment("🔧 Developer Access:");
            $this->line("URL: yourdomain.com/{$secret}");
            $this->line("Or: yourdomain.com?secret={$secret}");
            $this->line("");
            $this->comment("📋 To disable maintenance:");
            $this->line("php artisan up");
            $this->line("Or: php artisan maintenance off");
        } else {
            $this->error('❌ Failed to enable maintenance mode!');
            $this->line("Command: {$command}");
        }
    }

    private function disableMaintenance()
    {
        // Execute php artisan up
        exec('php artisan up', $output, $returnCode);

        if ($returnCode === 0) {
            $this->info('✅ EMS Maintenance mode disabled successfully!');
        } else {
            $this->error('❌ Failed to disable maintenance mode!');
        }
    }

    private function checkStatus()
    {
        if (app()->isDownForMaintenance()) {
            $this->warn('🔧 EMS Maintenance mode is ENABLED');
            
            // Try to get maintenance data
            $maintenanceData = app('Illuminate\Foundation\Application')->maintenanceMode();
            
            if ($maintenanceData) {
                $this->line("📝 Message: " . ($maintenanceData['message'] ?? 'Default message'));
                $this->line("🔑 Secret: " . ($maintenanceData['secret'] ?? 'Not set'));
                $this->line("🎨 Render: " . ($maintenanceData['render'] ?? 'Default'));
                
                if (isset($maintenanceData['secret'])) {
                    $this->line("");
                    $this->comment("🔧 Developer Access:");
                    $this->line("URL: yourdomain.com/{$maintenanceData['secret']}");
                    $this->line("Or: yourdomain.com?secret={$maintenanceData['secret']}");
                }
            }
            
            $this->line("");
            $this->comment("📋 To disable maintenance:");
            $this->line("php artisan up");
            $this->line("Or: php artisan maintenance off");
        } else {
            $this->info('✅ EMS Maintenance mode is DISABLED');
        }
    }
}
