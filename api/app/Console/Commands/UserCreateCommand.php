<?php

namespace App\Console\Commands;

use App\Timeline\Domain\Repositories\UserRepository;
use App\Timeline\Domain\ValueObjects\Email;
use App\Timeline\Exceptions\TimelineException;
use Illuminate\Console\Command;

class UserCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:user {--name= : Full name} {--email= : Email address} {--password= : Password} {--access=user : Account access level, either admin, editor or user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user';

    /** @var UserRepository */
    private $userRepository;

    /**
     * Create a new command instance.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
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

        while (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = $this->ask("Email \"{$email}\" is invalid. Please re-enter");
        }
        $password = $this->option('password') ?? $this->secret('Password');
        $accessLevel = $this->option('access') ?? $this->choice('Access level',
                ['admin', 'editor', 'user'], 1);

        try {
            $user = $this->userRepository->create(
                $name,
                new Email($email),
                $password,
                $accessLevel === 'admin',
                $accessLevel === 'editor'
            );

            $this->info(sprintf('%s "%s" with email "%s" and ID "%d" has been created',
                $user->isAdmin() ? 'Admin account' : 'Normal user',
                $name,
                $email,
                $user->getId()
            ));
        } catch (TimelineException $e) {
            $this->error($e->getMessage());
        }
    }
}
