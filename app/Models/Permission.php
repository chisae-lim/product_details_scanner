<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Permission
 * 
 * @property int $id_permission
 * @property string $permission
 * @property string $disabled
 * 
 * @property Collection|User[] $users
 *
 * @package App\Models
 */
class Permission extends Model
{
	protected $table = 'tbl_permission';
	protected $primaryKey = 'id_permission';
	public $timestamps = false;

	protected $hidden = [
		'pivot'
	];

	protected $fillable = [
		'permission',
		'status'
	];

	public function users(): BelongsToMany
	{
		return $this->belongsToMany(User::class, 'tbl_user_permission', 'id_permission', 'id_user')
					->withPivot('id_user_permission');
	}
}
