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
import android.os.Message;
import android.view.KeyEvent;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.widget.TextView;
import android.widget.Toast;

public class ReceiptsView extends Activity {
	public final static int NUM_RECEIPT = 7;
	public final static int NUM_RECEIPT_ITEM = 4;
	
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
	
	private int mCurReceipt = 1;
	private Handler mUpdateHandler = new Handler() {  
	    public void handleMessage(Message msg) {
	    	super.handleMessage(msg);
	    	fillReceiptView(msg.getData().getInt("num"));
	    }
	};
	private Runnable mPrevUpdate = new Runnable() {
		@Override
		public void run() {
			int pos = getPrevReceipt(mCurReceipt);
			Bundle data = new Bundle();
			data.putInt("num", pos);
			Message msg = new Message();
			msg.setData(data);
			mUpdateHandler.sendMessage(msg);
		}
	};
	private Runnable mNextUpdate = new Runnable() {
		@Override
		public void run() {
			int pos = getNextReceipt(mCurReceipt);
			Bundle data = new Bundle();
			data.putInt("num", pos);
			Message msg = new Message();
			msg.setData(data);
			mUpdateHandler.sendMessage(msg);
		}
	};
	
	@Override
	public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.receipt_view);
//        mReceiptView = (LinearLayout)findViewById(R.id.receipt_view);
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
		switch (item.getItemId()) {
		case R.id.refresh_option:
			Toast.makeText(this, "Refresh the receipt list!", Toast.LENGTH_SHORT).show();
			return true;
		case R.id.sw_receipt_option:
			Toast.makeText(this, "Switch to anther receipt view!", Toast.LENGTH_SHORT).show();
			return true;
		case R.id.b_to_fp_option:
			// Back to Front Page activity
			Intent front_page_intent = new Intent(ReceiptsView.this, FrontPage.class);
			startActivity(front_page_intent);
			finish();
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
			new Thread(mPrevUpdate).start();
//			fillReceiptView(getPrevReceipt(mCurReceipt));
			break;
		case KeyEvent.KEYCODE_DPAD_RIGHT:
			new Thread(mNextUpdate).start();
//			fillReceiptView(getNextReceipt(mCurReceipt));
			break;
		default:
			break;
		}
		return super.onKeyUp(keyCode, event);
	}
	
	private int getPrevReceipt(int num) {
		mCurReceipt = (num+6)%NUM_RECEIPT;
//		System.out.println(mCurReceipt);
		return (num+6)%NUM_RECEIPT;
	}
	
	private int getNextReceipt(int num) {
		mCurReceipt = (num+1)%NUM_RECEIPT;
//		System.out.println(mCurReceipt);
		return (num+1)%NUM_RECEIPT;
	}
	
	private void fillReceiptView(int item_num) {
		for (int i = 0;i < NUM_RECEIPT_ITEM;i++) {
			((TextView)findViewById(ReceiptViewElements[i]))
				.setText(FakeReceipts[item_num][i]);
		}
	}
}
