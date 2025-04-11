<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $fillable=[
        "id",
        "username",
        "first_name",
        "last_name",
        "info",
        "balance"
    ];

    public function ichancies():HasMany
    {
        return $this->hasMany(Ichancy::class);
    }
    public function ichTransactions():HasMany
    {
        return $this->hasMany(IchTransaction::class);
    }

}
