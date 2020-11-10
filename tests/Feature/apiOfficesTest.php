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

    public $api_Url = 'http://localhost:8080/api/';

    /**
     *
     */
    public function testBasePath()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    /**
     * Api show office by id , await for 201 response
     */
    public function testGetOfficeWithParamsOk(){
        $office = null;
        //find for a valid id
        while($office==null) {
            $id = rand(1,50);
            $office = Offices::find($id);
        }
        $response = $this->json('GET', $this->api_Url.'getOffice',array('id'=>$office->id));
        $response->assertStatus(201);
    }
    /**
     * Api show office by id , await for 201 response
     */
    public function testGetOfficeWithParamsFail(){
        //try get operation string
        $response = $this->json('GET', $this->api_Url.'getOffice',array('id'=>'string'));
        $response->assertStatus(422);
        //try get operation id=0
        $response = $this->json('GET', $this->api_Url.'getOffice',array('id'=>0));
        $response->assertStatus(422);
        //try get operation id not exist
        $response = $this->json('GET', $this->api_Url.'getOffice',array('id'=>3093824982));
        $response->assertStatus(422);
    }



    /**
     * Api show all offices , await for 201 response
     */
    public function testGetAllOfficesFromDDBB(){
        //try delete redis cache
        //call to show all offices
        $response = $this->json('GET', $this->api_Url.'offices');
        $response->assertStatus(201);
    }


    /**
     * Api call to new insertion office , generate name and address via faker
     */
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


    /**
     * Test insertion new office with params not valid
     */
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

    /**
     * Try update with valid params and verify if value in ddbb changed
     */
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

    /**
     * Call update action api with params not accepted
     */
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

    /**
     *  Call api for Delete office, verify that values in ddbb are ok
     */
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

    /**
     * Call api delete office  with a id empty
     */
    public function testDeleteOfficeIdEmptyFail(){
            //create new office and just delete
            $dataTopost = array('id'=>'');
            //try deletion with id empty
            $response = $this->json('POST', $this->api_Url.'deleteOffice', $dataTopost);
            $response->assertStatus(422);
        }

    /**
     * Call api delete office with a string in param id
     */
    public function testDeleteOfficeIdStringFail(){
            //create new office and just delete
            $dataTopost = array('id'=>'text');
            //try deletion with id empty
            $response = $this->json('POST', $this->api_Url.'deleteOffice', $dataTopost);
            $response->assertStatus(422);
    }

    /**
     *  Call api delete office with id not in ddbb, id null ,id not exist in ddbb,  id null and id string
     */
    public function testDeleteOfficeIdDontExistFail(){

        //try delete id empty
        $dataTopost = array('id'=>null);
        //try deletion with id empty
        $response = $this->json('POST', $this->api_Url.'deleteOffice', $dataTopost);
        $response->assertStatus(422);

        //try delete id not exist
        $dataTopost = array('id'=>787897980787878);
        //try deletion with id empty
        $response = $this->json('POST', $this->api_Url.'deleteOffice', $dataTopost);
        $response->assertStatus(422);

        //try delete id empty
        $dataTopost = array('id'=>'');
        //try deletion with id empty
        $response = $this->json('POST', $this->api_Url.'deleteOffice', $dataTopost);
        $response->assertStatus(422);

        //try delete id string
        $dataTopost = array('id'=>'id_isstring');
        //try deletion with id empty
        $response = $this->json('POST', $this->api_Url.'deleteOffice', $dataTopost);
        $response->assertStatus(422);
    }

}
