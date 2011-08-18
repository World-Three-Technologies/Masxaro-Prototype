package com.android.W3T.app.rmanager;

import org.json.JSONException;
import org.json.JSONObject;

public class ReceiptItem {
	// JSON names of Item entries.
	public static final String PARAM_ITEM_ID = "item_id";
	public static final String PARAM_ITEM_NAME = "item_name";
	public static final String PARAM_ITEM_QTY = "item_qty";
	public static final String PARAM_ITEM_PRICE = "item_price";
	public static final String PARAM_ITEM_DISCOUNT = "item_discount";
	
	private String mItemId;
	private String mName;
	private String mQty;
	private String mDiscount;
	private String mPrice;
	
	public ReceiptItem() {
		mItemId = null;
		mName = null;
		mQty = null;
		mDiscount = null;
		mPrice = null;
	}
	
	public ReceiptItem(JSONObject item) {
		// item id could be null.
		try {
			if (item.getString(PARAM_ITEM_ID).equals(null)) {
				mItemId = item.getString(PARAM_ITEM_ID);
			}
			else {
				mItemId = String.valueOf(-1);// the item id is null
			}
			mName = item.getString(PARAM_ITEM_NAME);
			mQty = item.getString(PARAM_ITEM_QTY);
			mPrice = item.getString(PARAM_ITEM_PRICE);
			mDiscount = item.getString(PARAM_ITEM_DISCOUNT);
			
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}
	
	public String getItemId() {
		return mItemId;
	}
	
	public void setItemId(String id) {
		mItemId = id;
	}
	
	public String getName() {
		return mName;
	}
	
	public void setName(String name) {
		mName = name;
	}
	
	public void setQty(String q) {
		mQty = q;
	}
	
	public String getQty() {
		return mQty;
	}
	
	public void setDiscount(String d) {
		mDiscount = d;
	}
	
	public String getDiscount() {
		return mDiscount;
	}
	
	public void setPrice(String p) {
		mPrice = p;
	}
	
	public String getPrice() {
		return mPrice;
	}
}
