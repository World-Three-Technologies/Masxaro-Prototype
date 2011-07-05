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

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

public class Receipt {
	
	public static final int ENTRY_STORE_NAME = 0;
	public static final int ENTRY_TIME = 1;
	public static final int ENTRY_ID = 2;
	
	// JSON names of Receipt entries
	public static final String PARAM_RECEIPT_ID = "receipt_id";
	public static final String PARAM_RECEIPT_TIME = "receipt_time";
	public static final String PARAM_RECEIPT_TAX = "tax";
	public static final String PARAM_RECEIPT_TOTAL = "total_cost";
	public static final String PARAM_RECEIPT_STORE_NAME = "store_name";
//	public static final String PARAM_RECEIPT_STORE_ACC = "store_account";
//	public static final String PARAM_RECEIPT_USER_ACC = "user_account";
	public static final String PARAM_RECEIPT_IMAGE = "img";
	public static final String PARAM_RECEIPT_DELETE = "delete";
	
	// JSON names of Item entries.
	public static final String PARAM_ITEM_ID = "item_id";
	public static final String PARAM_ITEM_NAME = "item_name";
	public static final String PARAM_ITEM_QTY = "item_qty";
	public static final String PARAM_ITEM_PRICE = "item_price";
	
//	private static final boolean FROM_DB = ReceiptsManager.FROM_DB;
//	private static final boolean FROM_NFC = ReceiptsManager.FROM_NFC;
	
	private String mReceiptId;
	private	String mStoreName;
	private String mTime;
	private String mTax;
	private String mTotal;
//	private String img;
//	private String delete;
	private ArrayList<ReceiptItem> mItems;	// Items in this receipt
	private int mNumItems;			// Number of items
	
	private boolean mWhere;			// Whether this receipt has been synced with system.
									// The receipt retrieved from database sets true.
									// The receipt retrieved from nfc tag sets false
	public Receipt() {
		mReceiptId = new String("ID@0000");
		mStoreName = new String("N/A");
		mTime = new String("N/A");
		mTotal = new String("N/A");
		mTax = new String("N/A");
//		img = new String("N/A");
//		delete = new String();
		mItems = new ArrayList<ReceiptItem>();
		mNumItems = 0;
		mWhere = false;
	}
	
	public Receipt(JSONObject str, boolean w) {
		try {
			mReceiptId = str.get(PARAM_RECEIPT_ID).toString();
			mTime = str.get(PARAM_RECEIPT_TIME).toString();
			mStoreName = str.get(PARAM_RECEIPT_STORE_NAME).toString();
			mTotal = str.get(PARAM_RECEIPT_TOTAL).toString();
			mTax = str.get(PARAM_RECEIPT_TAX).toString();
			mItems = new ArrayList<ReceiptItem>();
			mNumItems = 0;
			mWhere = w;
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}
	
	public String getEntry(int i) {
		String result = new String();  
		switch(i) {
		case ENTRY_STORE_NAME:
			result = getStoreName();
			break;
		case ENTRY_TIME:
			result = getTime();
			break;
		case ENTRY_ID:
			result = getId();
			break;
		case 3:
			result = getTax();
			break;
		case 4:
			result = getTotal();
			break;
		default:
			result = null;
			break;
		}
		return result;
	}
	
	public ReceiptItem getItem(int i) {
		return mItems.get(i);
	}
	
	// Called when there is a need to add items to a receipt, r.
	public void addItems(JSONArray items) {
		mNumItems = items.length();
		
		try {
			for (int i=0;i<mNumItems;i++) {
				JSONObject item = (JSONObject) items.get(i);
				ReceiptItem newItem = new ReceiptItem();
				newItem.setItemId(Integer.valueOf(item.getString(PARAM_ITEM_ID)));
				newItem.setName(item.getString(PARAM_ITEM_NAME));
				newItem.setQty(Integer.valueOf(item.getString(PARAM_ITEM_QTY)));
				newItem.setPrice(Double.parseDouble(item.getString(PARAM_ITEM_PRICE)));
				mItems.add(newItem);
			}
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}
	
	public int getNumItem() {
		return mNumItems;
	}
	
	private String getId() {
		return mReceiptId;
	}
	
	private String getStoreName() {
		return mStoreName;
	}
	
	private String getTime() {
		return mTime;
	}
	
	private String getTax() {
		return mTax;
	}
	
	public boolean getWhere() {
		return mWhere;
	}
	
	private String getTotal() {
		return mTotal;
	}
	
//	private void setId(String id) {
//		mReceiptId = id; 
//	}
//
//	private void setTime(String time) {
//		mTime = time; 
//	}
//	
//	private void setStoreName(String sn) {
//		mStoreName = sn; 
//	}
//	
//	private void setTotal(String tt) {
//		mTotal = tt; 
//	}
//	
//	private void setWhere(boolean w) {
//		mWhere = w;
//	}
	
//	private void setTax(String t) {
//		mTax = t;
//	}
}
