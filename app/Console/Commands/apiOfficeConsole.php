<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use GuzzleHttp\Client;
use Tests\TestCase;
use Illuminate\Http\Client\Request;


class apiOfficeConsole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apiOffices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute all api actions avaliable in this application';

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
     * @return int
     */
    public function handle()
    {
        $api_Url = 'http://localhost:8080/api/';

        $option = $this->menu('Api Office')
            ->addOption('Stop all services Docker', 'Stop all services docker')
            ->addOption('Run docker composer', 'Run docker composer')
            ->addOption('Show office', 'Show  office')
            ->addOption('Show all offices', 'Show all offices')
            ->addOption('update office', 'update office')
            ->addOption('new office', 'new office')
            ->addOption('delete office', 'delete office')
            ->setWidth(150)
            ->open();

            /*DOCKER OPERATIONS START/STOP*/
            $this->info("You have chosen the option: $option");
            if($option=='Run docker composer'){
                $output =shell_exec('docker-compose up -d');
            }
            if($option=='Stop all services Docker'){
                $output = shell_exec('docker-compose down');
            }

            /*READ OPERATIONS*/
            if($option=='Show all offices'){
                $client = new Client();
                $response = $client->request('GET', $api_Url.'offices');
                $responseBody = json_decode(json_decode($response->getBody(),true)['message']);

                foreach ($responseBody as $lineOffice){
                    echo $lineOffice->id.' '.$lineOffice->name.' '.$lineOffice->address.PHP_EOL;
                }
            }
            if($option=='Show office'){
                $id = readline("Id Office : ");
                $httpClient = Http::get($api_Url.'getOffice?id='.$id);
                echo $httpClient->body();
            }
            if($option=='update office'){
                $name = readline("Name : ");
                $address = readline("Address : ");
                $id = readline("id office to update : ");
                $dataToPost = array('id'=>$id,'name'=>$name,'address'=>$address);

                $httpClient = Http::post($api_Url.'updateOffice',$dataToPost);
                echo $httpClient->body();
            }
            if($option=='new office'){
                $name = readline("Name : ");
                $address = readline("Address : ");

                $dataToPost = array('name'=>$name,'address'=>$address);
                $httpClient = Http::post($api_Url.'newOffice',$dataToPost);

                echo $httpClient->body();
            }
            if($option=='delete office'){
                $id = readline("id office to delete : ");

                $dataToPost = array('id'=>$id);
                $httpClient = Http::post($api_Url.'deleteOffice',$dataToPost);

                echo $httpClient->body();
            }

            exit(0);
    }
}
