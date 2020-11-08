<?php

namespace Tests\Feature;

use GuzzleHttp\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Client\Request;
use League\Flysystem\Config;
use Tests\TestCase;

class apiOfficesTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public $api_Url = 'http://localhost:8080/api/';
    public function testExample()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /*
     * Call getOffices and await for 201 reply*/
    public function testGetAllOffices(){
        $response = $this->json('GET', $this->api_Url.'offices');
        $response->dump();
        $response->assertStatus(201);
    }


    public function testInsertNewOfficeParamsOk(){}
    public function testInsertNewOfficeParamsFail(){}

    public function testUpdateNewOfficeParamsOk(){}
    public function testUpdateNewOfficeParamsFail(){}

    public function testDeleteOfficeParamsOk(){
            $response = $this->json('POST', $this->api_Url.'deleteOffice', ['id' => '15']);
            $response
                ->assertStatus(201);
    }
    public function testDeleteOfficeParamsFail(){}

}
