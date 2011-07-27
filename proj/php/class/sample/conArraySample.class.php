<?php
//include '../../config.php';

/**
 * 
 * since this is a hashmap, operators, operand tags are keys,
 * when same operators of operand tags(field, value, formula) are parallel, 
 * you need to use numbers to differentiate them.
 * 
 * 
 * this is an example of SQL condition statement:
 * 
 * user_account = 'new' AND 0 = 0 AND receipt_time < '2012-02-02' AND 2 > 1 AND 1 AND 64*2 = 32 *4
 */


$con = array(
		'AND'=>array(
					'AND'=>array(
								'=:0'=>array(
											'field'=>'user_account',
											'value'=>'new'
										),
								'=:1'=>array(
											'value'=>'0',
											'value'=>'0'
										),
								'<'=>array(
											'field'=>'receipt_time',
											'value'=>'2012-02-02'
										),
								'>'=>array(
											'value:0'=>2,
											'value:1'=>1
										)
							),
					'value'=>true,
					'='=>array(
								'formula:0'=>'64*2',
								'formula:1'=>'32*4'
							)
				)
);


//echo json_encode($con);
echo Tool::condArray2SQL($con);
die();
?>
