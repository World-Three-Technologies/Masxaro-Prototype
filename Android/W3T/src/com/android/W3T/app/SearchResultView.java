package com.android.W3T.app;

import org.json.JSONArray;
import org.json.JSONException;

import com.android.W3T.app.rmanager.Receipt;
import com.android.W3T.app.rmanager.ReceiptsManager;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
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

public class SearchResultView extends Activity implements OnClickListener {
	public static final String TAG = "SearchResultViewActivity";
	
	private Button mBackListBtn;
	
	private Receipt mReceipt;
	
	@Override
	public void onCreate(Bundle savedInstanceState) {
		Log.i(TAG, "onCreate(" + savedInstanceState +")");
        super.onCreate(savedInstanceState);
        setContentView(R.layout.search_result_view);
        
		mBackListBtn = (Button) findViewById(R.id.b_to_ls_btn);
		mBackListBtn.setOnClickListener(this);
		
		String detailstr = (String) getIntent().getSerializableExtra("detail");
		String itemsstr = (String) getIntent().getSerializableExtra("items");
		try {
			mReceipt = new Receipt((new JSONArray(detailstr)).getJSONObject(0), true);
			mReceipt.addItems(new JSONArray(itemsstr));
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}
	
	@Override
	public void onResume() {
		Log.i(TAG, "onResume()");
		super.onResume();
		Log.i(TAG, "handler post a new thread");
		fillReceiptView();
	}
	
	@Override
	public void onClick(View v) {
		if (v == mBackListBtn) {
			setBackIntent();
		}
	}
	
	@Override
	public boolean onKeyUp (int keyCode, KeyEvent event) {
		switch (keyCode) {
		case KeyEvent.KEYCODE_BACK:
			setBackIntent();
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
		for (int i = 0;i < ReceiptsManager.NUM_RECEIPT_ENTRY;i++) {
			((TextView) findViewById(ReceiptsManager.ReceiptViewElements[i]))
				.setText(mReceipt.getEntry(i));
		}
		((ImageView) findViewById(R.id.receipt_sycn_flag)).setImageResource(R.drawable.sync);
	}
	
	private void fillItemsRows() {
		// Set all item rows in the Items Table: id, name, qty, price. (no discount for now)
		int numItems = mReceipt.getNumItem();
		int pos = 1;
		TableLayout t = (TableLayout) findViewById(R.id.items_table);
		for (int i=0;i<numItems;i++) {
			TableRow row1 = new TableRow(this);
			TextView itemId = new TextView(this);
			itemId.setText(String.valueOf(mReceipt.getItem(i).getItemId()));
			itemId.setTextColor(getResources().getColor(R.color.black));
			itemId.setTextSize(TypedValue.COMPLEX_UNIT_SP, 15);
			itemId.setPadding(10, 0, 0, 0);
			row1.addView(itemId);
			
			TableRow row2 = new TableRow(this);
			TextView itemName = new TextView(this);
			TextView itemQty = new TextView(this);
			TextView itemPrice = new TextView(this);
			itemName.setText(mReceipt.getItem(i).getName());
			itemName.setTextColor(getResources().getColor(R.color.black));
			itemName.setTextSize(TypedValue.COMPLEX_UNIT_SP, 15);
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
			
			t.addView(row2, pos++);
			t.addView(row1, pos++);
		}
	}
	
	private void setBackIntent() {
		// Back to Receipt list activity
		Intent search_view_intent = new Intent(SearchResultView.this, SearchView.class);
		search_view_intent.addFlags(Intent.FLAG_ACTIVITY_REORDER_TO_FRONT);
		startActivity(search_view_intent);
		finish();
	}
}
