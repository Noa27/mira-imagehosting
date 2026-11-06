<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public const EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'bmp'];

    public const MAX_KB = 20480;

    public static function rules(): array
    {
        return ['required', 'file', 'mimes:'.implode(',', self::EXTENSIONS), 'max:'.self::MAX_KB];
    }

    public static function maxUploadBytes(): int
    {
        $bytes = fn (string $value) => (int) $value * ([
            'k' => 1024,
            'm' => 1048576,
            'g' => 1073741824,
        ][strtolower(substr(trim($value), -1))] ?? 1);

        return min(self::MAX_KB * 1024, ...array_filter([
            $bytes(ini_get('upload_max_filesize')),
            $bytes(ini_get('post_max_size')),
        ]));
    }

    public function index(Request $request)
    {
        return view('home', [
            'images' => $request->user()->images()->latest()->get(),
            'maxUpload' => self::maxUploadBytes(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => self::rules(),
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
