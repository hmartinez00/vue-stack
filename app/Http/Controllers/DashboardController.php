<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:dashboard');
    }

    public function index()
    {
        $message = '¡Hola desde Laravel!'; // Aquí defines el dato
        return Inertia::render('Dashboard', [
            'myMessage' => $message, // Pasas el dato al componente
        ]);
    }
}
