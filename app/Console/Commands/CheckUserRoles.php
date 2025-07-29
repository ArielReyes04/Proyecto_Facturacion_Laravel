<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckUserRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:user-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check user roles and permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::with('roles')->get();
        
        foreach ($users as $user) {
            $this->info("User: {$user->name} ({$user->email})");
            $this->info("Roles: " . $user->roles->pluck('name')->join(', '));
            $this->info("Has Administrador role: " . ($user->hasRole('Administrador') ? 'Yes' : 'No'));
            $this->info("Is active: " . ($user->is_active ? 'Yes' : 'No'));
            $this->info("---");
        }
    }
}
