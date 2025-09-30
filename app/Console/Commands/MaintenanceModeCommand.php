<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MaintenanceModeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenance {action} {--message=} {--eta=} {--email=} {--phone=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable or disable maintenance mode for EMS system';

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
        $envFile = base_path('.env');
        
        if (!File::exists($envFile)) {
            $this->error('.env file not found!');
            return;
        }

        $envContent = File::get($envFile);
        
        // Get options
        $message = $this->option('message') ?: 'We are currently performing scheduled maintenance on the Employee Management System. Please try again later.';
        $eta = $this->option('eta') ?: '2-3 hours';
        $email = $this->option('email') ?: 'ems-support@company.com';
        $phone = $this->option('phone') ?: '+62 21 1234 5678';

        // Update or add maintenance mode settings
        $maintenanceSettings = [
            'MAINTENANCE_MODE=true',
            'MAINTENANCE_MESSAGE="' . addslashes($message) . '"',
            'MAINTENANCE_ETA="' . addslashes($eta) . '"',
            'MAINTENANCE_CONTACT_EMAIL="' . addslashes($email) . '"',
            'MAINTENANCE_CONTACT_PHONE="' . addslashes($phone) . '"'
        ];

        foreach ($maintenanceSettings as $setting) {
            $key = explode('=', $setting)[0];
            
            if (preg_match("/^{$key}=.*/m", $envContent)) {
                // Update existing setting
                $envContent = preg_replace("/^{$key}=.*/m", $setting, $envContent);
            } else {
                // Add new setting
                $envContent .= "\n{$setting}";
            }
        }

        File::put($envFile, $envContent);

        $this->info('✅ EMS Maintenance mode enabled successfully!');
        $this->line("📝 Message: {$message}");
        $this->line("⏰ ETA: {$eta}");
        $this->line("📧 Contact: {$email}");
        $this->line("📱 Phone: {$phone}");
        $this->warn('⚠️  Remember to run: php artisan config:clear');
    }

    private function disableMaintenance()
    {
        $envFile = base_path('.env');
        
        if (!File::exists($envFile)) {
            $this->error('.env file not found!');
            return;
        }

        $envContent = File::get($envFile);
        
        // Remove maintenance mode settings
        $maintenanceKeys = [
            'MAINTENANCE_MODE',
            'MAINTENANCE_MESSAGE',
            'MAINTENANCE_ETA',
            'MAINTENANCE_CONTACT_EMAIL',
            'MAINTENANCE_CONTACT_PHONE'
        ];

        foreach ($maintenanceKeys as $key) {
            $envContent = preg_replace("/^{$key}=.*\n?/m", '', $envContent);
        }

        File::put($envFile, $envContent);

        $this->info('✅ EMS Maintenance mode disabled successfully!');
        $this->warn('⚠️  Remember to run: php artisan config:clear');
    }

    private function checkStatus()
    {
        $envFile = base_path('.env');
        
        if (!File::exists($envFile)) {
            $this->error('.env file not found!');
            return;
        }

        $envContent = File::get($envFile);
        
        $isEnabled = strpos($envContent, 'MAINTENANCE_MODE=true') !== false;
        
        if ($isEnabled) {
            $this->warn('🔧 EMS Maintenance mode is ENABLED');
            
            // Extract settings
            preg_match('/MAINTENANCE_MESSAGE="([^"]*)"/', $envContent, $message);
            preg_match('/MAINTENANCE_ETA="([^"]*)"/', $envContent, $eta);
            preg_match('/MAINTENANCE_CONTACT_EMAIL="([^"]*)"/', $envContent, $email);
            preg_match('/MAINTENANCE_CONTACT_PHONE="([^"]*)"/', $envContent, $phone);
            
            $this->line("📝 Message: " . ($message[1] ?? 'Default message'));
            $this->line("⏰ ETA: " . ($eta[1] ?? 'Not set'));
            $this->line("📧 Email: " . ($email[1] ?? 'Not set'));
            $this->line("📱 Phone: " . ($phone[1] ?? 'Not set'));
        } else {
            $this->info('✅ EMS Maintenance mode is DISABLED');
        }
    }
}
