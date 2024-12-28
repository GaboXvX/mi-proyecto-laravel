<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class moviento extends Model
{
    use HasFactory;
    protected $table='movimientos';
    protected $primaryKey='id_movimiento';
}
