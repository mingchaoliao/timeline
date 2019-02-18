<?php

namespace App\Console\Commands;

use App\Timeline\Domain\Repositories\UserRepository;
use App\Timeline\Domain\ValueObjects\Email;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ResetUserPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:reset-password {email : Email Address}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset user\'s password.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param UserRepository $userRepository
     * @return mixed
     */
    public function handle(UserRepository $userRepository)
    {
        try {
            $email = Email::createFromString($this->argument('email'));
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }

        $user = $userRepository->getByEmail($email);

        if ($user === null) {
            $this->error(sprintf('User with email "%s" does not exist.', (string)$email));
            return 1;
        }

        $isConfirmed = $this->choice(sprintf(
                'User with email "%s" (ID: %s, name: %s) exist. Are you sure you want to reset password for this user?',
                (string)$email,
                (string)$user->getId(),
                $user->getName()
            ), ['yes', 'no'], 'no') === 'yes';

        if ($isConfirmed === false) {
            $this->info(sprintf(
                'Password reset request is cancelled. Password of user "%s" will remain unchange.',
                (string)$email
            ));
            return 0;
        }

        $randomPassword = str_random(8);

        try {
            $user = $userRepository->update($user->getId(), null, $randomPassword);
        } catch (\Exception $e) {
            $message = sprintf('failed to reset password for user "%s"', (string)$email);
            $this->error($message);
            Log::error($message . ' (CLI)');
            return 1;
        }

        $this->info(sprintf(
            'Password has been reset. New password is: %s',
            $randomPassword
        ));

        Log::notice('Password reset completed (CLI)', $user->toValueArray());

        return 0;
    }
}
