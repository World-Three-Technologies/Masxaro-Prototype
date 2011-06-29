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

import com.android.W3T.app.rmanager.Receipt;
import com.android.W3T.app.rmanager.ReceiptsManager;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ListView;
import android.widget.SimpleAdapter;
import android.widget.AdapterView.OnItemClickListener;

public class ReceiptsListSelector extends Activity {
	public static final String TAG = "ReceiptListView";
	
	public static final String SYNC_IMG = "entry_sync";
	public static final String TITLE_TEXT = "entry_title";
	public static final String TIME_TEXT = "entry_time";
	
	private static final boolean FROM_DB = ReceiptsManager.FROM_DB;
	private static final boolean FROM_NFC = ReceiptsManager.FROM_NFC;
	
	private static final int ENTRY_STORE_NAME = Receipt.ENTRY_STORE_NAME;
	private static final int ENTRY_TIME = Receipt.ENTRY_TIME;
	
	private ListView mList;
	
	@Override
	public void onCreate(Bundle savedInstanceState) {  
        super.onCreate(savedInstanceState);
        setContentView(R.layout.receipt_selector);

        mList = (ListView) findViewById(R.id.receipt_selector);  
        // Create the dynamic array, which includes all receipt entries.
        ArrayList<HashMap<String, Object>> listItem = new ArrayList<HashMap<String, Object>>();
        int num_receipt = ReceiptsManager.getNumValid();
        for(int i=0;i<num_receipt;i++)  
        {
        	Receipt r = ReceiptsManager.getReceipt(i);
            HashMap<String, Object> map = new HashMap<String, Object>();
            if (r.getWhere() == FROM_NFC) {
            	map.put(SYNC_IMG, R.drawable.unsync);
            }
            else if (r.getWhere() == FROM_DB) {
            	map.put(SYNC_IMG, R.drawable.sync);
            }
            
            map.put(TITLE_TEXT, r.getEntry(ENTRY_STORE_NAME));
            map.put(TIME_TEXT, r.getEntry(ENTRY_TIME));
            listItem.add(map);  
        }  
        // Create adapter's entries, which corresponds to the elements of the 
        // above dynamic array.  
        SimpleAdapter listAdapter = new SimpleAdapter(this,listItem,
            R.layout.receipt_selector_entry,
            new String[] {SYNC_IMG, TITLE_TEXT, TIME_TEXT},   
            new int[] {R.id.list_receipt_sync_flag,R.id.list_receipt_title, R.id.list_receipt_time}  
        );  
        System.out.println(mList.toString());
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
}
