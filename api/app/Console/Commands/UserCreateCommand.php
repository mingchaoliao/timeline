<?php

namespace App\Console\Commands;

use App\Timeline\Exceptions\UserNotFoundException;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentUserRepository;
use Illuminate\Console\Command;

class UserCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:user {--name= : Full name} {--email= : Email address} {--password= : Password} {--access= : Account access level, either admin or user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user';

    private $userRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(EloquentUserRepository $userRepository)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->option('name') ?? $this->ask('Full name:');
        $email = $this->option('email') ?? $this->ask('Email address');

        while (true) {
            while (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $email = $this->ask("Email \"{$email}\" is invalid. Please re-enter");
            }
            try {
                $this->userRepository->getByEmail($email);
                $email = $this->ask("Email \"{$email}\" has already existed. Please re-enter");
            } catch (UserNotFoundException $e) {
                break;
            }
        }
        $password = $this->option('password') ?? $this->secret('Password');
        $accessLevel = $this->option('access') ?? $this->choice('Access level',
                ['admin', 'user'], 1);

        $user = $this->userRepository->createUser(
            $name,
            $email,
            $password,
            $accessLevel === 'admin'
        );

        $this->info(sprintf('%s "%s" with email "%s" and ID "%d" has been created',
            $user->isAdmin() ? 'Admin account' : 'Normal user',
            $name,
            $email,
            $user->getId()
        ));
    }
}
