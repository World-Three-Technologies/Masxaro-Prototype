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

import java.io.IOException;

import com.android.W3T.app.NfcConnecting;
import com.android.W3T.app.R;
import com.android.W3T.app.ReceiptsList;
import com.android.W3T.app.ReceiptsView;
import com.android.W3T.app.network.NetworkUtil;
import com.android.W3T.app.rmanager.*;
import com.android.W3T.app.user.UserProfile;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Intent;
import android.nfc.FormatException;
import android.nfc.NdefMessage;
import android.nfc.NdefRecord;
import android.nfc.NfcAdapter;
import android.nfc.Tag;
import android.nfc.tech.Ndef;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.util.TypedValue;
import android.view.Gravity;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.TableLayout;
import android.widget.TableRow;
import android.widget.TextView;
import android.widget.Toast;

public class TagView extends Activity {
//	private static final boolean FROM_DB = ReceiptsManager.FROM_DB;
	private static final boolean FROM_NFC = ReceiptsManager.FROM_NFC;
	private static final boolean ONLINE = UserProfile.ONLINE;
	private static final boolean OFFLINE = UserProfile.OFFLINE;
	
	private static final int UPLOAD_MESSAGE = 1;
	
	private Receipt mReceipt;
	private NfcAdapter mAdapter;
	
	private TableLayout mItemsTable;
	private int mNumItemsInLast;
	
	private int[] ReceiptViewElements = {
		R.id.tag_store_name_txt, R.id.tag_time_txt, R.id.tag_id_txt, R.id.tag_tax_txt ,R.id.tag_total_cost_txt, R.id.tag_currency_txt
	};
	
	private ProgressDialog mUploadProgress;
	private Handler mUploadHandler = new Handler() {
		public void handleMessage(Message msg) {  
            super.handleMessage(msg);  
            switch (msg.what) {  
            case UPLOAD_MESSAGE:  
                Thread thread = new Thread(mUploadThread);
                thread.start();  
                break;
            }
        }
	};
	
	private Thread mUploadThread = new Thread() {
		@Override
		public void run() {
//			String jsonstr = new String("[{\"store_account\":\"Mc_NYU\",\"user_account\":\"new\",\"tax\":\"1\",\"items\":[{\"item_price\":\"5\",\"item_name\":\"coke\",\"item_id\":\"12\",\"item_qty\":\"1\"},{\"item_price\":\"2\",\"item_name\":\"fries-mid\",\"item_id\":\"10\",\"item_qty\":\"1\"}],\"total_cost\":\"10\",\"source\":\"nfc_tag\",\"store_name\":\"McDonalds(NYU)\",\"currency_mark\":\"$\",\"store_define_id\":\"123-456-7890\"}]");
//			if (ReceiptsManager.getNumValid() == ReceiptsManager.NUM_RECEIPT) {
//            	ReceiptsManager.deleteReceipt(6);
//            }
//			if (!ReceiptsManager.add(jsonstr, FROM_NFC)) {
//				Toast.makeText(TagView.this, "cannot add more receipts into the pool", Toast.LENGTH_SHORT);
//			}
            if (!NetworkUtil.syncUnsentReceipts()) {
            	mUploadProgress.dismiss();
//            	Toast.makeText(TagView.this, "Sending receipts occurred error", Toast.LENGTH_SHORT);
            }
            else {
            	mUploadProgress.dismiss();
            	setBackIntent();
            	finish();
            }
		}
	};
	
	private Button mRejectBtn;
	private Button mConfirmBtn;
	
	@Override
	public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        if (UserProfile.getStatus() == ONLINE) { 
	        setContentView(R.layout.received_tag_view);
	        
	        mItemsTable = (TableLayout) findViewById(R.id.items_table);
	        
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
					mUploadProgress = new ProgressDialog(TagView.this);
					mUploadProgress.setProgressStyle(ProgressDialog.STYLE_SPINNER);
					mUploadProgress.setMessage("Uploading to server...");
					mUploadProgress.setCancelable(true);
					mUploadProgress.show();
					Message msg = new Message();
					msg.what = UPLOAD_MESSAGE;
					mUploadHandler.sendMessage(msg);
				}
	        });
        
//mAdapter = NfcAdapter.getDefaultAdapter(this);
	        
//	        Tag t = (Tag) this.getIntent().getExtras().get(NfcAdapter.EXTRA_TAG);
//	        NdefMessage msg = (NdefMessage) getIntent().getParcelableArrayExtra(NfcAdapter.EXTRA_NDEF_MESSAGES)[0];
//		    NdefMessage nm = n.getNdefMessage();
//	        NdefRecord record = msg.getRecords()[0];
//			String result = new String(record.getPayload());

	    //mAdapter = NfcAdapter.getDefaultAdapter(this);
	        
	        Tag t = (Tag) this.getIntent().getExtras().get(NfcAdapter.EXTRA_TAG);
	        NdefMessage msg = (NdefMessage) getIntent().getParcelableArrayExtra(NfcAdapter.EXTRA_NDEF_MESSAGES)[0];
//		    NdefMessage nm = n.getNdefMessage();
	        NdefRecord record = msg.getRecords()[0];
			String result = new String(record.getPayload());
					        
			if (ReceiptsManager.getNumValid() == ReceiptsManager.NUM_RECEIPT) {
				ReceiptsManager.deleteReceipt(6);
			}
			if (ReceiptsManager.add(result.substring(1), FROM_NFC)) {	//skip the first letter.
				mReceipt = ReceiptsManager.getReceipt(ReceiptsManager.getNumValid()-1);
			}
			fillReceiptView();
	        
			if (ReceiptsManager.getNumValid() == ReceiptsManager.NUM_RECEIPT) {
				ReceiptsManager.deleteReceipt(6);
			}
			if (ReceiptsManager.add(result.substring(1), FROM_NFC)) {	//skip the first letter.
				mReceipt = ReceiptsManager.getReceipt(ReceiptsManager.getNumValid()-1);
			}
			fillReceiptView();
        }
        else {
        	Toast.makeText(this, "Please log in first.", Toast.LENGTH_SHORT).show();
        }
	}
	
	@Override
	public void onResume() {
		super.onResume();
//		if (UserProfile.getStatus() == OFFLINE){
//        	Toast.makeText(this, "Please log in first.", Toast.LENGTH_SHORT).show();
//        }
//		else {
////mAdapter = NfcAdapter.getDefaultAdapter(this);
//	        
////	        Tag t = (Tag) this.getIntent().getExtras().get(NfcAdapter.EXTRA_TAG);
//	        NdefMessage msg = (NdefMessage) getIntent().getParcelableArrayExtra(NfcAdapter.EXTRA_NDEF_MESSAGES)[0];
////		    NdefMessage nm = n.getNdefMessage();
//	        NdefRecord record = msg.getRecords()[0];
//			String result = new String(record.getPayload());
//					        
//			if (ReceiptsManager.getNumValid() == ReceiptsManager.NUM_RECEIPT) {
//				ReceiptsManager.deleteReceipt(6);
//			}
//			if (ReceiptsManager.add(result.substring(1), FROM_NFC)) {	//skip the first letter.
//				mReceipt = ReceiptsManager.getReceipt(ReceiptsManager.getNumValid()-1);
//			}
//			fillReceiptView();
//		}
	}
	
	@Override
	public void onPause() {
		super.onPause();
		finish();
	}
	
	@Override
	// Deal with any key press event
	public boolean onKeyUp (int keyCode, KeyEvent event) {
		switch (keyCode) {
		case KeyEvent.KEYCODE_BACK:
			final Intent tag_intent = new Intent(TagView.this, NfcConnecting.class);
			tag_intent.addFlags(Intent.FLAG_ACTIVITY_REORDER_TO_FRONT);
			startActivity(tag_intent);
			finish();
			break;
		default:
			break;
		}
		return super.onKeyUp(keyCode, event);
	}
	
	private void fillReceiptView() {
		fillBasicInfo();
		fillItemsRows();
	}
	
	private void fillBasicInfo() {
		// Set all basicInfo entries: store name, id, time, tax, total .
		for (int i = 0;i < ReceiptsManager.NUM_RECEIPT_BASIC;i++) {
			Receipt r = mReceipt;
			((TextView) findViewById(ReceiptViewElements[i]))
				.setText(r.getEntry(i));
//			if (r.getWhere() == FROM_DB) {
//				((ImageView) findViewById(R.id.receipt_sycn_flag)).setImageResource(R.drawable.sync);
//			}
//			else if (r.getWhere() == FROM_NFC) {
//				((ImageView) findViewById(R.id.receipt_sycn_flag)).setImageResource(R.drawable.unsync);
//			}
		}
	}
	
	private void fillItemsRows() {
		// Set all item rows in the Items Table: id, name, qty, price. (no discount for now)
		int numItems = mReceipt.getNumItem();
		int pos = 1;
		mItemsTable.removeViews(1, mNumItemsInLast * 2);
		for (int i=0;i<numItems;i++) {
			TableRow row1 = new TableRow(this);
			TextView itemId = new TextView(this);
			String id = mReceipt.getItem(i).getItemId();
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
			
			final String name = mReceipt.getItem(i).getName();
			itemName.setText(name);
			itemName.setTextColor(getResources().getColor(R.color.black));
			itemName.setTextSize(TypedValue.COMPLEX_UNIT_SP, 15);
			itemName.setWidth(170);
			
			itemName.setLines(1);
			itemName.setClickable(true);
			itemName.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					Toast.makeText(TagView.this, name, Toast.LENGTH_SHORT).show();
				}
			});
			
			itemQty.setText(String.valueOf(mReceipt.getItem(i).getQty()));
			itemQty.setTextColor(getResources().getColor(R.color.black));
			itemQty.setTextSize(TypedValue.COMPLEX_UNIT_SP, 15);
			itemQty.setGravity(Gravity.RIGHT);
			itemQty.setPadding(0, 0, 10, 0);
			
			itemPrice.setText(String.valueOf(mReceipt.getItem(i).getPrice()));
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
		Intent receipt_list_intent = new Intent(TagView.this, ReceiptsList.class);
		receipt_list_intent.addFlags(Intent.FLAG_ACTIVITY_NO_HISTORY);
		startActivity(receipt_list_intent);
	}
}
