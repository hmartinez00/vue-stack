<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\User; // Asegúrate de importar el modelo

class MyNewPageController extends Controller
{
    /**
     * Muestra la vista de mi nueva página.
     */
    public function index()
    {
        // Preparamos los datos
        $message = '¡Hola desde Mi nueva página!';
        $users = User::all(); // Obtenemos todos los usuarios

        // Pasamos los datos a la vista en un solo arreglo
        return Inertia::render('MyNewPage', [
            'myMessage' => $message,
            'usersList' => $users, // La clave 'usersList' se convertirá en una prop
        ]);
    }
}
