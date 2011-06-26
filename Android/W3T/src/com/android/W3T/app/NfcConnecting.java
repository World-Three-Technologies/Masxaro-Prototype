/*
 * NFCConnection.java -- Android app's NFC screen 
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
 *  Assumption: This activity is triggered by user when he is ready to retrieve the receipt
 *  from a vendor. When the retrieval finished, a verification screen will popup.
 */

package com.android.W3T.app;

import android.app.Activity;
import android.app.PendingIntent;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.IntentFilter.MalformedMimeTypeException;
import android.os.Bundle;
import android.view.KeyEvent;
import android.widget.Toast;
import android.nfc.NdefMessage;
import android.nfc.NdefRecord;
import android.nfc.NfcAdapter;
import com.android.W3T.app.nfc.*;

public class NfcConnecting extends Activity {
	private NfcAdapter mAdapter;
    private PendingIntent mPendingIntent;
    private IntentFilter[] mFilters;
    
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
        // Just showing a message in the center of the screen
		setContentView(R.layout.nfc_connecting);
		
		mAdapter = NfcAdapter.getDefaultAdapter(this);

        // Create a generic PendingIntent that will be deliver to this activity. The NFC stack
        // will fill in the intent with the details of the discovered tag before delivering to
        // this activity. @from sample code
        mPendingIntent = PendingIntent.getActivity(this, 0,
                new Intent(this, getClass()).addFlags(Intent.FLAG_ACTIVITY_SINGLE_TOP), 0);
        mPendingIntent = PendingIntent.getActivity(this, 0,
                new Intent(this, TagView.class).addFlags(Intent.FLAG_ACTIVITY_SINGLE_TOP), 0);
        // Setup an intent filter for all MIME based dispatches
        IntentFilter ndef = new IntentFilter(NfcAdapter.ACTION_TAG_DISCOVERED);
        try {
            ndef.addDataType("*/*");
        } catch (MalformedMimeTypeException e) {
            throw new RuntimeException("fail", e);
        }
        mFilters = new IntentFilter[] {
                ndef,
        };
    }
	
	@Override
    public void onResume() {
        super.onResume();
//        mAdapter.enableForegroundDispatch(new TagView(), mPendingIntent, mFilters, null);
        // Set filters as null. According to reference, that will make the system receive a 
        // TAG_DISCOVERED for all tags.
        
        mAdapter.enableForegroundDispatch(new TagView(), mPendingIntent, null, null);
    }
	
	@Override
    public void onPause() {
        super.onPause();
        mAdapter.disableForegroundDispatch(this);
        finish();
    }
	
	@Override
	public void onNewIntent(Intent intent) {
		// This is called to check where the PendingIntent is going.
		Toast.makeText(this, "NfcConnecting onNewIntent", Toast.LENGTH_SHORT).show();
	}
	
	@Override
	// Deal with any key press event
	public boolean onKeyUp (int keyCode, KeyEvent event) {
		switch (keyCode) {
		case KeyEvent.KEYCODE_DPAD_CENTER:
			final Intent tag_intent = new Intent(NfcConnecting.this, TagView.class);
			startActivity(tag_intent);
			System.out.println("Get a fake tag.");
			break;
		default:
			break;
		}
		return super.onKeyUp(keyCode, event);
	}
}
