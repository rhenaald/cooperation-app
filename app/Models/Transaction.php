<?php

namespace App\Models;

use App\Models\category;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'category_id',
        'date_transaction',
        'amount',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(category::class);
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
