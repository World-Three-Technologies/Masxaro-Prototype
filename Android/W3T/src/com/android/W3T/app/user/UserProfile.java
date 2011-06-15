package com.android.W3T.app.user;

public class UserProfile {
	// Indicators for logged in or no one logged in.
	public final static boolean OFFLINE = false;
	public final static boolean ONLINE = true;
	
	
	// Flag for whether any user logged in or not
	public static boolean sLogStatus = OFFLINE;

	private static String sUname = new String("Not Login");
	
	public static void setStatus(boolean s) {
		sLogStatus = s;
	}
	
	public static void setUname(String name) {
		sUname = name;
	}
	
	public static boolean getStatus() { return sLogStatus; }
	public static String getUname() { return sUname; }
	
}
