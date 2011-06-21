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

import java.util.ArrayList;

import org.json.JSONException;
import org.json.JSONObject;

public class Receipt {
	// JSON names of Receipt entries
	public static final String PARAM_RECEIPT_ID = "receipt_id";
	public static final String PARAM_RECEIPT_TIME = "receipt_time";
	public static final String PARAM_RECEIPT_TAX = "tax";
	public static final String PARAM_RECEIPT_TOTAL = "total_cost";
	public static final String PARAM_RECEIPT_STORE_NAME = "store_name";
	
	// JSON names of Item entries.
	public static final String PARAM_ITEM_ID = "item_id";
	public static final String PARAM_ITEM_NAME = "item_name";
	public static final String PARAM_ITEM_QTY = "item_qty";
	public static final String PARAM_ITEM_DISCOUNT = "item_discount";
	public static final String PARAM_ITEM_PRICE = "item_price";
	
	
	public static final String[][] sFakeReceiptsInfo = {
		{"ID@1234", "06-01-2011", "Wendy's", "12.32USD"},
		{"ID@1235", "06-02-2011", "Starbucks", "4.63USD"},
		{"ID@1236", "06-02-2011", "J Street", "10.02USD"},
		{"ID@1237", "06-03-2011", "Starbucks", "4.63USD"},
		{"ID@1238", "06-03-2011", "Penn Grill", "8.76USD"},
		{"ID@1239", "06-04-2011", "Starbucks", "7.56USD"},
		{"ID@1234", "06-04-2011", "Wendy's", "6.22USD"}
	};
	
	private String mReceiptId;
	private	String mStoreName;
	private String mTime;
	private String mTax;
	private String mTotal;
	private ArrayList<ReceiptItem> mItems;
	private int mNumItems;
	private boolean mValid;
	
	public Receipt() {
		mReceiptId = new String("ID@0000");
		mStoreName = new String("N/A");
		mTime = new String("N/A");
		mTotal = new String("N/A");
		mTax = new String("N/A");
		mItems = new ArrayList<ReceiptItem>();
		mNumItems = 0;
		mValid = false;
	}
	
	public String getEntry(int i) {
		switch(i) {
		case 0:
			return getId();
		case 1:
			return getTime();
		case 2:
			return getStoreName();
		case 3:
			return getTotal();
		case 4:
			return getTax();
		}
		return null;
	}
	
	public ReceiptItem getItem(int i) {
		return mItems.get(i);
	}
	
	public void addItem(JSONObject item) {
		ReceiptItem newItem = new ReceiptItem();
		try {
			newItem.setItemId(Integer.valueOf((String) item.get(PARAM_ITEM_ID)));
			newItem.setName(item.get(PARAM_ITEM_NAME).toString());
			newItem.setQty(Integer.valueOf((String) item.get(PARAM_ITEM_QTY)));
			newItem.setDiscount(Double.parseDouble(item.getString(PARAM_ITEM_DISCOUNT)));
			newItem.setPrice(Double.parseDouble(item.getString(PARAM_ITEM_PRICE)));
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		mItems.add(newItem);
		mNumItems++;
	}
	
	public String getId() {
		return mReceiptId;
	}
	
	public String getStoreName() {
		return mStoreName;
	}
	
	public String getTime() {
		return mTime;
	}
	
	public String getTax() {
		return mTax;
	}
	
	public boolean getValid() {
		return mValid;
	}
	
	public String getTotal() {
		return mTotal;
	}
	
	public void setId(String id) {
		mReceiptId = id; 
	}

	public void setTime(String time) {
		mTime = time; 
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
	
	public void setTax(String t) {
		mTax = t;
	}
}
