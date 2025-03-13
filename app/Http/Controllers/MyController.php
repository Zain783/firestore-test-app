<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Firestore;

class MyController extends Controller
{
    protected $firebase;
    protected $firestore;
    public function __construct()
    {
        $this->firebase = app('firebase');
        $this->firestore = $this->firebase->firestore();
    }

    public function add_record()
    {
        $data = [
            'name' => 'Los Angeles',
            'state' => 'CA',
            'country' => 'USA'
        ];
        
        $this->firestore->database()->collection('cities')->document('LA')->set($data);
    }
}
