/*
 * ReceiptView.java -- View the details of the receipts 
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
 *  NUM_RECEIPT receipts will be listed in the activity. The detail of one of them 
 *  will be shown.
 */

package com.android.W3T.app;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.util.Log;
import android.util.TypedValue;
import android.view.Gravity;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.TableLayout;
import android.widget.TableRow;
import android.widget.TextView;

import com.android.W3T.app.network.NetworkUtil;
import com.android.W3T.app.rmanager.*;
import com.android.W3T.app.user.UserProfile;

public class ReceiptsView extends Activity implements OnClickListener {
	public static final String TAG = "ReceiptsViewActivity";
	
	private static final String RECEIVE_ALL_BASIC = NetworkUtil.METHOD_RECEIVE_ALL_BASIC;
	
	private static final boolean FROM_DB = ReceiptsManager.FROM_DB;
	private static final boolean FROM_NFC = ReceiptsManager.FROM_NFC;
	
	private Button mSyncBtn;
	private Button mBackListBtn;
	
	private int mCurReceipt = 0;
	private ProgressDialog mRefreshProgress;
	private Handler mUpdateHandler = new Handler();
	private Runnable mReceiptThread = new Runnable() {
		@Override
		public void run() {
			Log.i(TAG, "retrieve receipts from database");
			// TODO: upload the receipt with FROM_NFC flag
			// Upload non-uploaded receipts and download latest 7 receipts from database. 
			// to the database.
//            NetworkUtil.syncUnsentReceipts();
			String jsonstr = NetworkUtil.attemptGetReceipt(RECEIVE_ALL_BASIC, UserProfile.getUsername(), null);
			if (jsonstr != null) {
				Log.i(TAG, "add new receipts basic");
				System.out.println(jsonstr);
				// Set the IsUpload true
//				ReceiptsManager.add(jsonstr, FROM_DB);
				Log.i(TAG, "finished new receipts");
				Log.i(TAG, "update receipt view");
				mRefreshProgress.dismiss();
			}
		}
	};
	
	@Override
	public void onCreate(Bundle savedInstanceState) {
		Log.i(TAG, "onCreate(" + savedInstanceState +")");
        super.onCreate(savedInstanceState);
        setContentView(R.layout.receipt_view);
		mSyncBtn = (Button) findViewById(R.id.sync_btn);
		mSyncBtn.setOnClickListener(this);
		mBackListBtn = (Button) findViewById(R.id.b_to_ls_btn);
		mBackListBtn.setOnClickListener(this);
	}
	
	@Override
	public void onResume() {
		Log.i(TAG, "onResume()");
		super.onResume();
		int id = this.getIntent().getIntExtra("num", 0);
		fillReceiptView(id);
	}
	
	@Override
	public void onClick(View v) {
		if (v == mSyncBtn) {
			Log.i(TAG, "handler post a new thread");
			// Show a progress bar and send account info to server.
			mRefreshProgress = new ProgressDialog(ReceiptsView.this);
			mRefreshProgress.setProgressStyle(ProgressDialog.STYLE_SPINNER);
			mRefreshProgress.setMessage("Refreshing...");
			mRefreshProgress.setCancelable(true);
			mRefreshProgress.show();
			mUpdateHandler.post(mReceiptThread);
		}
		else if (v == mBackListBtn) {
			setBackIntent();
		}
	}
	
/*	@Override
	// Thinking of using context menu to display the menu bar next time.
	public boolean onCreateOptionsMenu(Menu menu) {
        // Hold on to this
//        mMenu = menu;
        
        // Inflate the currently selected menu XML resource.
        MenuInflater inflater = getMenuInflater();
        inflater.inflate(R.menu.receipt_view_menu, menu);

        return true;
    }
	
	@Override
	// All Toast messages are implemented later.
	public boolean onOptionsItemSelected(MenuItem item) {
		Log.i(TAG, "onOptionsItemSelected(" + item + ")");
		switch (item.getItemId()) {
		case R.id.refresh_opt:
			Log.i(TAG, "handler post a new thread");
			// Show a progress bar and send account info to server.
			mRefreshProgress = new ProgressDialog(ReceiptsView.this);
			mRefreshProgress.setProgressStyle(ProgressDialog.STYLE_SPINNER);
			mRefreshProgress.setMessage("Refreshing...");
			mRefreshProgress.setCancelable(true);
			mRefreshProgress.show();
			mUpdateHandler.post(mReceiptThread);
			return true;
//		case R.id.sw_receipt_opt:
//			if (ReceiptsManager.getNumValid() != 0) {
//				setBackIntent();
//			}
//			Toast.makeText(this, "Switch to anther receipt view!", Toast.LENGTH_SHORT).show();
//			return true;
		case R.id.b_to_ls_opt:
			if (ReceiptsManager.getNumValid() != 0) {
				setBackIntent();
			}
			break;
		default:
			return false;
		}
		return false;
	}	
	*/
	@Override
	public boolean onKeyUp (int keyCode, KeyEvent event) {
		switch (keyCode) {
		case KeyEvent.KEYCODE_DPAD_LEFT:
			setContentView(R.layout.receipt_view);
			fillReceiptView(getPrevReceipt(mCurReceipt));
			break;
		case KeyEvent.KEYCODE_DPAD_RIGHT:
			setContentView(R.layout.receipt_view);
			fillReceiptView(getNextReceipt(mCurReceipt));
			break;
		case KeyEvent.KEYCODE_BACK:
			setBackIntent();
			break;
		default:
			break;
		}
		return super.onKeyUp(keyCode, event);
	}
	
	private int getPrevReceipt(int num) {
		mCurReceipt = (num+ReceiptsManager.getNumValid()-1)%ReceiptsManager.getNumValid();
		return (num+ReceiptsManager.getNumValid()-1)%ReceiptsManager.getNumValid();
	}
	
	private int getNextReceipt(int num) {
		mCurReceipt = (num+1)%ReceiptsManager.getNumValid();
		return (num+1)%ReceiptsManager.getNumValid();
	}
	
	private void fillReceiptView(int num) {
		fillBasicInfo(num);
		fillItemsRows(num);
	}
	
	private void fillBasicInfo(int num) {
		// Set all basicInfo entries: store name, id, time, tax, total .
		for (int i = 0;i < ReceiptsManager.NUM_RECEIPT_ENTRY;i++) {
			Receipt r = ReceiptsManager.getReceipt(num);
			((TextView) findViewById(ReceiptsManager.ReceiptViewElements[i]))
				.setText(r.getEntry(i));
			if (r.getWhere() == FROM_DB) {
				((ImageView) findViewById(R.id.receipt_sycn_flag)).setImageResource(R.drawable.sync);
			}
			else if (r.getWhere() == FROM_NFC) {
				((ImageView) findViewById(R.id.receipt_sycn_flag)).setImageResource(R.drawable.unsync);
			}
		}
	}
	
	private void fillItemsRows(int num) {
		// Set all item rows in the Items Table: id, name, qty, price. (no discount for now)
		int numItems = ReceiptsManager.getReceipt(num).getNumItem();
		int pos = 1;
		TableLayout t = (TableLayout) findViewById(R.id.items_table);
		for (int i=0;i<numItems;i++) {
			TableRow row1 = new TableRow(this);
			TextView itemId = new TextView(this);
			itemId.setText(String.valueOf(ReceiptsManager.getReceipt(num).getItem(i).getItemId()));
			itemId.setTextColor(getResources().getColor(R.color.black));
			itemId.setTextSize(TypedValue.COMPLEX_UNIT_SP, 15);
			itemId.setPadding(10, 0, 0, 0);
			row1.addView(itemId);
			
			TableRow row2 = new TableRow(this);
			TextView itemName = new TextView(this);
			TextView itemQty = new TextView(this);
			TextView itemPrice = new TextView(this);
			itemName.setText(ReceiptsManager.getReceipt(num).getItem(i).getName());
			itemName.setTextColor(getResources().getColor(R.color.black));
			itemName.setTextSize(TypedValue.COMPLEX_UNIT_SP, 15);
			itemQty.setText(String.valueOf(ReceiptsManager.getReceipt(num).getItem(i).getQty()));
			itemQty.setTextColor(getResources().getColor(R.color.black));
			itemQty.setTextSize(TypedValue.COMPLEX_UNIT_SP, 15);
			itemQty.setGravity(Gravity.RIGHT);
			itemQty.setPadding(0, 0, 10, 0);
			itemPrice.setText(String.valueOf(ReceiptsManager.getReceipt(num).getItem(i).getPrice()));
			itemPrice.setTextColor(getResources().getColor(R.color.black));
			itemPrice.setTextSize(TypedValue.COMPLEX_UNIT_SP, 15);
			itemPrice.setGravity(Gravity.RIGHT);
			itemPrice.setPadding(0, 0, 10, 0);
			row2.addView(itemName);
			row2.addView(itemQty);
			row2.addView(itemPrice);
			
			t.addView(row2, pos++);
			t.addView(row1, pos++);
		}
	}
	
	private void setBackIntent() {
		// Back to Receipt list activity
		Intent receipt_list_intent = new Intent(ReceiptsView.this, ReceiptsList.class);
		receipt_list_intent.addFlags(Intent.FLAG_ACTIVITY_NO_HISTORY);
		startActivity(receipt_list_intent);
	}
}
