<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ImageController as WebImageController;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ImageController extends Controller
{
    public function index(Request $request)
    {
        $images = $request->user()->images()->latest()->get();

        if ($request->query('format') === 'html') {
            return $this->html(Image::html($images));
        }

        return $images->map(fn ($image) => $this->present($image));
    }

    public function store(Request $request)
    {
        $files = $request->file('images') ?? array_filter([$request->file('image')]);

        Validator::make(['images' => $files], [
            'images' => 'required|array',
            'images.*' => WebImageController::RULES,
        ])->validate();

        $images = collect($files)->map(fn ($file) => Image::store($request->user(), $file));

        if ($request->query('format') === 'json' || $request->wantsJson()) {
            return response()->json([
                'images' => $images->map(fn ($image) => $this->present($image)),
                'html' => Image::html($images),
            ], 201);
        }

        return $this->html(Image::html($images), 201);
    }

    public function destroy(Request $request, Image $image)
    {
        abort_unless($image->user_id === $request->user()->id, 404);
        $image->delete();

        return response()->noContent();
    }

    private function html(string $html, int $status = 200)
    {
        return response($html, $status)->header('Content-Type', 'text/html; charset=UTF-8');
    }

    private function present(Image $image): array
    {
        return [
            'id' => $image->id,
            'url' => $image->url(),
            'name' => $image->original_name,
            'size' => $image->size,
            'created_at' => $image->created_at,
        ];
    }
}
