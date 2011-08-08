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

import java.io.Serializable;
import java.util.ArrayList;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

public class Receipt implements Serializable{
	
	/**
	 * 
	 */
	private static final long serialVersionUID = 8704584220504619955L;
	
	public static final int ENTRY_STORE_NAME = 0;
	public static final int ENTRY_TIME = 1;
	public static final int ENTRY_ID = 2;
	public static final int ENTRY_TAX = 3;
	public static final int ENTRY_TOTAL = 4;
	public static final int ENTRY_CURRENCY = 5;
	public static final int ENTRY_STORE_ACC = 6;
	
	// JSON names of Receipt entries
//	public static final String PARAM_RECEIPT_USER_ACC = "user_account";
	public static final String PARAM_RECEIPT_IMAGE = "img";
	public static final String PARAM_RECEIPT_DELETE = "delete";
	
	// JSON names of Item entries.
	public static final String PARAM_ITEM_ID = "item_id";
	public static final String PARAM_ITEM_NAME = "item_name";
	public static final String PARAM_ITEM_QTY = "item_qty";
	public static final String PARAM_ITEM_PRICE = "item_price";
	
	private BasicInfo basic;
//	private String img;
//	private String delete;
	private ArrayList<ReceiptItem> mItems;	// Items in this receipt
	private int mNumItems;			// Number of items
	
	private boolean mWhere;			// Whether this receipt has been synced with system.
									// The receipt retrieved from database sets true.
									// The receipt retrieved from nfc tag sets false
	public Receipt() {
		basic = new BasicInfo();
//		img = new String("N/A");
//		delete = new String();
		mItems = new ArrayList<ReceiptItem>();
		mNumItems = 0;
		mWhere = false;
	}
	
	public Receipt(JSONObject str, boolean w) {
		//			mReceiptId = str.get(PARAM_RECEIPT_ID).toString();
		//			mTime = str.get(PARAM_RECEIPT_TIME).toString();
		//			mStoreName = str.get(PARAM_RECEIPT_STORE_NAME).toString();
		//			mTotal = str.get(PARAM_RECEIPT_TOTAL).toString();
		//			mTax = str.get(PARAM_RECEIPT_TAX).toString();
		basic = new BasicInfo(str);
		mItems = new ArrayList<ReceiptItem>();
		mNumItems = 0;
		mWhere = w;
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
		case ENTRY_TAX:
			result = getTax();
			break;
		case ENTRY_TOTAL:
			result = getTotal();
			break;
		case ENTRY_CURRENCY:
			result = getCurrency();
			break;
		case ENTRY_STORE_ACC:
			result = getStoreAcc();
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
	
	public JSONArray getItemsJsonArray() {
		JSONArray items = new JSONArray();
		for (int i=0;i<mNumItems;i++) {
			ReceiptItem item = mItems.get(i);
			JSONObject itemstr = new JSONObject();
			try {
				itemstr.put("item_id", item.getItemId());
				itemstr.put("item_name", item.getName());
				itemstr.put("item_qty", item.getQty());
				itemstr.put("item_price", item.getPrice());	
				items.put(i, itemstr);
			} catch (JSONException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		}
		
		return items;
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
		return basic.getId();
	}
	
	private String getStoreName() {
		return basic.getStoreName();
	}
	
	private String getTime() {
		return basic.getTime();
	}
	
	private String getTax() {
		return basic.getTax();
	}
	
	private String getCurrency() {
		return basic.getCurrency();
	}
	
	public boolean getWhere() {
		return mWhere;
	}
	
	private String getTotal() {
		return basic.getTotal();
	}
	
	private String getStoreAcc() {
		return basic.getStoreAcc();
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
	public void setWhere(boolean w) {
		mWhere = w;
	}
	
//	private void setTax(String t) {
//		mTax = t;
//	}
}
