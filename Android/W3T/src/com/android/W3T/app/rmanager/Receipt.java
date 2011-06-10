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
