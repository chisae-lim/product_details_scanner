<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Brand
 * 
 * @property int $id_brand
 * @property string $name
 * 
 * @property Collection|Product[] $products
 *
 * @package App\Models
 */
class Brand extends Model
{
	protected $table = 'tbl_brand';
	protected $primaryKey = 'id_brand';
	public $timestamps = false;

	protected $hidden = [
		
	];

	protected $fillable = [
		'name'
	];

	public function products(): HasMany
	{
		return $this->hasMany(Product::class, 'id_brand');
	}
}
