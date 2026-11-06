<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public const RULES = ['required', 'file', 'mimes:jpg,jpeg,png,gif,webp,avif,bmp', 'max:20480'];

    public function index(Request $request)
    {
        return view('home', [
            'images' => $request->user()->images()->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => self::RULES,
        ]);

        $images = collect($request->file('images'))->map(fn ($file) => Image::store($request->user(), $file));

        if ($request->expectsJson()) {
            return response()->json([
                'images' => $images->map(fn ($image) => ['id' => $image->id, 'url' => $image->url()]),
                'html' => Image::html($images),
            ]);
        }

        return back();
    }

    public function destroy(Request $request, Image $image)
    {
        abort_unless($image->user_id === $request->user()->id, 403);
        $image->delete();

        return $request->expectsJson() ? response()->noContent() : back();
    }

    public function token(Request $request)
    {
        return back()->with('token', $request->user()->regenerateToken());
    }
}
