<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class RevokeAdmin extends Command
{
    protected $signature = 'admin:revoke {email : Email address of the existing account}';

    protected $description = 'Revoke administrator access from a user account';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (!$user) {
            $this->error('No account exists with that email.');
            return self::FAILURE;
        }

        $user->forceFill(['is_admin' => false])->save();
        $this->info("Administrator access revoked from {$user->email}.");

        return self::SUCCESS;
    }
}
