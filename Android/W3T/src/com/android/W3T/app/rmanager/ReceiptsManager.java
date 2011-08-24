<<<<<<< HEAD
/*
 * ReceiptManager.java -- Receipt management class 
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
 *  This class is used to manage the receipts: add/upload/keep the database 
 *  on cellphone small.
 *  Right now, I just use a fake receipt system and initialize the manager with
 *  3 receipts.
 *  
 */
package com.android.W3T.app.rmanager;

import java.util.ArrayList;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import android.util.Log;

import com.android.W3T.app.R;

public class ReceiptsManager {
	public static final String TAG = "ReceiptManager";
	
	public static final int NUM_RECEIPT = 7;
	public static final int NUM_RECEIPT_BASIC = 6;
	
	// the flags are used in add(jsonstr, where) method
	public static final boolean FROM_DB = true;
	public static final boolean FROM_NFC = false;
	
	public static final boolean RECEIPT_NEW = true;
	public static final boolean RECEIPT_IN_POOL = false;
		
	public static final String PARAM_ITEM_LABEL = "items";
<<<<<<< HEAD
	public static final int PARAM_RECEIPT_ID = 2;
=======
	public static final int PARAM_ID = 9;
>>>>>>> Android-Prototype
	
	public final static int[] ReceiptViewElements = {
		R.id.store_name_txt, R.id.time_txt, R.id.id_txt, R.id.tax_txt ,R.id.total_cost_txt, R.id.currency_txt
	};
	
	// This array stores all receipts in mobile app.
	private static ArrayList<Receipt> Receipts = new ArrayList< Receipt>();
	private static int[] sReceiptId = new int[NUM_RECEIPT];
//	private static int[] sUnSentId = new int[NUM_RECEIPT];
	private static int sNumValidReceipt = 0;
//	private static int sNumUnSent = 0;
	
	public static void initReceiptsManager() {
		if (Log.isLoggable(TAG, Log.VERBOSE)) {
            Log.v(TAG, "initialize receitp manager");
        }
		for(int i=0;i<NUM_RECEIPT;i++) {
			Receipts.add(i, new Receipt());
			sReceiptId[i] = 0;
//			sUnSentId[i] = 0;
		}
		sNumValidReceipt = 0;
//		sNumUnSent = 0;
	}
<<<<<<< HEAD
	
	public static void clearReceiptPool() {
		Receipts.clear();
=======
	
//	public static void clearReceiptPool() {
//		Receipts.clear();
//		sNumValidReceipt = 0;
//	}
	
	public static void deleteReceipt(int index) {
		Receipts.remove(index);
		sNumValidReceipt--;
>>>>>>> Android-Prototype
	}
	
	/*
	 * This method is called when we get receipts from database or receipt from a nfc tag.
	 * param where	where the receipts come from, FROM_DB or FROM_NFC.
	 * param str	the JSON string which includes the content of receipts.
	 */
<<<<<<< HEAD
	public static void add(String str, boolean where) {
=======
	public static boolean add(String str, boolean where) {
>>>>>>> Android-Prototype
		/* Receipt JSON structure: 
		 * [{"store_account":null,"receipt_id":"101","user_account":null,"receipt_time":"2011-06-21 20:28:41","tax":"0.1","items":[],"total_cost":"10","img":null,"deleted":0,"store_name":"McD"},
		 * {"store_account":null,"receipt_id":"100","user_account":null,"receipt_time":"2011-06-20 03:58:52","tax":"1","items":[{"item_price":"5","item_name":"hamburger","item_id":"1001","item_qty":"1"},{"item_price":"5","item_name":"french fries","item_id":"1002","item_qty":"1"}],"total_cost":"10","img":null,"deleted":0,"store_name":"Starbucks"}]
		 */
		try {
<<<<<<< HEAD
			JSONArray receiptsArray = new JSONArray(str);
			int numReceipt = receiptsArray.length();
			
			JSONObject receiptsInfo[] = new JSONObject[numReceipt];
			JSONArray items[] = new JSONArray[numReceipt];
			if (Log.isLoggable(TAG, Log.VERBOSE)) {
                Log.v(TAG, "start adding receipts");
            }
			// After every loop, a receipt has been created and added into ReceiptsManager.
			for (int i=0;i < numReceipt;i++) {
				receiptsInfo[i] = (JSONObject) receiptsArray.get(i);
				Receipt r = new Receipt(receiptsInfo[i], where);
				items[i] = receiptsInfo[i].getJSONArray(PARAM_ITEM_LABEL);
				if (Log.isLoggable(TAG, Log.VERBOSE)) {
	                Log.v(TAG, "check whether receipt "+r.getEntry(PARAM_RECEIPT_ID)+" in the pool");
	            }
				if (addNewReceipt(r) == RECEIPT_NEW) {
					r.addItems(items[i]);
				}
=======
			if (sNumValidReceipt < NUM_RECEIPT) {
				JSONArray receiptsArray = new JSONArray(str);
				int numReceipt = receiptsArray.length();
				JSONObject receiptsInfo[] = new JSONObject[numReceipt];
				JSONArray items[] = new JSONArray[numReceipt];
				if (Log.isLoggable(TAG, Log.VERBOSE)) {
	                Log.v(TAG, "start adding receipts");
	            }
				// After every loop, a receipt has been created and added into ReceiptsManager.
				for (int i=0;i < numReceipt;i++) {
					receiptsInfo[i] = (JSONObject) receiptsArray.get(i);
					Receipt r = new Receipt(receiptsInfo[i], where);
					items[i] = receiptsInfo[i].getJSONArray(PARAM_ITEM_LABEL);
					if (Log.isLoggable(TAG, Log.VERBOSE)) {
		                Log.v(TAG, "check whether receipt "+r.getEntry(PARAM_ID)+" in the pool");
		            }
					if (addNewReceipt(r) == RECEIPT_NEW) {
						r.addItems(items[i]);
					}
				}
				if (Log.isLoggable(TAG, Log.VERBOSE)) {
		            Log.v(TAG, "adding receipts is done");
		        }		
			}
			else {
				return false;
>>>>>>> Android-Prototype
			}
		} catch (JSONException e) {
			e.printStackTrace();
		}
		return true;
	}
	
	/* Add a new receipt into ReceiptManager.
	 * return	if true, the receipt was not in receipt pool before 
	 * 			and has been added into the pool now.
	 * 			if false, already in the pool and won't be added.
	 */
	private static boolean addNewReceipt(Receipt r) {
		boolean get = false;
		if (Log.isLoggable(TAG, Log.VERBOSE)) {
            Log.v(TAG, "add a new receipt into Receipt pool");
        }
<<<<<<< HEAD
		int id = Integer.parseInt(r.getEntry(PARAM_RECEIPT_ID));
		for (int i=0;i<NUM_RECEIPT;i++) {
			if (sReceiptId[i] == id) {
=======
		int id = Integer.parseInt(r.getEntry(PARAM_ID));
		// should check the database whether the receipt is already in the database.
		for (int i=0;i<NUM_RECEIPT;i++) {
			if (id != -1 && sReceiptId[i] == id) {
>>>>>>> Android-Prototype
				get = true;
				break;
			}
		}
		if (get == false){
			if (Log.isLoggable(TAG, Log.VERBOSE)) {
	            Log.v(TAG, "no such receipt, add one");
	        }
			Receipts.add(sNumValidReceipt, r);
			sReceiptId[sNumValidReceipt] = id;
			sNumValidReceipt++;
<<<<<<< HEAD
//			if (r.getWhere() == FROM_NFC) {
//				sUnSentId[sNumUnSent] = id;
//				sNumUnSent++;
//			}
=======
>>>>>>> Android-Prototype
			return RECEIPT_NEW;
		}
		if (Log.isLoggable(TAG, Log.VERBOSE)) {
            Log.v(TAG, "already have such receipt");
        }
		return RECEIPT_IN_POOL;
//		TODO: System.out.println("Any un-delivered receipt?");	

	}
	
	public static ArrayList<Receipt> getUnSentReceipts() {
		int cnt = getNumValid();
		ArrayList<Receipt> result = new ArrayList<Receipt>();
		for (int i=0;i<cnt;i++) {
			Receipt r = getReceipt(i);
			if (r.getWhere() == FROM_NFC) {
				result.add(r);
			}
		}
		return result;
	}
	
<<<<<<< HEAD
//	public static void clearUnSentArray() {
//		for (int i=0;i<sNumUnSent;i++) {
//			sUnSentId[i] = 0;
//		}
//		sNumUnSent = 0;
//	}
	
//	public static int getUnSentNum() {
//		return sNumUnSent;
//	}
	
=======
>>>>>>> Android-Prototype
	public static int getNumValid() {
		return sNumValidReceipt;
	}
	
	public static Receipt getReceipt(int index) {
		return Receipts.get(index);
	}
}
=======
/*
 * ReceiptManager.java -- Receipt management class 
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
 *  This class is used to manage the receipts: add/upload/keep the database 
 *  on cellphone small.
 *  Right now, I just use a fake receipt system and initialize the manager with
 *  3 receipts.
 *  
 */
package com.android.W3T.app.rmanager;

import java.util.ArrayList;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.util.Log;

import com.android.W3T.app.R;

public class ReceiptsManager {
	public static final String TAG = "ReceiptManager";
	public static final int NUM_RECEIPT = 7;
	public static final int NUM_RECEIPT_ENTRY = 5;
	public static final int NUM_ITEM_ENTRY = 4;
	
	// the flags are used in add(jsonstr, where) method
	public static final boolean FROM_DB = true;
	public static final boolean FROM_NFC = false;
	
	public static final boolean RECEIPT_NEW = true;
	public static final boolean RECEIPT_IN_POOL = false;
		
	public static final String PARAM_ITEM_LABEL = "items";
	public static final int PARAM_RECEIPT_ID = 2;
	
	public final static int[] ReceiptViewElements = {
		R.id.store_name_txt, R.id.time_txt, R.id.id_txt, R.id.tax_txt ,R.id.total_cost_txt
	};
	
	// This array stores all receipts in mobile app.
	private static ArrayList<Receipt> Receipts = new ArrayList< Receipt>();
	private static int[] sReceiptId = new int[NUM_RECEIPT];
//	private static int[] sUnSentId = new int[NUM_RECEIPT];
	private static int sNumValidReceipt = 0;
//	private static int sNumUnSent = 0;
	
	public static void initReceiptsManager() {
		if (Log.isLoggable(TAG, Log.VERBOSE)) {
            Log.v(TAG, "initialize receitp manager");
        }
		for(int i=0;i<NUM_RECEIPT;i++) {
			Receipts.add(i, new Receipt());
			sReceiptId[i] = 0;
//			sUnSentId[i] = 0;
		}
		sNumValidReceipt = 0;
//		sNumUnSent = 0;
	}
	
	public static void clearReceiptPool() {
		Receipts.clear();
	}
	
	/*
	 * This method is called when we get receipts from database or receipt from a nfc tag.
	 * param where	where the receipts come from, FROM_DB or FROM_NFC.
	 * param str	the JSON string which includes the content of receipts.
	 */
	public static void add(String str, boolean where) {
		/* Receipt JSON structure: 
		 * [{"store_account":null,"receipt_id":"101","user_account":null,"receipt_time":"2011-06-21 20:28:41","tax":"0.1","items":[],"total_cost":"10","img":null,"deleted":0,"store_name":"McD"},
		 * {"store_account":null,"receipt_id":"100","user_account":null,"receipt_time":"2011-06-20 03:58:52","tax":"1","items":[{"item_price":"5","item_name":"hamburger","item_id":"1001","item_qty":"1"},{"item_price":"5","item_name":"french fries","item_id":"1002","item_qty":"1"}],"total_cost":"10","img":null,"deleted":0,"store_name":"Starbucks"}]
		 */
		try {
			JSONArray receiptsArray = new JSONArray(str);
			int numReceipt = receiptsArray.length();
			
			JSONObject receiptsInfo[] = new JSONObject[numReceipt];
			JSONArray items[] = new JSONArray[numReceipt];
			if (Log.isLoggable(TAG, Log.VERBOSE)) {
                Log.v(TAG, "start adding receipts");
            }
			// After every loop, a receipt has been created and added into ReceiptsManager.
			for (int i=0;i < numReceipt;i++) {
				receiptsInfo[i] = (JSONObject) receiptsArray.get(i);
				Receipt r = new Receipt(receiptsInfo[i], where);
				items[i] = receiptsInfo[i].getJSONArray(PARAM_ITEM_LABEL);
				if (Log.isLoggable(TAG, Log.VERBOSE)) {
	                Log.v(TAG, "check whether receipt "+r.getEntry(PARAM_RECEIPT_ID)+" in the pool");
	            }
				if (addNewReceipt(r) == RECEIPT_NEW) {
					r.addItems(items[i]);
				}
			}
			if (Log.isLoggable(TAG, Log.VERBOSE)) {
	            Log.v(TAG, "adding receipts is done");
	        }			
		} catch (JSONException e) {
			e.printStackTrace();
		}
	}
	
	/* Add a new receipt into ReceiptManager.
	 * return	if true, the receipt was not in receipt pool before 
	 * 			and has been added into the pool now.
	 * 			if false, already in the pool and won't be added.
	 */
	private static boolean addNewReceipt(Receipt r) {
		boolean get = false;
		if (Log.isLoggable(TAG, Log.VERBOSE)) {
            Log.v(TAG, "add a new receipt into Receipt pool");
        }
		int id = Integer.parseInt(r.getEntry(PARAM_RECEIPT_ID));
		for (int i=0;i<NUM_RECEIPT;i++) {
			if (sReceiptId[i] == id) {
				get = true;
				break;
			}
		}
		if (get == false){
			if (Log.isLoggable(TAG, Log.VERBOSE)) {
	            Log.v(TAG, "no such receipt, add one");
	        }
			Receipts.add(sNumValidReceipt, r);
			sReceiptId[sNumValidReceipt] = id;
			sNumValidReceipt++;
//			if (r.getWhere() == FROM_NFC) {
//				sUnSentId[sNumUnSent] = id;
//				sNumUnSent++;
//			}
			return RECEIPT_NEW;
		}
		if (Log.isLoggable(TAG, Log.VERBOSE)) {
            Log.v(TAG, "already have such receipt");
        }
		return RECEIPT_IN_POOL;
//		TODO: System.out.println("Any un-delivered receipt?");	

	}
	
	public static ArrayList<Receipt> getUnSentReceipts() {
		int cnt = getNumValid();
		ArrayList<Receipt> result = new ArrayList<Receipt>();
		for (int i=0;i<cnt;i++) {
			Receipt r = getReceipt(i);
			if (r.getWhere() == FROM_NFC) {
				result.add(r);
			}
		}
		return result;
	}
	
//	public static void clearUnSentArray() {
//		for (int i=0;i<sNumUnSent;i++) {
//			sUnSentId[i] = 0;
//		}
//		sNumUnSent = 0;
//	}
	
//	public static int getUnSentNum() {
//		return sNumUnSent;
//	}
	
	public static int getNumValid() {
		return sNumValidReceipt;
	}
	
	public static Receipt getReceipt(int index) {
		return Receipts.get(index);
	}
}
>>>>>>> da8bc115943369846f0c236f750280372737864d
