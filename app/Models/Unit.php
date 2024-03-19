<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Unit
 * 
 * @property int $id_unit
 * @property string $name
 * @property string $label
 * 
 * @property Collection|Product[] $products
 *
 * @package App\Models
 */
class Unit extends Model
{
	protected $table = 'tbl_unit';
	protected $primaryKey = 'id_unit';
	public $timestamps = false;

	protected $hidden = [
		
	];

	protected $fillable = [
		'name',
		'label'
	];

	public function products(): HasMany
	{
		return $this->hasMany(Product::class, 'id_unit');
	}
}
