package com.android.W3T.app.user;

public class UserProfile {
	private static String mUname = new String();
	public static String getUname() { return mUname; }
	public static void setUname(String name) {
		mUname = name;
	}
}
