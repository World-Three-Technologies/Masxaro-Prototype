package com.android.W3T.app.rmanager;

import java.util.ArrayList;

import com.android.W3T.app.R;

import android.app.Activity;

public class ReceiptManager extends Activity {
	public static ArrayList<String[]> FakeReceipt;
	public final static String[][] FakeReceipts = {
		{"ID@1234", "06-01-2011", "Wendy's", "12.32USD"},
		{"ID@1234", "06-02-2011", "Starbucks", "4.63USD"},
		{"ID@1234", "06-02-2011", "J Street", "10.02USD"},
		{"ID@1234", "06-03-2011", "Starbucks", "4.63USD"},
		{"ID@1234", "06-03-2011", "Penn Grill", "8.76USD"},
		{"ID@1234", "06-04-2011", "Starbucks", "7.56USD"},
		{"ID@1234", "06-04-2011", "Wendy's", "6.22USD"}
	};
	
	public final static int[] ReceiptViewElements = {
		R.id.id_text, R.id.date_text, R.id.store_name_text, R.id.total_cost_text
	};
	
	public static void addNewReceipt() {
		System.out.println("Any un-delivered receipt?");
		
		System.out.println("Add a new receipt");
	}
}
