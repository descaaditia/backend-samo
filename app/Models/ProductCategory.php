<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
    // SoftDeletes karena di table ini kita menggunakan SoftDeletes
    use HasFactory, SoftDeletes;

        /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name'
    ];

        // me relasikan table uproduct_categoriesser dengan table products (id = field ysng ada di product_categories , users_id = field ysng ada di products )
        public function products(){

            return $this->hasMany(product::class, 'users_id','id');
        }
}
