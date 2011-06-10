package com.android.W3T.app.rmanager;

import java.util.ArrayList;
import com.android.W3T.app.R;
import com.android.W3T.app.ReceiptsView;

public class ReceiptManager {
	public static ArrayList<String[]> FakeReceipt;
	public final static String[][] FakeReceipts = {
		{"ID@1234", "06-01-2011", "Wendy's", "12.32USD"},
		{"ID@1235", "06-02-2011", "Starbucks", "4.63USD"},
		{"ID@1236", "06-02-2011", "J Street", "10.02USD"},
		{"ID@1237", "06-03-2011", "Starbucks", "4.63USD"},
		{"ID@1238", "06-03-2011", "Penn Grill", "8.76USD"},
		{"ID@1239", "06-04-2011", "Starbucks", "7.56USD"},
		{"ID@1234", "06-04-2011", "Wendy's", "6.22USD"}
	};
	
	public final static int[] ReceiptViewElements = {
		R.id.id_txt, R.id.date_txt, R.id.store_name_txt, R.id.total_cost_txt
	};
	
	public ReceiptManager() {
		//for(int i=0;i<ReceiptsView.NUM_RECEIPT;i++)
		//FakeReceipt.add(i, )
	}
	
	public static void addNewReceipt() {
		System.out.println("Any un-delivered receipt?");
		
		System.out.println("Add a new receipt");
	}
}
