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

import com.android.W3T.app.network.NetworkUtil;
import com.android.W3T.app.rmanager.Receipt;
import com.android.W3T.app.rmanager.ReceiptsManager;
import com.android.W3T.app.user.UserProfile;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.util.Log;
import android.view.View;
import android.view.ViewGroup;
import android.view.View.OnClickListener;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.ListView;
import android.widget.SimpleAdapter;
import android.widget.TextView;
import android.widget.AdapterView.OnItemClickListener;

public class ReceiptsList extends Activity implements OnClickListener {
	public static final String TAG = "ReceiptListView";
	
	public static final String SYNC_IMG = "entry_sync";
	public static final String TITLE_TEXT = "entry_title";
	public static final String TIME_TEXT = "entry_time";
	
	private static final boolean FROM_DB = ReceiptsManager.FROM_DB;
//	private static final boolean FROM_NFC = ReceiptsManager.FROM_NFC;
	
	private static final String RECEIVE_ALL_BASIC = NetworkUtil.METHOD_RECEIVE_ALL_BASIC;
	
	private static final int ENTRY_STORE_NAME = Receipt.ENTRY_STORE_NAME;
	private static final int ENTRY_TIME = Receipt.ENTRY_TIME;
	
	private ListView mList;
	private Button mSyncBtn;
	private Button mBackFrontBtn;
	
	private ProgressDialog mSyncProgress;
	private Handler mUpdateHandler = new Handler();
	private Runnable mReceiptThread = new Runnable() {
		@Override
		public void run() {
			Log.i(TAG, "retrieve receipts from database");
			// TODO: upload the receipt with FROM_NFC flag
            NetworkUtil.syncUnsentReceipts();
			// Download latest 7 receipts from database and upload non-uploaded receipts
			// to the database.
			String jsonstr = NetworkUtil.attemptGetReceiptBasic(RECEIVE_ALL_BASIC, UserProfile.getUsername());
			if (jsonstr != null) {
				Log.i(TAG, "add new receipts");
				// TODO: pick up the basic info of the latest 7 receipts and list them here.
//				System.out.println(jsonstr);
				// Set the IsUpload true
//				ReceiptsManager.add(jsonstr, FROM_DB);
				Log.i(TAG, "finished new receipts");
				Log.i(TAG, "update receipt view");
				mSyncProgress.dismiss();
				
				Intent receipt_list_intent = new Intent(ReceiptsList.this, ReceiptsList.class);
				receipt_list_intent.addFlags(Intent.FLAG_ACTIVITY_NO_HISTORY);
				startActivity(receipt_list_intent);
				finish();
			}
		}
	};
	
	@Override
	public void onCreate(Bundle savedInstanceState) {  
        super.onCreate(savedInstanceState);
        setContentView(R.layout.receipt_list);
        mSyncBtn = (Button) findViewById(R.id.sync_btn);
        mSyncBtn.setOnClickListener(this);
		mBackFrontBtn = (Button) findViewById(R.id.back_main_btn);
		mBackFrontBtn.setOnClickListener(this);
    }
	
	@Override
	public void onResume() {
		super.onResume();
		mList = (ListView) findViewById(R.id.receipt_list);  
        // Create the dynamic array, which includes all receipt entries.
		Log.i(TAG, "add category entry");
        ArrayList<HashMap<String, Object>> listItem = new ArrayList<HashMap<String, Object>>();
        HashMap<String, Object> category = new HashMap<String, Object>();
        category.put(SYNC_IMG, R.drawable.list_unsync);
//        category.put(TITLE_TEXT, getResources().getString(R.string.receipt_list_merchant));
//        category.put(TIME_TEXT, getResources().getString(R.string.receipt_list_date));
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
            R.layout.receipt_list_entry,
            new String[] {SYNC_IMG, TITLE_TEXT, TIME_TEXT},   
            new int[] {R.id.list_receipt_sync_flag,R.id.list_receipt_title, R.id.list_receipt_time}
        );
        
        mList.setAdapter(listAdapter);
        
        mList.setOnItemClickListener(new OnItemClickListener() {  
  			@Override
			public void onItemClick(AdapterView<?> parent, View view, int pos,
					long id) {
  				// TODO: should get the receipt id of the posth receipt here.
  				// TODO: check whether the posth receipt in the latest receipt pool 
  				// TODO: Display the posth receipt in the receipt pool.
  				final Intent receipt_view_intent = new Intent(ReceiptsList.this, ReceiptsView.class);
  				receipt_view_intent.addFlags(Intent.FLAG_ACTIVITY_NO_HISTORY);
  				receipt_view_intent.putExtra("pos", pos-1);
  				startActivity(receipt_view_intent);
			}  
        });
	}
	
	@Override
	public void onClick(View v) {
		if (v == mSyncBtn) {
			Log.i(TAG, "handler post a new thread");
			// Show a progress bar and send account info to server.
			mSyncProgress = new ProgressDialog(ReceiptsList.this);
			mSyncProgress.setProgressStyle(ProgressDialog.STYLE_SPINNER);
			mSyncProgress.setMessage("Syncing...");
			mSyncProgress.setCancelable(true);
			mSyncProgress.show();
			mUpdateHandler.post(mReceiptThread);
		}
		else if (v == mBackFrontBtn) {
			// Back to Front Page when there is no reciept
			Intent front_page_intent = new Intent(ReceiptsList.this, MainPage.class);
			front_page_intent.addFlags(Intent.FLAG_ACTIVITY_REORDER_TO_FRONT);
			startActivity(front_page_intent);
			finish();
		}
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
//		        return super.getView(position, convertView, parent);
			}
			return super.getView(position, convertView, parent);
	    }
	}
}
