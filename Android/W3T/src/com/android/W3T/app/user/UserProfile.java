<<<<<<< HEAD
/*
 * UserProfile.java -- Logged-in user profile class
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
 *  Written by Yichao Yu <yichao@Masxaro>
 * 
 *  All members and methods in this class is static, which means only one user could
 *  be in the app in one time. Once logged in, the status will be ONLINE until log out.
 *  The username and his own receipt manager will be stored in as well.
 */

package com.android.W3T.app.user;

public class UserProfile {
	// Indicators for logged in or no one logged in.
	public static final boolean OFFLINE = false;
	public static final boolean ONLINE = true;
	
	// Flag for whether any user logged in or not
	private static boolean sLogStatus = OFFLINE;
	
	private static String sUname = new String("Not Login");
//	private static String sPassword = new String();
	
	// Called every time when a new user logged in.
	public static void resetUserProfile(boolean s, String name) {
		setStatus(s);
		setUsername(name);
	}
	
	public static void setStatus(boolean s) {
		sLogStatus = s;
	}
	
	public static void setUsername(String name) {
		sUname = name;
	}
	
	public static boolean getStatus() { return sLogStatus; }
	public static String getUsername() { return sUname; }
	
}
=======
/*
 * UserProfile.java -- Logged-in user profile class
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
 *  Written by Yichao Yu <yichao@Masxaro>
 * 
 *  All members and methods in this class is static, which means only one user could
 *  be in the app in one time. Once logged in, the status will be ONLINE until log out.
 *  The username and his own receipt manager will be stored in as well.
 */

package com.android.W3T.app.user;

public class UserProfile {
	// Indicators for logged in or no one logged in.
	public static final boolean OFFLINE = false;
	public static final boolean ONLINE = true;
	
	// Flag for whether any user logged in or not
	private static boolean sLogStatus = OFFLINE;
	
	private static String sUname = new String("Not Login");
//	private static String sPassword = new String();
	
	// Called every time when a new user logged in.
	public static void resetUserProfile(boolean s, String name) {
		setStatus(s);
		setUsername(name);
	}
	
	public static void setStatus(boolean s) {
		sLogStatus = s;
	}
	
	public static void setUsername(String name) {
		sUname = name;
	}
	
	public static boolean getStatus() { return sLogStatus; }
	public static String getUsername() { return sUname; }
	
}
>>>>>>> da8bc115943369846f0c236f750280372737864d
