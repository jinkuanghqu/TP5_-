<?php
namespace app\api\validate;

class IDCollection extends BaseValidate
{
	protected $rule = [
		'ids'  => 'require|checkIDs',
	];

	protected $message = [
		'ids'  => 'ids必须是以逗号分隔的正整数',
	];

	//ids=id1,id2,....
	protected function checkIDs($value)
	{
		$values = explode(',',$value);
		if (empty($values)) {
			return false;
		}

		foreach ($values as $key => $id) {
			if (!$this->isPositiveInteger($id)) {
				return false;
			}
		}
		return true;
	}
}