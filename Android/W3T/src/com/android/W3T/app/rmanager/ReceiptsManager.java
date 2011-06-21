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

import com.android.W3T.app.R;

public class ReceiptsManager {
	public static final int NUM_RECEIPT = 7;
	public static final int NUM_RECEIPT_ENTRY = 5;
	public static final int NUM_ITEM_ENTRY = 5;
	
	public static final String PARAM_RECEIPT_LABEL = "basicInfo";
	public static final String PARAM_ITEM_LABEL = "items";
	
	public final static int[] ReceiptViewElements = {
		R.id.store_name_txt, R.id.time_txt, R.id.id_txt, R.id.tag_tax_txt ,R.id.total_cost_txt
	};
	
	// This array stores all receipts in mobile app.
	private static ArrayList<Receipt> Receipt = new ArrayList<Receipt>();
	private static int sNumValidReceipt = 0;
	
	public static void initReceiptsManager() {
		for(int i=0;i<NUM_RECEIPT;i++) {
			Receipt.add(i, new Receipt());
		}
		sNumValidReceipt = 0;
	}	
	
	public static int getNumValid() {
		return sNumValidReceipt;
	}
	
	public static Receipt getReceipt(int index) {
		return Receipt.get(index);
	}
	
	public static void add(String str) {
		/* Receipt JSON structure: 
		 * [
		 * {"basicInfo":
		 * {"receipt_id":"1","receipt_time":"2011-06-15 09:08:42","tax":"0.1","total_cost":"14","store_name":"McDonalds(NYU)"},
		 * "items":
		 * [{"item_id":"10","item_name":"fries-mid","item_qty":"2","item_discount":"1","item_price":"2.25"},...]},
		 * 
		 * {"basicInfo...", "items"...}
		 * ]
		 */
		try {
			JSONArray receiptsArray = new JSONArray(str);
			int numReceipt = receiptsArray.length();
			
			JSONObject basicInfo[] = new JSONObject[numReceipt];
			JSONArray items[] = new JSONArray[numReceipt];
			// After every loop, a receipt has been created and added into ReceiptsManager.
			for (int i=0;i < numReceipt;i++) {
				JSONObject tmp = (JSONObject) receiptsArray.get(i);
				basicInfo[i] = tmp.getJSONObject(PARAM_RECEIPT_LABEL);
				items[i] = tmp.getJSONArray(PARAM_ITEM_LABEL);
				Receipt r = new Receipt(basicInfo[i]);
				r.addItems(items[i]);
				addNewReceipt(r);
			}
			
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}
	
	// Add a new receipt into ReceiptManager
	private static void addNewReceipt(Receipt r) {
		Receipt.add(sNumValidReceipt, r);
		sNumValidReceipt++;
//	TODO:	System.out.println("Any un-delivered receipt?");	
//	TODO:	System.out.println("Add a new receipt");
	}
}
