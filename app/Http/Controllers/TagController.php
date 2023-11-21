<?php

namespace App\Http\Controllers;

use App\Actions\Tags\CreateTagAction;
use App\Http\Requests\Tags\CreateTagRequest;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        Return Inertia::render('Tags/Index', [
            'tags' => Tag::query()->latest()->paginate(150),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateTagRequest $request, CreateTagAction $action): JsonResponse|RedirectResponse
    {
        $model = $action->execute($request->validated());

        if ($request->wantsJson()) {
            return response()->json(['id' => $model->id]);
        }

        return to_route('tags.show', ['post' => $model->id]);
    }
}
