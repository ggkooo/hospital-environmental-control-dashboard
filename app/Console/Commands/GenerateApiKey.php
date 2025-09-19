<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateApiKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api-key:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera uma chave de API aleatória e atualiza o .env';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $key = Str::random(32);
        $this->info('Nova chave de API gerada: ' . $key);

        $envPath = base_path('.env');
        if (file_exists($envPath)) {
            $env = file_get_contents($envPath);
            if (preg_match('/^API_KEY=.*$/m', $env)) {
                $env = preg_replace('/^API_KEY=.*$/m', 'API_KEY=' . $key, $env);
            } else {
                $env .= "\nAPI_KEY=$key\n";
            }
            file_put_contents($envPath, $env);
            $this->info('Arquivo .env atualizado com a nova chave.');
        } else {
            $this->warn('Arquivo .env não encontrado. Adicione manualmente: API_KEY=' . $key);
        }
    }
}

