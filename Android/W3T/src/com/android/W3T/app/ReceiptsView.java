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
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.util.Log;
import android.view.KeyEvent;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.widget.TableLayout;
import android.widget.TableRow;
import android.widget.TextView;
import android.widget.Toast;

import com.android.W3T.app.network.NetworkUtil;
import com.android.W3T.app.rmanager.*;

public class ReceiptsView extends Activity {
	public static final String TAG = "ReceiptsViewActivity";
	
	public static final String RECEIVE_ALL = "user_get_all_receipt";
	
//	public static final int EMPTY_VIEW_LAYOUT = R.id.empty_receipt_view;
//	public static final int RECEITP_VIEW_LAYOUT = R.id.receipt_view;
	
	private int mCurReceipt = 0;
	private Handler mUpdateHandler = new Handler();
	private Runnable mReceiptThread = new Runnable() {
		@Override
		public void run() {
			Log.i(TAG, "retrieve receipts from database");
			// Download latest 7 receipts from database and upload non-uploaded receipts
			// to the database.
			String jsonstr = NetworkUtil.attemptGetReceipt(ReceiptsView.RECEIVE_ALL, "new");
			if (jsonstr != null) {
				setContentView(R.layout.receipt_view);
				Log.i(TAG, "add new receipts");
				System.out.println(jsonstr);
				// Set the IsUpload true
				ReceiptsManager.add(jsonstr);
				Log.i(TAG, "finished new receipts");
				Log.i(TAG, "update receipt view");
				fillReceiptView(0);
				
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
		super.onResume();
		if (ReceiptsManager.getNumValid() != 0) {
			setContentView(R.layout.receipt_view);
			fillReceiptView(0);
		}
//		else {
//			noReceiptView();
//		}
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
			mUpdateHandler.post(mReceiptThread);
			return true;
		case R.id.sw_receipt_opt:
			Toast.makeText(this, "Switch to anther receipt view!", Toast.LENGTH_SHORT).show();
			return true;
		case R.id.b_to_fp_opt:
			setBackIntent();
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
	
	private void setBackIntent() {
		// Back to Front Page activity
		Intent front_page_intent = new Intent(ReceiptsView.this, FrontPage.class);
		front_page_intent.addFlags(Intent.FLAG_ACTIVITY_REORDER_TO_FRONT);
		startActivity(front_page_intent);
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
			TextView itemQty = new TextView(this);
			TextView itemPrice = new TextView(this);
			itemId.setText(String.valueOf(ReceiptsManager.getReceipt(num).getItem(i).getItemId()));
			itemQty.setText(String.valueOf(ReceiptsManager.getReceipt(num).getItem(i).getQty()));
			itemPrice.setText(String.valueOf(ReceiptsManager.getReceipt(num).getItem(i).getPrice()));
			row1.addView(itemId);
			row1.addView(itemQty);
			row1.addView(itemPrice);
			TableRow row2 = new TableRow(this);
			TextView itemName = new TextView(this);
			itemName.setText(ReceiptsManager.getReceipt(num).getItem(i).getName());
			itemName.setPadding(10, 0, 0, 0);
			row2.addView(itemName);
			t.addView(row1, pos++);
			t.addView(row2, pos++);
		}
		
	}
	
	private void noReceiptView() {
		Log.i(TAG, "Create a empty view");
		setContentView(R.layout.empty_receipt_view);
	}
}
