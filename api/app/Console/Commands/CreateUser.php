<?php

namespace App\Console\Commands;

use App\Timeline\Domain\Repositories\UserRepository;
use App\Timeline\Domain\ValueObjects\Email;
use Illuminate\Support\Facades\Log;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:user {--name= : Full name} {--email= : Email address} {--password= : Password} {--access= : Account access level, either admin, editor or user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user';

    /**
     * Create a new command instance.
     *
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
        $name = $this->option('name') ?? $this->askWithValidation(
                'Full name:',
                function (?string $answer) {
                    return $answer !== null;
                },
                'Name is required!'
            );
        $email = $this->option('email') ?? $this->askWithValidation(
                'Email address',
                function (?string $answer) {
                    return $answer !== null;
                },
                'Email is required!'
            );
        $password = $this->option('password') ?? $this->askWithValidation(
                'Password',
                function (?string $answer) {
                    return $answer !== null && strlen($answer) >= 8;
                },
                'Password must be greater than 8 character length!',
                true
            );
        $accessLevel = $this->option('access') ?? $this->choice('Access level',
                ['admin', 'editor', 'user'], 'user');

        try {
            $user = $userRepository->create(
                $name,
                new Email($email),
                $password,
                $accessLevel === 'admin',
                $accessLevel === 'editor'
            );

            $this->info(sprintf('%s "%s" with email "%s" and ID "%d" has been created',
                $user->isAdmin() ? 'Admin account' : ($user->isEditor() ? 'Editor account' : 'User account'),
                $name,
                $email,
                $user->getId()->getValue()
            ));

            Log::notice('user created (CLI)', $user->toValueArray());
        } catch (\Exception $e) {
            $message = sprintf(
                'failed to create user. Reason: %s',
                $e->getMessage()
            );
            $this->error($message);
            Log::error($message . ' (CLI)');
            return 1;
        }

        return 0;
    }
}
