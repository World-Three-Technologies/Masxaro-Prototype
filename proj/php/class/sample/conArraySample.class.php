<?php
//include '../../config.php';
$con = array(
		'AND'=>array(
					'AND'=>array(
								'='=>array(
											'field'=>'user_account',
											'value'=>'new'
										),
								'<'=>array(
											'field'=>'receipt_time',
											'value'=>'2012-02-02'
										),
								'>'=>array(
											'value0'=>2,
											'value1'=>1
										)
							),
					'value'=>true,
					'='=>array(
								'formula0'=>'64*2',
								'formula1'=>'32*4'
							)
				)
);
//echo json_encode($con);
echo Tool::condArray2SQL($con);
die();
?>
