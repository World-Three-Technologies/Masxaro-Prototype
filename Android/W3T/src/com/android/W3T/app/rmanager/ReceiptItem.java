package com.android.W3T.app.rmanager;

public class ReceiptItem {
	private int mItemId;
	private String mName;
	private int mQty;
//	private String mDiscount;
	private double mPrice;
	
	public ReceiptItem() {
		mItemId = 0;
		mName = null;
		mQty = 0;
//		mDiscount = null;
		mPrice = 0.0;
	}
	
	public int getItemId() {
		return mItemId;
	}
	public String getName() {
		return mName;
	}
	public int getQty() {
		return mQty;
	}
//	public String getDiscount() {
//		return mDiscount;
//	}
	public double getPrice() {
		return mPrice;
	}
	
	public void setItemId(int id) {
		mItemId = id;
	}
	public void setName(String name) {
		mName = name;
	}
	public void setQty(int q) {
		mQty = q;
	}
//	public void setDiscount(String d) {
//		mDiscount = d;
//	}
	public void setPrice(double p) {
		mPrice = p;
	}
}
