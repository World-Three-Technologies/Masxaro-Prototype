/*
 * Receipt.java -- Receipt structure class 
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
 *  This class is what the receipt looks like and some setter-getter operations on its
 *  several fields.
 */

package com.android.W3T.app.rmanager;

public class Receipt {
	public final static String[][] FakeReceiptsInfo = {
		{"ID@1234", "06-01-2011", "Wendy's", "12.32USD"},
		{"ID@1235", "06-02-2011", "Starbucks", "4.63USD"},
		{"ID@1236", "06-02-2011", "J Street", "10.02USD"},
		{"ID@1237", "06-03-2011", "Starbucks", "4.63USD"},
		{"ID@1238", "06-03-2011", "Penn Grill", "8.76USD"},
		{"ID@1239", "06-04-2011", "Starbucks", "7.56USD"},
		{"ID@1234", "06-04-2011", "Wendy's", "6.22USD"}
	};
	
	private String mId;
	private	String mStoreName;
	private String mDate;
	private String mTotal;
	private boolean mValid;
	
	public Receipt() {
		mId = new String("ID@0000");
		mStoreName = new String("N/A");
		mDate = new String("N/A");
		mTotal = new String("N/A");
		mValid = false;
	}
	
	public String getItem(int i) {
		switch(i) {
		case 0:
			return getId();
		case 1:
			return getDate();
		case 2:
			return getStoreName();
		case 3:
			return getTotal();
		}
		return null;
	}
	
	public String getId() {
		return mId;
	}
	
	public String getStoreName() {
		return mStoreName;
	}
	
	public String getDate() {
		return mDate;
	}
	
	public boolean getValid() {
		return mValid;
	}
	
	public String getTotal() {
		return mTotal;
	}
	
	public void setId(String id) {
		mId = id; 
	}

	public void setDate(String date) {
		mDate = date; 
	}
	
	public void setStoreName(String sn) {
		mStoreName = sn; 
	}
	
	public void setTotal(String tt) {
		mTotal = tt; 
	}
	
	public void setValid(boolean v) {
		mValid = v;
	}
}
