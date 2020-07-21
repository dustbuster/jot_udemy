<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Contact;
use Carbon\Carbon;
use Tests\TestCase;

class ContactsTest extends TestCase
{
    use refreshDatabase;

    /** @test */
    public function a_contact_can_be_added()
    {
        $response = $this->post('/api/contacts', $this->data());
        $contact = Contact::first();
        $this->assertEquals('Test Name', $contact->name);
        $this->assertEquals('test@email.com', $contact->email);
        $this->assertEquals('10/19/1979', $contact->birthday);
        $this->assertEquals('Acme Company', $contact->company);
    }

    /** @test */
    public function fields_are_required()
    {
        collect(['name', 'email', 'birthday', 'company'])
            ->each(function ($field) {
                $response = $this->post('/api/contacts', 
                array_merge($this->data(), [$field => '']));
            
                $response->assertSessionHasErrors($field);
                $this->assertCount(0, Contact::all());
            });
    }

    /** @test */
    public function email_must_be_a_valid_email(){
        $response = $this->post('/api/contacts', 
        array_merge($this->data(), ['email' => 'not an email']));
    
        $response->assertSessionHasErrors('email');
        $this->assertCount(0, Contact::all());
    }

    /** @test */
    public function birthday_are_properly_stored(){
        $this->withoutExceptionHandling();
        $response = $this->post('/api/contacts', 
            array_merge($this->data()));
        $this->assertCount(1, Contact::all());
        $this->assertInstanceOf(Carbon::class, Contact::first()->birthday);
        $this->assertEquals('10-19-1979', Contact::first()
            ->birthday->format('m-d-Y'));
    }

    /** @test */
    public function a_contact_can_be_retreived(){
        $contact = factory(Contact::class)->create();
        $response = $this->get('/api/contacts/'. $contact->id);
        $response->assertJson([
            'name' => $contact->name,
            'email' => $contact->email,
            'birthday' => $contact->birthday,
            'company' => $contact->company,
        ]);
    }


    private function data(){
        return [
            'name' => 'Test Name',
            'email' => 'test@email.com',
            'birthday' => '10/19/1979',
            'company' => 'Acme Company',
        ];
    }
}
