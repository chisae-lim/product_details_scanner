<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class UserPermission
 * 
 * @property int $id_user_permission
 * @property int $id_user
 * @property int $id_permission
 * 
 *
 * @package App\Models
 */
class UserPermission extends Model
{
	protected $table = 'tbl_user_permission';
	protected $primaryKey = 'id_user_permission';
	public $timestamps = false;

	protected $casts = [
		'id_user' => 'int',
		'id_permission' => 'int'
	];

	protected $hidden = [
		'id_user',
		'id_permission'
	];

	protected $fillable = [
		'id_user',
		'id_permission'
	];

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class, 'id_user');
	}

	public function permission(): BelongsTo
	{
		return $this->belongsTo(Permission::class, 'id_permission');
	}
}
