<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Category
 * 
 * @property int $id_category
 * @property string $name
 * 
 * @property Collection|Product[] $products
 *
 * @package App\Models
 */
class Category extends Model
{
	protected $table = 'tbl_category';
	protected $primaryKey = 'id_category';
	public $timestamps = false;

	protected $hidden = [
		
	];

	protected $fillable = [
		'name'
	];

	public function products(): HasMany
	{
		return $this->hasMany(Product::class, 'id_category');
	}
}
