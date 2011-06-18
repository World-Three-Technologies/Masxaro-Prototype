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

import com.android.W3T.app.R;
import com.android.W3T.app.ReceiptsView;
import com.android.W3T.app.rmanager.*;

import android.app.Activity;
import android.content.Intent;
import android.nfc.NfcAdapter;
import android.os.Bundle;
import android.os.Parcelable;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;

public class TagView extends Activity {
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
				finish();
			
			}
        });
        mConfirmBtn = (Button)findViewById(R.id.receipt_confirm_btn);
        mConfirmBtn.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
				Receipt r = new Receipt();
				r.setId(((TextView)findViewById(R.id.tag_id_txt)).getText().toString());
				r.setDate(((TextView)findViewById(R.id.tag_date_txt)).getText().toString());
				r.setTotal(((TextView)findViewById(R.id.tag_total_cost_txt)).getText().toString());
				r.setStoreName(((TextView)findViewById(R.id.tag_store_name_txt)).getText().toString());
				r.setValid(true);
				ReceiptsManager.addNewReceipt(r);
				setBackIntent();
				finish();
			}
        });
	}
	
	@Override
    public void onNewIntent(Intent intent) {
        setIntent(intent);
        String action = intent.getAction();
        if (NfcAdapter.ACTION_TAG_DISCOVERED.equals(action)) {
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
		Intent tag_intent = new Intent(TagView.this, ReceiptsView.class);
		tag_intent.addFlags(Intent.FLAG_ACTIVITY_REORDER_TO_FRONT);
		startActivity(tag_intent);
	}
}
