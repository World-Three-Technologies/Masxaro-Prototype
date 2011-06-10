package com.android.W3T.app.rmanager;

import java.util.ArrayList;
import com.android.W3T.app.R;
import com.android.W3T.app.ReceiptsView;

public class ReceiptManager {
	public final static int NUM_RECEIPT = 7;
	public final static int NUM_RECEIPT_ITEM = 4;
	
	public final static int[] ReceiptViewElements = {
		R.id.id_txt, R.id.date_txt, R.id.store_name_txt, R.id.total_cost_txt
	};
	

	private static ArrayList<Receipt> FakeReceipt = new ArrayList<Receipt>();
	private static int mNumValidReceipt = 0;
	
	public ReceiptManager() {
		for(int i=0;i<NUM_RECEIPT;i++) {
			FakeReceipt.add(i, new Receipt());
		}
		mNumValidReceipt = 0;
		
		addFakeReceipts();
	}
	
	/*----------------- Test part ------------------*/
	private void addFakeReceipts() {
		for (int i=0;i<3;i++) { 
			FakeReceipt.get(i).setId(Receipt.FakeReceiptsInfo[i][0]);
			FakeReceipt.get(i).setDate(Receipt.FakeReceiptsInfo[i][1]);
			FakeReceipt.get(i).setStoreName(Receipt.FakeReceiptsInfo[i][2]);
			FakeReceipt.get(i).setTotal(Receipt.FakeReceiptsInfo[i][3]);
			FakeReceipt.get(i).setValid(true);
			mNumValidReceipt++;
		}
			
	}
	
	
	
	public static int getNumValid() {
		return mNumValidReceipt;
	}
	
	public static ArrayList<Receipt> getReceipts() {
		return FakeReceipt;
	}
	
	public static void addNewReceipt(Receipt r) {
		FakeReceipt.add(mNumValidReceipt, r);
		mNumValidReceipt++;
//		System.out.println("Any un-delivered receipt?");
//		
//		System.out.println("Add a new receipt");
	}
}
