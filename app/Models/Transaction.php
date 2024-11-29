<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'users_id',
        'category_id',
        'date_transaction',
        'amount',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class,  'users_id');
    }

    public function category()
    {
        return $this->belongsTo(category::class,  'category_id');
    }
    
    public function scopePemasukan($query)
    {
        return $query->whereHas('category', function ($query) {
            $query->where('id', 1);
        });
    }

    public function scopePengeluaran($query)
    {
        return $query->whereHas('category', function ($query) {
            $query->where('id', 2);
        });
    }
}
