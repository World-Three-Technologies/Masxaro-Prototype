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
import com.android.W3T.app.R;

public class ReceiptsManager {
	public final static int NUM_RECEIPT = 7;
	public final static int NUM_RECEIPT_ENTRY = 5;
	public final static int NUM_ITEM_ENTRY = 5;
	
	public final static int[] ReceiptViewElements = {
		R.id.id_txt, R.id.date_txt, R.id.store_name_txt, R.id.total_cost_txt
	};
	

	private static ArrayList<Receipt> FakeReceipt = new ArrayList<Receipt>();
	private static int sNumValidReceipt = 0;
	
	public static void initReceiptsManager() {
		for(int i=0;i<NUM_RECEIPT;i++) {
			FakeReceipt.add(i, new Receipt());
		}
		sNumValidReceipt = 0;
		
		addFakeReceipts();
	}
	
	/*----------------- Test part ------------------*/
	private static void addFakeReceipts() {
		for (int i=0;i<3;i++) { 
			FakeReceipt.get(i).setId(Receipt.sFakeReceiptsInfo[i][0]);
			FakeReceipt.get(i).setTime(Receipt.sFakeReceiptsInfo[i][1]);
			FakeReceipt.get(i).setStoreName(Receipt.sFakeReceiptsInfo[i][2]);
			FakeReceipt.get(i).setTotal(Receipt.sFakeReceiptsInfo[i][3]);
			FakeReceipt.get(i).setValid(true);
			sNumValidReceipt++;
		}
			
	}
	
	
	public static int getNumValid() {
		return sNumValidReceipt;
	}
	
	public static ArrayList<Receipt> getReceipts() {
		return FakeReceipt;
	}
	
	public static void addNewReceipt(Receipt r) {
		FakeReceipt.add(sNumValidReceipt, r);
		sNumValidReceipt++;
//	TODO:	System.out.println("Any un-delivered receipt?");
//		
//	TODO:	System.out.println("Add a new receipt");
	}
}
