<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PagesBlocksModel extends Model
{
	private static $model;
	public function __construct()
	{
		parent::__construct();
		self::$model = new self();
	}

	public static function getBlocks($page_id) {
		return DB::table('pages_blocks AS pb')
			->select(['dt.display_name_plural AS name', 'pb.slug'])
			->where('pb.page_id', $page_id)
			->join('data_types AS dt', 'dt.name', '=', 'pb.table')
			->get();
	}
}
