<?php

namespace App\Http\Controllers;

use App\Contact;
use Illuminate\Http\Request;

class ContactsController extends Controller
{
    public function store()
    {
        $data = request()->validate([
            'name' => 'required',
            'email' => 'required|email',
            'birthday' => 'required|date',
            'company' => 'required',
        ]);
        Contact::create($data);
    }

    public function show($contact)
    {
        // return dd(gettype($contact));
        return $contact;
    }
}
