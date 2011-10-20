/*
 * EmptyView.java -- View is shown when there is no receipt in the app. 
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
 */

package com.android.W3T.app;

import com.android.W3T.app.network.NetworkUtil;
import com.android.W3T.app.rmanager.ReceiptsManager;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.util.Log;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.Toast;

public class EmptyView extends Activity implements OnClickListener{
public static final String TAG = "EmptyViewActivity";
	
	private static final String RECEIVE_ALL = NetworkUtil.METHOD_RECEIVE_ALL;
	
	private static final boolean FROM_DB = ReceiptsManager.FROM_DB;
	
	private static final int SYNC_MESSAGE = 1;
	
	private Button mSyncBtn;
	private Button mBackMainBtn;
	
	private ProgressDialog mSyncProgress;
	private Handler mUpdateHandler = new Handler() {
        public void handleMessage(Message msg) {  
            super.handleMessage(msg);  
            switch (msg.what) {  
            case SYNC_MESSAGE:  
                Thread thread = new Thread(mReceiptThread);
                thread.start();  
                break;
            }  
        }  
	};
	private Runnable mReceiptThread = new Runnable() {
//GJP		@Override
		public void run() {
			Log.i(TAG, "retrieve receipts from database");
			// Download latest 7 receipts from database and upload non-uploaded receipts
			// to the database.
			String jsonstr = NetworkUtil.attemptGetReceipt(RECEIVE_ALL, null);
			if (jsonstr != null) {
//				System.out.println(jsonstr);
				// Set the IsUpload true
				if (!ReceiptsManager.add(jsonstr, FROM_DB)) {
					Toast.makeText(EmptyView.this, "cannot add more receipts into the pool", Toast.LENGTH_SHORT);
				}
				mSyncProgress.dismiss();
				if (ReceiptsManager.getNumValid() != 0) {
					Intent receipt_list_intent = new Intent(EmptyView.this, ReceiptsList.class);
					receipt_list_intent.addFlags(Intent.FLAG_ACTIVITY_NO_HISTORY);
					startActivity(receipt_list_intent);
					finish();
				}
				else {
					Toast.makeText(EmptyView.this, "No Receipts in the system yet", Toast.LENGTH_SHORT).show();
					Intent front_page_intent = new Intent(EmptyView.this, MainPage.class);
					front_page_intent.addFlags(Intent.FLAG_ACTIVITY_REORDER_TO_FRONT);
					startActivity(front_page_intent);
					finish();
				}
			}
		}
	};
	
	@Override
	public void onCreate(Bundle savedInstanceState) {
		Log.i(TAG, "onCreate(" + savedInstanceState +")");
        super.onCreate(savedInstanceState);
        setContentView(R.layout.empty_receipt_view);
		mSyncBtn = (Button) findViewById(R.id.sync_btn);
		mSyncBtn.setOnClickListener(this);
		mBackMainBtn = (Button) findViewById(R.id.back_main_btn);
		mBackMainBtn.setOnClickListener(this);
	}
	
//GJP	@Override
	public void onClick(View v) {
		if (v == mSyncBtn) {
			Log.i(TAG, "handler post a new thread");
			// Show a progress bar and send account info to server.
			mSyncProgress = new ProgressDialog(EmptyView.this);
			mSyncProgress.setProgressStyle(ProgressDialog.STYLE_SPINNER);
			mSyncProgress.setMessage("Syncing...");
			mSyncProgress.setCancelable(true);
			mSyncProgress.show();
			Message msg = new Message();
			msg.what = SYNC_MESSAGE;
			mUpdateHandler.sendMessage(msg);
		}
		else if (v == mBackMainBtn) {
			Intent front_page_intent = new Intent(EmptyView.this, MainPage.class);
			front_page_intent.addFlags(Intent.FLAG_ACTIVITY_REORDER_TO_FRONT);
			startActivity(front_page_intent);
			finish();
		}
	}
}
