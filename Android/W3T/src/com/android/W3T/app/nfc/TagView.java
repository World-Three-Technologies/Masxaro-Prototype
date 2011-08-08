/*
 * TagView.java -- Viewing the tag after received one. 
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
 *  This activity just exists when the NFC link is connecting
 */

package com.android.W3T.app.nfc;

import com.android.W3T.app.NfcConnecting;
import com.android.W3T.app.R;
import com.android.W3T.app.ReceiptsList;
import com.android.W3T.app.network.NetworkUtil;
import com.android.W3T.app.rmanager.*;

import android.app.Activity;
import android.content.Intent;
import android.nfc.NfcAdapter;
import android.os.Bundle;
import android.os.Parcelable;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.Toast;

public class TagView extends Activity {
//	private static final boolean FROM_DB = ReceiptsManager.FROM_DB;
	private static final boolean FROM_NFC = ReceiptsManager.FROM_NFC;
	
//	private Receipt mReceipt;
	
	private Button mRejectBtn;
	private Button mConfirmBtn;
	@Override
	public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.received_tag_view);
        
        mRejectBtn = (Button)findViewById(R.id.receipt_reject_btn);
        mRejectBtn.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
				final Intent nfc_intent = new Intent(TagView.this, NfcConnecting.class);
				nfc_intent.addFlags(Intent.FLAG_ACTIVITY_REORDER_TO_FRONT);
				startActivity(nfc_intent);
				finish();
			}
        });
        mConfirmBtn = (Button)findViewById(R.id.receipt_confirm_btn);
        mConfirmBtn.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
				// TODO: Temporarily put here. get nfc tag from real world
				String jsonstr = 
					new String("[{\"store_account\":\"Mc_NYU\",\"id\":\"105\",\"user_account\":null,\"receipt_time\":\"2011-06-29 10:45:32\",\"tax\":\"1\",\"items\":[{\"item_price\":\"5\",\"item_name\":\"coke\",\"item_id\":\"12\",\"item_qty\":\"1\"},{\"item_price\":\"2\",\"item_name\":\"fries-mid\",\"item_id\":\"10\",\"item_qty\":\"1\"}],\"total_cost\":\"10\",\"img\":null,\"deleted\":0,\"store_name\":\"McDonalds(NYU)\", \"currency_mark\":\"$\"}]");
	            if (ReceiptsManager.getNumValid() == ReceiptsManager.NUM_RECEIPT) {
	            	ReceiptsManager.deleteReceipt(6);
	            }
				if (!ReceiptsManager.add(jsonstr, FROM_NFC)) {
					Toast.makeText(TagView.this, "cannot add more receipts into the pool", Toast.LENGTH_SHORT);
				}
	            if (!NetworkUtil.syncUnsentReceipts()) {
	            	Toast.makeText(TagView.this, "Sending receipts occurred error", Toast.LENGTH_SHORT);
	            }
	            else {
	            	setBackIntent();
	            	finish();
	            }
				
			}
        });
        // -------------- fake tag receive ---------------- //
	}
	
	@Override
	// Deal with any key press event
	public boolean onKeyUp (int keyCode, KeyEvent event) {
		switch (keyCode) {
		case KeyEvent.KEYCODE_BACK:
			final Intent tag_intent = new Intent(TagView.this, NfcConnecting.class);
			tag_intent.addFlags(Intent.FLAG_ACTIVITY_REORDER_TO_FRONT);
			startActivity(tag_intent);
			break;
		default:
			break;
		}
		return super.onKeyUp(keyCode, event);
	}
	
	@Override
    public void onNewIntent(Intent intent) {
		Toast.makeText(this, "TagView onNewIntent", Toast.LENGTH_SHORT).show();
        setIntent(intent);
        String action = intent.getAction();
        if (NfcAdapter.ACTION_NDEF_DISCOVERED.equals(action)) {
	        Parcelable[] rawMsgs = intent.getParcelableArrayExtra(NfcAdapter.EXTRA_TAG);
	        if (rawMsgs != null)
	        	Toast.makeText(this, "Is a nfc tag.", Toast.LENGTH_SHORT).show();
	        else
	        	Toast.makeText(this, "Not a nfc tag.", Toast.LENGTH_SHORT).show();
	        rawMsgs = intent.getParcelableArrayExtra(NfcAdapter.EXTRA_NDEF_MESSAGES);
	        if (rawMsgs != null)
	        	Toast.makeText(this, "Is a ndef tag.", Toast.LENGTH_SHORT).show();
	        else
	        	Toast.makeText(this, "Not a ndef tag.", Toast.LENGTH_SHORT).show();
        }
        else {
        	Toast.makeText(this, "Not a tag intent", Toast.LENGTH_SHORT).show();
        }
    }
	
	private void setBackIntent() {
		Intent receipt_list_intent = new Intent(TagView.this, ReceiptsList.class);
		receipt_list_intent.addFlags(Intent.FLAG_ACTIVITY_NO_HISTORY);
		startActivity(receipt_list_intent);
	}
}
