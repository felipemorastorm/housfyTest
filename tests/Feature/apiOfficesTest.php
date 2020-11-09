<?php

namespace Tests\Feature;

use App\Models\Offices;
use Faker\Factory as Faker;
use GuzzleHttp\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Redis as Redis;
use League\Flysystem\Config;
use Tests\TestCase;

/**
 * Class apiOfficesTest
 * @package Tests\Feature
 */
class apiOfficesTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    //define api base url for calls
    public $api_Url = 'http://localhost:8080/api/';

    public function testExample()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /*
     * Call getOffices and await for 201 reply*/
    public function testGetAllOfficesFromDDBB(){
        //try delete redis cache
        //call to show all offices
        $response = $this->json('GET', $this->api_Url.'offices');
        $response->dump();
        $response->assertStatus(201);
    }
    public function testGetAllOfficesFromCache(){
        $response = $this->json('GET', $this->api_Url.'offices');
        $response->dump();
        $response->assertStatus(201);
    }


    public function testInsertNewOfficeParamsOk(){
        //create the new value
        $faker = Faker::create();
        $newOffice = Offices::create(array('name' => $faker->name(), 'address' => $faker->address()));

        //launch insertion api action
        $dataToPost = array('name'=>$newOffice->name,'address'=>$newOffice->address);
        $response = $this->json('POST', $this->api_Url.'newOffice',$dataToPost);
        $response->assertStatus(201);

        //delete created
        Offices::find($newOffice->id)->delete();
    }


    public function testInsertNewOfficeParamsFail(){
        //create the new value
        $faker = Faker::create();
        $newOffice = Offices::create(array('name' => '', 'address' => ''));

        //launch insertion api action with empty values for name and address
        $dataToPost = array('name'=>$newOffice->name,'address'=>$newOffice->address);
        $response = $this->json('POST', $this->api_Url.'newOffice',$dataToPost);
        $response->assertStatus(422);

        $newOffice = Offices::create(array('name' => '', 'address' => ''));

        //launch insertion api action with empty values for name empty
        $dataToPost = array('name'=>'','address'=>$newOffice->address);
        $response = $this->json('POST', $this->api_Url.'newOffice',$dataToPost);
        $response->assertStatus(422);

        $newOffice = Offices::create(array('name' => '', 'address' => ''));

        //launch insertion api action with empty values for  address empty
        $dataToPost = array('name'=>$newOffice->name,'address'=>'');
        $response = $this->json('POST', $this->api_Url.'newOffice',$dataToPost);
        $response->assertStatus(422);
    }

    public function testUpdateNewOfficeParamsOk(){
        $office = null;
        //find for a valid id
        while($office==null) {
            $id = rand(1,50);
            $office = Offices::find($id);
        }
        $name = $office->name;

        $address = $office->address;

        //launch update values
        $dataToPost = array('id'=>$office->id,'name'=>'testName','address'=>'testAddress');
        $response = $this->json('POST', $this->api_Url.'updateOffice',$dataToPost);
        $response->assertStatus(201);

        $officeUpdated = Offices::find($id);
        $this->assertEquals($officeUpdated->name, 'testName');
        $this->assertEquals($officeUpdated->address, 'testAddress');

        //recover update in ddbb
        $reUpdateOffice = new Offices();
        $reUpdateOffice->id = $id;
        $reUpdateOffice->name = $name;
        $reUpdateOffice->address = $address;
        $reUpdateOffice->update();

        $this->assertNotEquals($reUpdateOffice->name, 'testName',$office->name.' --testName for id:'.$office->id);
        $this->assertNotEquals($reUpdateOffice->address, 'testAddress',$office->address.' --testAddress for id:'.$office->id);
    }
    public function testUpdateNewOfficeParamsFail(){
        $office = null;
        //find for a valid id
        while($office==null) {
            $id = rand(1,50);
            $office = Offices::find($id);
        }

        $name = $office->name;
        $address = $office->address;

        //launch update values empty name
        $dataToPost = array('id'=>$office->id,'name'=>'','address'=>'testAddress');
        $response = $this->json('POST', $this->api_Url.'updateOffice',$dataToPost);
        $response->assertStatus(422);

        //launch update values empty name
        $dataToPost = array('id'=>$office->id,'name'=>'','address'=>'testAddress');
        $response = $this->json('POST', $this->api_Url.'updateOffice',$dataToPost);
        $response->assertStatus(422);

        //launch update values empty name
        $dataToPost = array('id'=>'','name'=>'','address'=>'');
        $response = $this->json('POST', $this->api_Url.'updateOffice',$dataToPost);
        $response->assertStatus(422);


    }

    public function testDeleteOfficeParamsOk(){
            //create new office and just delete
            $faker = Faker::create();
            $newOffice = Offices::create(array('name' => $faker->name(), 'address' => $faker->address()));
            $newOffice->save();
            //verify saved in dddb
            $office = Offices::find($newOffice->id);
            $this->assertNotNull($office,'office with name: '.$office->name.' saved in ddbb');
            $dataTopost = array('id'=>$newOffice->id);
            //new office created and saved in ddbb, launch delete api operation
            $response = $this->json('POST', $this->api_Url.'deleteOffice', $dataTopost);
            $response->assertStatus(201);
            //action delete launched , verify that is not in ddbb
            $office = Offices::find($newOffice->id);
            $this->assertNull($office);
        }
    public function testDeleteOfficeIdEmptyFail(){
            //create new office and just delete
            $dataTopost = array('id'=>'');
            //try deletion with id empty
            $response = $this->json('POST', $this->api_Url.'deleteOffice', $dataTopost);
            $response->assertStatus(422);
        }
    public function testDeleteOfficeIdStringFail(){
            //create new office and just delete
            $dataTopost = array('id'=>'text');
            //try deletion with id empty
            $response = $this->json('POST', $this->api_Url.'deleteOffice', $dataTopost);
            $response->assertStatus(422);
    }
    public function testDeleteOfficeIdDontExistFail(){
        //create new office and just delete
        $officeNotExist = true;
        while($office!=null) {
            $id = rand(0, 2000);
            $office = Offices::find($id);
        }
        //id dont found, try delete
        $dataTopost = array('id'=>$id);
        //try deletion with id empty
        $response = $this->json('POST', $this->api_Url.'deleteOffice', $dataTopost);
        $response->assertStatus(422);
    }

}
