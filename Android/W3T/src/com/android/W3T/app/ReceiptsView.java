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
import android.os.Message;
import android.util.Log;
import android.util.TypedValue;
import android.view.GestureDetector;
import android.view.Gravity;
import android.view.KeyEvent;
import android.view.MotionEvent;
import android.view.View;
import android.view.GestureDetector.OnGestureListener;
import android.view.View.OnClickListener;
import android.view.View.OnTouchListener;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ScrollView;
import android.widget.TableLayout;
import android.widget.TableRow;
import android.widget.TextView;
import android.widget.Toast;

import com.android.W3T.app.network.NetworkUtil;
import com.android.W3T.app.rmanager.*;

public class ReceiptsView extends Activity implements OnClickListener, 
	OnTouchListener, OnGestureListener {
	public static final String TAG = "ReceiptsViewActivity";
	
	private static final String RECEIVE_ALL = NetworkUtil.METHOD_RECEIVE_ALL;
	private static final int FLING_MIN_DISTANCE = 100;
	private static final int FLING_MIN_VELOCITY = 100;
	
	private static final boolean FROM_DB = ReceiptsManager.FROM_DB;
	private static final boolean FROM_NFC = ReceiptsManager.FROM_NFC;

	private static final int SYNC_MESSAGE = 1;
	
	private Button mSyncBtn;
	private Button mBackListBtn;
	
	private GestureDetector mGestureDetector;
	private TableLayout mItemsTable;
	private int mNumItemsInLast;
//	private LinearLayout mReceiptsView;
	
	private int mCurReceipt = 0;
	private ProgressDialog mSyncProgress;
	private Handler mUpdateHandler = new Handler() {
        public void handleMessage(Message msg)  
        {  
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
		@Override
		public void run() {
			Log.i(TAG, "retrieve receipts from database");
			// Download latest 7 receipts from database and upload non-uploaded receipts
			// to the database.
			if (!NetworkUtil.syncUnsentReceipts()) {
            	Toast.makeText(ReceiptsView.this, "Sending receipts occurred error", Toast.LENGTH_SHORT);
            }
			ReceiptsManager.initReceiptsManager();
			String jsonstr = NetworkUtil.attemptGetReceipt(RECEIVE_ALL, null);
			if (jsonstr != null) {
				Log.i(TAG, "add new receipts basic");
//				System.out.println(jsonstr);
				// Set the IsUpload true
				if (!ReceiptsManager.add(jsonstr, FROM_DB)) {
					Toast.makeText(ReceiptsView.this, "cannot add more receipts into the pool", Toast.LENGTH_SHORT);
				}
				Log.i(TAG, "finished new receipts");
				Log.i(TAG, "update receipt view");
				mSyncProgress.dismiss();
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
		
		((LinearLayout) findViewById(R.id.receipt_view)).setOnTouchListener(this);
		((LinearLayout) findViewById(R.id.receipt_view)).setLongClickable(true);
		((ScrollView) findViewById(R.id.fling)).setOnTouchListener(this);
		((ScrollView) findViewById(R.id.fling)).setLongClickable(true);
		mGestureDetector = new GestureDetector(this);
		
		mItemsTable = (TableLayout) findViewById(R.id.items_table);
	}
	
	@Override
	public void onResume() {
		Log.i(TAG, "onResume()");
		super.onResume();
		int id = this.getIntent().getIntExtra("pos", 0);
		fillReceiptView(id);
	}
	
	@Override
	public void onClick(View v) {
		if (v == mSyncBtn) {
			Log.i(TAG, "handler post a new thread");
			// Show a progress bar and send account info to server.
			mSyncProgress = new ProgressDialog(ReceiptsView.this);
			mSyncProgress.setProgressStyle(ProgressDialog.STYLE_SPINNER);
			mSyncProgress.setMessage("Syncing...");
			mSyncProgress.setCancelable(true);
			mSyncProgress.show();
			Message msg = new Message();
			msg.what = SYNC_MESSAGE;
			mUpdateHandler.sendMessage(msg);
		}
		else if (v == mBackListBtn) {
			setBackIntent();
		}
	}
	
	@Override
	public boolean onKeyUp (int keyCode, KeyEvent event) {
		switch (keyCode) {
//		case KeyEvent.KEYCODE_DPAD_LEFT:
//			setContentView(R.layout.receipt_view);
//			fillReceiptView(getPrevReceipt(mCurReceipt));
//			break;
//		case KeyEvent.KEYCODE_DPAD_RIGHT:
//			setContentView(R.layout.receipt_view);
//			fillReceiptView(getNextReceipt(mCurReceipt));
//			break;
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
		for (int i = 0;i < ReceiptsManager.NUM_RECEIPT_BASIC;i++) {
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
		mItemsTable.removeViews(1, mNumItemsInLast * 2);
		for (int i=0;i<numItems;i++) {
			TableRow row1 = new TableRow(this);
			TextView itemId = new TextView(this);
			String id = ReceiptsManager.getReceipt(num).getItem(i).getItemId();
			if (Integer.valueOf(id) != -1) {
				itemId.setText(id);
				itemId.setTextColor(getResources().getColor(R.color.black));
				itemId.setTextSize(TypedValue.COMPLEX_UNIT_SP, 15);
				itemId.setPadding(10, 0, 0, 0);
				row1.addView(itemId);
			}
			
			TableRow row2 = new TableRow(this);
			TextView itemName = new TextView(this);
			TextView itemQty = new TextView(this);
			TextView itemPrice = new TextView(this);
			
			final String name = ReceiptsManager.getReceipt(num).getItem(i).getName();
			itemName.setText(name);
			itemName.setTextColor(getResources().getColor(R.color.black));
			itemName.setTextSize(TypedValue.COMPLEX_UNIT_SP, 15);
			itemName.setWidth(170);
			
			itemName.setLines(1);
			itemName.setClickable(true);
			itemName.setOnClickListener(new OnClickListener() {

				@Override
				public void onClick(View v) {
					Toast.makeText(ReceiptsView.this, name, Toast.LENGTH_SHORT).show();
				}
				
			});
			
			itemQty.setText(String.valueOf(ReceiptsManager.getReceipt(num).getItem(i).getQty()));
			itemQty.setTextColor(getResources().getColor(R.color.black));
			itemQty.setTextSize(TypedValue.COMPLEX_UNIT_SP, 15);
			itemQty.setGravity(Gravity.RIGHT);
			itemQty.setPadding(0, 0, 10, 0);
			itemQty.setWidth(60);
			
			itemPrice.setText(String.valueOf(ReceiptsManager.getReceipt(num).getItem(i).getPrice()));
			itemPrice.setTextColor(getResources().getColor(R.color.black));
			itemPrice.setTextSize(TypedValue.COMPLEX_UNIT_SP, 15);
			itemPrice.setGravity(Gravity.RIGHT);
			itemPrice.setPadding(0, 0, 10, 0);
			
			row2.addView(itemName);
			row2.addView(itemQty);
			row2.addView(itemPrice);
			
			mItemsTable.addView(row2, pos++);
			mItemsTable.addView(row1, pos++);
		}
		mNumItemsInLast = numItems;
	}
	
	private void setBackIntent() {
		// Back to Receipt list activity
		Intent receipt_list_intent = new Intent(ReceiptsView.this, ReceiptsList.class);
		receipt_list_intent.addFlags(Intent.FLAG_ACTIVITY_NO_HISTORY);
		startActivity(receipt_list_intent);
		finish();
	}

	@Override
	public boolean onTouch(View v, MotionEvent e) {
		// OnGestureListener will analyzes the given motion event
		return mGestureDetector.onTouchEvent(e);
	}

	@Override
	public boolean onDown(MotionEvent e) {
		// TODO Auto-generated method stub
		return false;
	}

	@Override
	public boolean onFling(MotionEvent e1, MotionEvent e2, float velocityX,
			float velocityY) {
		if (e1.getX() - e2.getX() > FLING_MIN_DISTANCE
		        && Math.abs(velocityX) > FLING_MIN_VELOCITY) {
		    // Fling left
			fillReceiptView(getPrevReceipt(mCurReceipt));
		    Toast.makeText(this, "Fling Left", Toast.LENGTH_SHORT).show();
		} else if (e2.getX() - e1.getX() > FLING_MIN_DISTANCE
		        && Math.abs(velocityX) > FLING_MIN_VELOCITY) {
		    // Fling right
			fillReceiptView(getNextReceipt(mCurReceipt));
		    Toast.makeText(this, "Fling Right", Toast.LENGTH_SHORT).show();
		}
//		else Toast.makeText(this, "nothing", Toast.LENGTH_SHORT).show();
		return false;
	}

	@Override
	public void onLongPress(MotionEvent e) {
		// TODO Auto-generated method stub
		
	}

	@Override
	public boolean onScroll(MotionEvent e1, MotionEvent e2, float distanceX,
			float distanceY) {
		// TODO Auto-generated method stub
		return false;
	}

	@Override
	public void onShowPress(MotionEvent e) {
		// TODO Auto-generated method stub
		
	}

	@Override
	public boolean onSingleTapUp(MotionEvent e) {
		// TODO Auto-generated method stub
		return false;
	}
}
