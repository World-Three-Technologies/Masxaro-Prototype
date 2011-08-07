package com.android.W3T.app;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import com.android.W3T.app.network.NetworkUtil;
import com.android.W3T.app.rmanager.BasicInfo;
import com.android.W3T.app.user.UserProfile;

import android.app.Activity;
import android.app.DatePickerDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.app.DatePickerDialog.OnDateSetListener;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.View.OnClickListener;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.ImageButton;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.SimpleAdapter;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.AdapterView.OnItemSelectedListener;

public class SearchView extends Activity implements OnClickListener {
	public static final String TAG = "SearchViewActivity";

	private static final String RECEIVE_RECEIPT_DETAIL = "user_get_receipts_detail";
	private static final String RECEIVE_RECEIPT_ITEMS = "user_get_receipts_items";
	
	public static final String TITLE_TEXT = "entry_title";
	public static final String TIME_TEXT = "entry_time";
	public static final String TOTAL_TEXT = "entry_total";
	public static final String CURRENCY_TEXT = "entry_currency";
	
	public static final int STORE_ITEM = 0;
	public static final int TAG_NAME = 1;
//	public static final int DATE = 2;
	
	private static final int SEVEN_DAYS = 7;
	private static final int FOURTEEN_DAYS = 14;
	private static final int ONE_MONTH = 1;
	private static final int THREE_MONTHS = 3;
	private static final int CUSTOM = 0;
	
	private static final int START_DATE_DIALOG_ID = 1;
	private static final int END_DATE_DIALOG_ID = 2;
	
	private ArrayList<BasicInfo> basics = new ArrayList<BasicInfo>();
	private int mSearchBy = 0;
	private int mSearchRange = SEVEN_DAYS;
	
	private EditText mSearchTerms;
	private ImageButton mSearchBtn;
	
	private LinearLayout mDynamicSearchRange;
	private Spinner mSearchBySpinner;
	private Spinner mSearchRangeSpinner;
	private EditText mStartText;
	private String mStartDate;
	private EditText mEndText;
	private String mEndDate;
	
	private ListView mResultList;
	private ProgressDialog mSearchProgress;
	private Handler mSearchHandler = new Handler();
	private Runnable mSearchThread = new Runnable() {
		@Override
		public void run() {
			// TODO: upload the receipt with FROM_NFC flag
			// Upload non-uploaded receipts
			String result = new String();
			String text = mSearchTerms.getText().toString();
			String[] terms;
            NetworkUtil.syncUnsentReceipts();
			switch (mSearchBy) {
			case STORE_ITEM:
				if (mSearchRange == CUSTOM) {
					Log.i(TAG, "search the terms by key word and custom date");
					if (!mStartDate.equals("") && !mEndDate.equals("")) {
						text = text + " " + mStartDate + " " + mEndDate;
						terms = text.split(" ");
						result = NetworkUtil.attemptSearch("key_date_search", 0, terms);
						// Get the basic info of the hit receipts from the result
						// Create the result list.
						searchResultDecode(result);
						createSearchResultList();
					}
					else {
						Toast.makeText(SearchView.this, "Pleae select date", Toast.LENGTH_SHORT).show();
					}
				}
				else {
					Log.i(TAG, "search the terms by key word");
					if (!text.equals("")) {
						terms = text.split(" ");
						result = NetworkUtil.attemptSearch("key_search", 0-mSearchRange, terms);
					}
					else {
						result = NetworkUtil.attemptSearch("key_search", 0-mSearchRange, null);
					}
					searchResultDecode(result);
					createSearchResultList();
				}
				
				
				break;
			case TAG_NAME:
				Log.i(TAG, "search the terms by tag");
				
				break;
			default:
				break;
			}
			mSearchProgress.dismiss();
		}
	};
	
	Bundle mReceiptBundle = new Bundle();
	private String mId = new String();
	private ProgressDialog mDownloadProgress;
	private Handler mDownloadHandler = new Handler();
	private Runnable mDownloadThread = new Runnable() {
		@Override
		public void run() {
			Log.i(TAG, "retrieve receipts from database");
			
			String detailstr = null;
			detailstr = NetworkUtil.attemptGetReceipt(RECEIVE_RECEIPT_DETAIL, mId);
			String itemsstr = null;
			itemsstr = NetworkUtil.attemptGetReceipt(RECEIVE_RECEIPT_ITEMS, mId);
			mDownloadProgress.dismiss();
			if (detailstr != null && itemsstr != null) {
				Log.i(TAG, "add new receipts basic");
				mReceiptBundle.putSerializable("detail", detailstr);
				mReceiptBundle.putSerializable("items", itemsstr);
				final Intent search_result_intent = new Intent(SearchView.this, SearchResultView.class);
				search_result_intent.addFlags(Intent.FLAG_ACTIVITY_NO_HISTORY);
				search_result_intent.putExtras(mReceiptBundle);
				startActivity(search_result_intent);
			}
		}
	};
	
	@Override
	public void onCreate(Bundle savedInstanceState) {
	    super.onCreate(savedInstanceState);
	    setContentView(R.layout.receipt_search);
	    mSearchBtn = (ImageButton) findViewById(R.id.search_btn);
	    mSearchBtn.setOnClickListener(this);
	    
	    mSearchTerms = (EditText) findViewById(R.id.search_terms);
	    
	    mDynamicSearchRange = (LinearLayout) findViewById(R.id.dynamic_search_range);
	    setSearchSpinner();
	    
	    mResultList = (ListView) findViewById(R.id.search_result_list);
	}
	
	@Override
	public void onResume() {
		super.onResume();
		mStartDate = "";
	    mEndDate = "";
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
		// Set Search By spinner, which is not in the search_range_spinner.xml
		mSearchBySpinner = (Spinner) findViewById(R.id.search_by_spinner);
	    ArrayAdapter<CharSequence> adapter1 = ArrayAdapter.createFromResource(
	            this, R.array.search_by, android.R.layout.simple_spinner_item);
	    adapter1.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
	    mSearchBySpinner.setAdapter(adapter1);
	    
	    mSearchBySpinner.setOnItemSelectedListener(new OnItemSelectedListener() {
			@Override
			public void onItemSelected(AdapterView<?> parent, View view, int pos, long id) {
				mSearchBy = pos;
				Toast.makeText(parent.getContext(), "Search By " + parent.getItemAtPosition(pos).toString()
						, Toast.LENGTH_SHORT).show();
			}

			@Override
			public void onNothingSelected(AdapterView<?> parent) {
				
			}
	    });
	    
	    // Set Search Range spinner
	    mSearchRangeSpinner = (Spinner) findViewById(R.id.search_range_spinner);
	    ArrayAdapter<CharSequence> adapter2 = ArrayAdapter.createFromResource(
	            this, R.array.search_range, android.R.layout.simple_spinner_item);
	    adapter2.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
	    mSearchRangeSpinner.setAdapter(adapter2);
	    
	    mSearchRangeSpinner.setOnItemSelectedListener(new OnItemSelectedListener() {
			@Override
			public void onItemSelected(AdapterView<?> parent, View view, int pos, long id) {
				setDateRange(pos);
				if (pos < 4) {
					mDynamicSearchRange.removeAllViews();
				}
				else if (pos == 4) { // Custom
						View range =((LayoutInflater) getSystemService(Context.LAYOUT_INFLATER_SERVICE)).inflate(R.layout.search_range_view, null);
						mStartText = (EditText) range.findViewById(R.id.start_date);
						mStartText.setOnClickListener(new OnClickListener() {
							@Override
							public void onClick(View v) {
								showDialog(START_DATE_DIALOG_ID);
							}
						});
						mEndText = (EditText) range.findViewById(R.id.end_date);
						mEndText.setOnClickListener(new OnClickListener() {
							@Override
							public void onClick(View v) {
								showDialog(END_DATE_DIALOG_ID);
							}
						});
						mDynamicSearchRange.addView(range);
						
				}
				Toast.makeText(parent.getContext(), "Search Range " + parent.getItemAtPosition(pos).toString()
						, Toast.LENGTH_SHORT).show();
			}

			@Override
			public void onNothingSelected(AdapterView<?> parent) {
				
			}
	    });
//	    mDynamicSearchRange.addView(spinner);
	}
	
	private void setDateRange(int pos) {
    	switch (pos) {
		case 0:
			mSearchRange = SEVEN_DAYS;
			break;
		case 1:
			mSearchRange = FOURTEEN_DAYS;
			break;
		case 2:
			mSearchRange = ONE_MONTH;
			break;
		case 3:
			mSearchRange = THREE_MONTHS;
			break;
		case 4:
			mSearchRange = CUSTOM;
			break;
		default:
			break;
		}
    }
	
	private void searchResultDecode(String r) {
		// Clear the basics history.
		basics.clear();
		try {
			if (!r.equals("null")) {
				JSONArray result = new JSONArray(r);
				int num = result.length();
				for (int i=0;i<num;i++) {
					JSONObject b = result.getJSONObject(i);
					basics.add(new BasicInfo(b));
				}
			}
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		
	}
	
	private void createSearchResultList() {
		Log.i(TAG, "add category entry");
		if (basics.size() == 0) {
			Toast.makeText(SearchView.this, "No results returned", Toast.LENGTH_SHORT).show();
			mResultList.setAdapter(null);
		}
		else {
			
			mResultList.setVisibility(View.VISIBLE);
//			mResultList.postInvalidate();
			ArrayList<HashMap<String, Object>> listItem = new ArrayList<HashMap<String, Object>>();
	        
	        // First, add the category line.
	        HashMap<String, Object> category = new HashMap<String, Object>();
	        category.put(TITLE_TEXT, "Merchant");
	        category.put(TIME_TEXT, "Date");
	        category.put(CURRENCY_TEXT, " ");
	        category.put(TOTAL_TEXT, "Total");
	        listItem.add(category);
	        
	        // Secondly, add the result lines.
	        int num_result = basics.size();
	        for(int i=0;i<num_result;i++) {
		        HashMap<String, Object> map = new HashMap<String, Object>();
		        map.put(TITLE_TEXT, basics.get(i).getStoreName());
		        map.put(TIME_TEXT, basics.get(i).getTime().split(" ")[0]);
		        map.put(CURRENCY_TEXT, basics.get(i).getCurrency());
		        map.put(TOTAL_TEXT, basics.get(i).getTotal());
		        listItem.add(map);
	        }  
	        
	        // Create adapter's entries, which corresponds to the elements of the 
	        // above dynamic array.  
	        ResultListAdapter listAdapter = new ResultListAdapter(this, listItem, 
	            R.layout.search_result_entry,
	            new String[] {TITLE_TEXT, TIME_TEXT, CURRENCY_TEXT, TOTAL_TEXT},   
	            new int[] {R.id.list_search_title,R.id.list_search_time, R.id.list_search_currency, R.id.list_search_total}
	        );
	        
	        mResultList.setAdapter(listAdapter);
	        
	        mResultList.setOnItemClickListener(new OnItemClickListener() {  
	  			@Override
				public void onItemClick(AdapterView<?> parent, View view, int pos,
						long id) {
	  				mId = basics.get(pos-1).getId();
	  				mDownloadProgress = new ProgressDialog(SearchView.this);
	  				mDownloadProgress.setProgressStyle(ProgressDialog.STYLE_SPINNER);
	  				mDownloadProgress.setMessage("Downloading...");
	  				mDownloadProgress.setCancelable(true);
	  				mDownloadProgress.show();
	  				mDownloadHandler.post(mDownloadThread);
				}  
	        });	
		}
        
	}
	
	// the callback received when the user "sets" the date in the dialog
	@Override
	protected Dialog onCreateDialog(int id) {
	    switch (id) {
	    case START_DATE_DIALOG_ID:
	        return new DatePickerDialog(this,
	                    new OnDateSetListener() {
							@Override
							public void onDateSet(DatePicker view, int year,
									int monthOfYear, int dayOfMonth) {
								mStartDate = new StringBuilder()
								.append(year).append("-")
			                    // Month is 0 based so add 1
			                    .append(monthOfYear + 1).append("-")
			                    .append(dayOfMonth).toString();
			                    mStartText.setText(mStartDate);
							}
	        			},
	                    2011, 0, 1);
	    case END_DATE_DIALOG_ID:
	        return new DatePickerDialog(this,
		        		new OnDateSetListener() {
							@Override
							public void onDateSet(DatePicker view, int year,
									int monthOfYear, int dayOfMonth) {
								mEndDate = new StringBuilder()
								.append(year).append("-")
			                    // Month is 0 based so add 1
			                    .append(monthOfYear + 1).append("-")
			                    .append(dayOfMonth).toString();
								mEndText.setText(mEndDate);
							}
						},
		                2011, 0, 1);
	    }
	    return null;
	}
	
	// Set the category line's feature.
	private class ResultListAdapter extends SimpleAdapter {

		public ResultListAdapter(Context context,
				List<? extends Map<String, ?>> data, int resource,
				String[] from, int[] to) {
			super(context, data, resource, from, to);
		}
		
		public View getView(int position, View convertView, ViewGroup parent) {
			if (position == 0) {
				View row = super.getView(position, convertView, parent);
		        TextView title = (TextView) row.findViewById(R.id.list_search_title);
		        title.setTextColor(getResources().getColor(R.color.white));
		        TextView date = (TextView) row.findViewById(R.id.list_search_time);
		        date.setTextColor(getResources().getColor(R.color.white));
		        TextView total = (TextView) row.findViewById(R.id.list_search_total);
		        total.setTextColor(getResources().getColor(R.color.white));
		        return super.getView(position, convertView, parent);
			}
			return super.getView(position, convertView, parent);
	    }
	}
}
