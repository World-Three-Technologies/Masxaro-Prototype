<?php

/*
 *  EmailCtrl.class.php -- email control, email receive & send 
 *
 *  Copyright 2011 World Three Technologies, Inc. 
 *  All Rights Reserved.
 * 
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 *  Written by Yaxing Chen <Yaxing@masxaro.com>
 * 
 */

class EmailCtrl extends Ctrl{
	
	function __construct(){
		parent::__construct();
	}
	
	public function mail($to, $subject, $message, $addHeaders = ""){
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
		// Additional headers
		//$headers .= 'To: Mary <mary@example.com>, Kelly <kelly@example.com>' . "\r\n";
		$headers .= 'From: Masxaro <masxaro-notice@masxaro.com>' . "\r\n";
//		$headers .= 'Cc: birthdayarchive@example.com' . "\r\n";
//		$headers .= 'Bcc: birthdaycheck@example.com' . "\r\n";
		$headers .= $addHeaders;
		
		return mail($to, $subject, $message, $headers);
	}
	
}

?>