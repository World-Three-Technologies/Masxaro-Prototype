package com.android.W3T.app;

import com.android.W3T.app.network.NetworkUtil;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.util.Log;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.ImageButton;
import android.widget.ListView;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;
import android.widget.AdapterView.OnItemSelectedListener;

public class SearchView extends Activity implements OnClickListener {
	public static final String TAG = "SearchViewActivity";

	public static final int STORE_NAME = 0;
	public static final int ITEM_NAME = 1;
	public static final int TAG_NAME = 2;
	public static final int DATE = 3;
	
	private TextView t;
	
	
	private int mSearchBy;
	private Spinner mSearchSpinner;
	private ImageButton mSearchBtn;
	private ListView mResultList;
	private ProgressDialog mSearchProgress;
	private Handler mSearchHandler = new Handler();
	private Runnable mSearchThread = new Runnable() {
		@Override
		public void run() {
			switch (mSearchBy) {
			case STORE_NAME:
				Log.i(TAG, "search the terms by store name");
				t.setText(NetworkUtil.attemptSearch("key_search"));
				break;
			case ITEM_NAME:
				Log.i(TAG, "search the terms by item name");
				
				break;
			case TAG_NAME:
				Log.i(TAG, "search the terms by tag");
				
				break;
			case DATE:
				Log.i(TAG, "search the terms by date");
				
				break;
			default:
				break;
			}
			
			mSearchProgress.dismiss();
		}
	};
	
	
	@Override
	public void onCreate(Bundle savedInstanceState) {
	    super.onCreate(savedInstanceState);
	    setContentView(R.layout.receipt_search);
	    mSearchBtn = (ImageButton) findViewById(R.id.search_btn);
	    mSearchBtn.setOnClickListener(this);
	    
	    t=(TextView) findViewById(R.id.tmp);
	    
	    setSearchSpinner();
	}
	
	@Override
	public void onClick(View v) {
		if (v == mSearchBtn) {
			// Show a progress bar and do the search.
			mSearchProgress = new ProgressDialog(SearchView.this);
			mSearchProgress.setProgressStyle(ProgressDialog.STYLE_SPINNER);
			mSearchProgress.setMessage("Syncing...");
			mSearchProgress.setCancelable(true);
			mSearchProgress.show();
			mSearchHandler.post(mSearchThread);
		}
		
	}
	
	
	private void setSearchSpinner() {
		mSearchSpinner = (Spinner) findViewById(R.id.search_spinner);
	    ArrayAdapter<CharSequence> adapter = ArrayAdapter.createFromResource(
	            this, R.array.search_by, android.R.layout.simple_spinner_item);
	    adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
	    mSearchSpinner.setAdapter(adapter);
	    
	    mSearchSpinner.setOnItemSelectedListener(new OnItemSelectedListener() {
			@Override
			public void onItemSelected(AdapterView<?> parent, View view, int pos, long id) {
				mSearchBy = pos;
				Toast.makeText(parent.getContext(), "Search By " + parent.getItemAtPosition(pos).toString()
						, Toast.LENGTH_SHORT).show();
			}

			@Override
			public void onNothingSelected(AdapterView<?> parent) {
				mSearchBy = 0;
			}
	    });
	}
}
