<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

#[Fillable(['filename', 'original_name', 'size'])]
class Image extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function url(): string
    {
        return asset('i/'.$this->filename);
    }

    public function path(): string
    {
        return public_path('i/'.$this->filename);
    }

    public static function store(User $user, UploadedFile $file): self
    {
        $filename = Str::lower(Str::random(10)).'.'.$file->guessExtension();
        $file->move(public_path('i'), $filename);

        return $user->images()->create([
            'filename' => $filename,
            'original_name' => mb_substr($file->getClientOriginalName(), 0, 255),
            'size' => filesize(public_path('i/'.$filename)),
        ]);
    }

    public static function html(iterable $images): string
    {
        $html = '';
        foreach ($images as $image) {
            $html .= '<img src="'.e($image->url()).'" alt="'.e($image->original_name).'">';
        }

        return $html;
    }

    protected static function booted(): void
    {
        static::deleted(fn (Image $image) => @unlink($image->path()));
    }
}
