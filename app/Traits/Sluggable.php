<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait Sluggable
{
    /**
     * Boot Sluggable Event
     *
     * @return void
     */
    public static function bootSluggable()
    {
        static::creating(function (Model $model) {
            if (!$model->slug) {
                static::handleSlugging($model);
            }
        });
    }
    /**
     * Handle Sluggable conditions
     *
     * @param Model $model
     * @param boolean $updating
     * @return void
     */
    public static function handleSlugging(Model $model)
    {
        if (isset(static::$sluggable)) {
            foreach (static::$sluggable as $slug) {
                if (!isset($slug['slugName']) || !isset($slug['slugRef'])) {
                    return;
                }
                static::slugging($model, $slug['slugName'], $slug['slugRef']);
            }
        }
    }
    /**
     * Handle Set Slugging Case
     *
     * @param Model $model
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function slugging(Model $model, $slugName, $slugRef)
    {
        $slug = Str::slug($model->{$slugRef});
        $value  = self::generateUniqueSlug($slugName, $slug);
        $model->$slugName = $value;
    }
    /**
     * Generate Unique Nummber
     *
     * @return mixed
     */
    public static function generateUniqueSlug($slugName, $slugValue)
    {
        $slug = (static::class)::where($slugName, '=', $slugValue)
            ->orWhere($slugName, 'LIKE', $slugValue . '-' . '%')->orderBy('created_at', 'desc');
        if (!$slug->exists()) {
            return $slugValue;
        }
        $slug = $slug->first();
        $array_slug  =  explode('-', $slug->{$slugName});
        $number = array_reverse($array_slug)[0];
        if (is_numeric($number)) {
            $value = str_replace('-' . $number, '', $slug->{$slugName});
            return $value . '-' . ++$number;
        }
        return $slug->{$slugName} . '-1';
    }
}
