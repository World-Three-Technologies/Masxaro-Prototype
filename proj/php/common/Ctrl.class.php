<?php
/*
 *  Ctrl.class.php -- public common tool functions 
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
 *  tool global functions for all
 */

class Ctrl{
	/**
	 * infoArray to SQL query
	 *
	 * @param array contains item info ([0] key1 => value1, [1] key2 => value2, [2] key3 => value3...)
	 * 
	 * @return str SQL query
	 */
	public static function infoArray2SQL($info)
	{
		$sql = '';
		foreach ($info as $key => $value)
			$sql = $sql."`{$key}` = '$value',";
		$sql = substr($sql, 0, strlen($sql)-1);		
		return $sql;
	}
}
?>