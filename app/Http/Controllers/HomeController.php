<?php

namespace App\Http\Controllers;

use Inertia\Response;

class HomeController extends Controller
{
    public function __construct(private readonly PostController $postController)
    {
    }

    public function home(): Response
    {
        return $this->postController->index();
    }
}
