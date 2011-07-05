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
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.widget.ImageView;
import android.widget.TableLayout;
import android.widget.TableRow;
import android.widget.TextView;
import android.widget.Toast;

import com.android.W3T.app.network.NetworkUtil;
import com.android.W3T.app.rmanager.*;

public class ReceiptsView extends Activity {
	public static final String TAG = "ReceiptsViewActivity";
	
	public static final String RECEIVE_ALL = "user_get_all_receipt";
	
	private static final boolean FROM_DB = ReceiptsManager.FROM_DB;
	private static final boolean FROM_NFC = ReceiptsManager.FROM_NFC;
	
	private int mCurReceipt = 0;
	private ProgressDialog mRefreshProgress;
	private Handler mUpdateHandler = new Handler();
	private Runnable mReceiptThread = new Runnable() {
		@Override
		public void run() {
			Log.i(TAG, "retrieve receipts from database");
			// TODO: upload the receipt with FROM_NFC flag
			// Download latest 7 receipts from database and upload non-uploaded receipts
			// to the database.
			String jsonstr = NetworkUtil.attemptGetReceipt(ReceiptsView.RECEIVE_ALL, "new");
			if (jsonstr != null) {
				Log.i(TAG, "add new receipts");
				// Set the IsUpload true
				ReceiptsManager.add(jsonstr, FROM_DB);
				Log.i(TAG, "finished new receipts");
				Log.i(TAG, "update receipt view");
				mRefreshProgress.dismiss();
				setBackIntent();
			}
		}
	};
	
	@Override
	public void onCreate(Bundle savedInstanceState) {
		Log.i(TAG, "onCreate(" + savedInstanceState +")");
        super.onCreate(savedInstanceState);
        noReceiptView();
	}
	
	@Override
	public void onResume() {
		Log.i(TAG, "onResume()");
		super.onResume();
		if (ReceiptsManager.getNumValid() != 0) {
			Log.i(TAG, "Receipts exist");
			setContentView(R.layout.receipt_view);
			fillReceiptView(0);
		}
	}
	
	@Override
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
		if (ReceiptsManager.getNumValid() != 0) {
			fillBasicInfo(num);
			fillItemsRows(num);
		}
		else {
			noReceiptView();
		}
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
		Intent receipt_list_intent = new Intent(ReceiptsView.this, ReceiptsListSelector.class);
		receipt_list_intent.addFlags(Intent.FLAG_ACTIVITY_NO_HISTORY);
		startActivity(receipt_list_intent);
	}
	
	private void noReceiptView() {
		Log.i(TAG, "Create a empty view");
		setContentView(R.layout.empty_receipt_view);
	}
}
