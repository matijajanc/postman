<?php

declare(strict_types=1);

namespace Matijajanc\Postman\Commands;

use Illuminate\Console\Command;
use Matijajanc\Postman\Postman;

class PostmanGenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'postman:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate postman files';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        private Postman $postman
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->postman->generateEnvironmentData();
        $this->postman->generatePostmanJson();

        $this->error('Postman files generated.');
    }
}
