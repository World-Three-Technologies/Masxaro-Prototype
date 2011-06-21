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

import org.json.*;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.view.KeyEvent;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.widget.TextView;
import android.widget.Toast;

import com.android.W3T.app.network.NetworkUtil;
import com.android.W3T.app.rmanager.*;

public class ReceiptsView extends Activity {
	public static final String RECEIVE_ALL = "user_get_all_receipt";
	
	private int mCurReceipt = 0;
	private Handler mUpdateHandler = new Handler();
	private Runnable mReceiptThread = new Runnable() {
		@Override
		public void run() {
			String str = NetworkUtil.attemptGetReceipt(ReceiptsView.RECEIVE_ALL, "new");
			if (str != null)
				valueOfJSON(str);
		}
	};
	
	@Override
	public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.receipt_view);
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
		case R.id.refresh_opt:
			mUpdateHandler.post(mReceiptThread);
			Toast.makeText(this, "Refresh the receipt list!", Toast.LENGTH_SHORT).show();
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
			fillReceiptView(getPrevReceipt(mCurReceipt));
			break;
		case KeyEvent.KEYCODE_DPAD_RIGHT:
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
		for (int i = 0;i < ReceiptsManager.NUM_RECEIPT_ITEM;i++) {
			((TextView)findViewById(ReceiptsManager.ReceiptViewElements[i]))
				.setText(ReceiptsManager.getReceipts().get(num).getItem(i));
		}
	}
	
	private void valueOfJSON(String str) {
		/*
		 * [
		 * {"basicInfo":
		 * {"receipt_id":"1","receipt_time":"2011-06-15 09:08:42","tax":"0.1","total_cost":"14","store_name":"McDonalds(NYU)"},
		 * "items":
		 * [{"item_id":"10","item_name":"fries-mid","item_qty":"2","item_discount":"1","item_price":"2.25"},...]},
		 * 
		 * {"basicInfo...", "items"...}
		 * ]
		 */
		try {
			JSONArray receiptsArray = new JSONArray(str);
			int numReceipt = receiptsArray.length();
			
			JSONObject basicInfo[] = new JSONObject[numReceipt];
			JSONArray items[] = new JSONArray[numReceipt];
			for (int i=0;i < numReceipt;i++) {
				JSONObject tmp = (JSONObject) receiptsArray.get(i);
				basicInfo[i] = tmp.getJSONObject("basicInfo");
				items[i] = tmp.getJSONArray("items");
				System.out.println(basicInfo[i].toString());
				System.out.println(items[i].toString());
			}
			
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

	}
}
