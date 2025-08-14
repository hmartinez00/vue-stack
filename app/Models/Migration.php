<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Migration
 *
 * @property $id
 * @property $migration
 * @property $batch
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Migration extends Model
{
    public $timestamps = false;
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['migration', 'batch'];


}
