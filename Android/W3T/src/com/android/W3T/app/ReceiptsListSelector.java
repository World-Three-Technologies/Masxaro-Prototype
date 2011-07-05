/*
 * ReceiptsListSelector.java -- Select a receipt from the list. 
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
 *  This activity provides a list view from which users could select a receipt,
 *  which is already in the receipt pool, to review its detail.
 */

package com.android.W3T.app;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import com.android.W3T.app.rmanager.Receipt;
import com.android.W3T.app.rmanager.ReceiptsManager;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.ListView;
import android.widget.SimpleAdapter;
import android.widget.TextView;
import android.widget.AdapterView.OnItemClickListener;

public class ReceiptsListSelector extends Activity {
	public static final String TAG = "ReceiptListView";
	
	public static final String SYNC_IMG = "entry_sync";
	public static final String TITLE_TEXT = "entry_title";
	public static final String TIME_TEXT = "entry_time";
	
	private static final boolean FROM_DB = ReceiptsManager.FROM_DB;
//	private static final boolean FROM_NFC = ReceiptsManager.FROM_NFC;
	
	private static final int ENTRY_STORE_NAME = Receipt.ENTRY_STORE_NAME;
	private static final int ENTRY_TIME = Receipt.ENTRY_TIME;
	
	private ListView mList;
	
	@Override
	public void onCreate(Bundle savedInstanceState) {  
        super.onCreate(savedInstanceState);
        setContentView(R.layout.receipt_selector);
    }
	
	@Override
	public void onResume() {
		super.onResume();
		mList = (ListView) findViewById(R.id.receipt_selector);  
        // Create the dynamic array, which includes all receipt entries.
		Log.i(TAG, "add category entry");
        ArrayList<HashMap<String, Object>> listItem = new ArrayList<HashMap<String, Object>>();
        HashMap<String, Object> category = new HashMap<String, Object>();
        category.put(SYNC_IMG, R.drawable.list_unsync);
        category.put(TITLE_TEXT, "Merchant");
        category.put(TIME_TEXT, "Date/Time");
        listItem.add(category);
        
        ArrayList<Receipt> unsentreceipts = ReceiptsManager.getUnSentReceipts();
        int num = unsentreceipts.size();
        for(int i=0;i<num;i++) {
        	Log.i(TAG, "add unsent receipt entry");
        	Receipt r = unsentreceipts.get(i);
            HashMap<String, Object> map = new HashMap<String, Object>();
            map.put(SYNC_IMG, R.drawable.list_unsync);
            map.put(TITLE_TEXT, r.getEntry(ENTRY_STORE_NAME));
            map.put(TIME_TEXT, r.getEntry(ENTRY_TIME));
            listItem.add(map);  
        }
        
        int num_receipt = ReceiptsManager.getNumValid();
        for(int i=0;i<num_receipt;i++) {
        	Receipt r = ReceiptsManager.getReceipt(i);
        	if (r.getWhere() == FROM_DB) {
        		Log.i(TAG, "add unsent receipt entry");
	            HashMap<String, Object> map = new HashMap<String, Object>();
	           	map.put(SYNC_IMG, R.drawable.list_sync);
	            map.put(TITLE_TEXT, r.getEntry(ENTRY_STORE_NAME));
	            map.put(TIME_TEXT, r.getEntry(ENTRY_TIME));
	            listItem.add(map);
            }
        }  
        
        // Create adapter's entries, which corresponds to the elements of the 
        // above dynamic array.  
        ReceiptListAdapter listAdapter = new ReceiptListAdapter(this,listItem,
            R.layout.receipt_selector_entry,
            new String[] {SYNC_IMG, TITLE_TEXT, TIME_TEXT},   
            new int[] {R.id.list_receipt_sync_flag,R.id.list_receipt_title, R.id.list_receipt_time}
        );
        
        mList.setAdapter(listAdapter);
        
        mList.setOnItemClickListener(new OnItemClickListener() {  
  			@Override
			public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
					long arg3) {
  				// TODO: Display the arg2th receipt in the receipt pool.
  				final Intent receipt_view_intent = new Intent(ReceiptsListSelector.this, ReceiptsView.class);
  				receipt_view_intent.addFlags(Intent.FLAG_ACTIVITY_REORDER_TO_FRONT);
  				startActivity(receipt_view_intent);
			}  
        });
	}
	
	// Set the category line's feature.
	private class ReceiptListAdapter extends SimpleAdapter {

		public ReceiptListAdapter(Context context,
				List<? extends Map<String, ?>> data, int resource,
				String[] from, int[] to) {
			super(context, data, resource, from, to);
		}
		
		public View getView(int position, View convertView, ViewGroup parent) {
			if (position == 0) {
				View row = super.getView(position, convertView, parent);
		        TextView title = (TextView) row.findViewById(R.id.list_receipt_title);
		        title.setTextColor(getResources().getColor(R.color.white));
		        return super.getView(position, convertView, parent);
			}
			return super.getView(position, convertView, parent);
			
	        
	    }
	}
}
