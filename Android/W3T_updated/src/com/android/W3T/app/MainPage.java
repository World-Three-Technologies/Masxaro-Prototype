/*
 * FrontPage.java -- Android app's entry and main control activity 
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
 *  This activity is the main entry. The menu bar of this activity control the other activities.
 */

package com.android.W3T.app;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.text.TextUtils;
import android.util.Log;
import android.view.KeyEvent;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.MotionEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.android.W3T.app.network.NetworkUtil;
import com.android.W3T.app.rmanager.ReceiptsManager;
import com.android.W3T.app.user.*;

public class MainPage extends Activity {
	public static final String TAG = "FrontPageActivity";
	// flags for every dialog view.
	public static final int DIALOG_LOGIN = 1;
	public static final int DIALOG_LOGOUT = 2;
	
	// flags for every logon/off status.
	private static final boolean OFF_LINE = UserProfile.OFFLINE;
	private static final boolean ON_LINE = UserProfile.ONLINE;
	
	// the content of the main page
	private LinearLayout mMainPage;
	private TextView mUname;
	private ImageView mFractalImg;
	
	// Screen touch event holding time
	private long mUptime;
	private long mDowntime;
	
	// Menu bar in this activity 
	private Menu mMenu;
	
	// Dialogs will show in this activity
	private Dialog mLoginDialog;
	private AlertDialog mLogoutDialog;
	private ProgressDialog mLogProgress;
	
	// Content on the login dialog
	private EditText mUnameEdit;
	private EditText mPwdEdit;
	
	// Buttons on the login dialog
	private Button mSubmitBtn;
	private Button mCancelBtn;	
	
	@Override
	// Create the activity
	public void onCreate(Bundle savedInstanceState) {
		Log.i(TAG, "onCreate(" + savedInstanceState + ")");
        super.onCreate(savedInstanceState);
        setContentView(R.layout.main_page);
        
        Log.i(TAG, "Get FrontPage elements");
        mMainPage = (LinearLayout) findViewById(R.id.main_page);
        mUname = (TextView) findViewById(R.id.Username);
        mFractalImg = (ImageView) findViewById(R.id.FractalFern);
        mLogProgress = new ProgressDialog(MainPage.this);
	}
	
	@Override
	public void onResume() {
		super.onResume();
		Log.i(TAG, "onResume" + "Set FrontPage elements");
		
		if (UserProfile.getStatus() == ON_LINE) {
			setMainPage(UserProfile.getUsername()+getResources().getString(R.string.masxaro_email), 0);
		}
		else {
			setMainPage("Not Login", 0);
		}
	}
		
	// Create the dialogs: 1.Login dialog; 2.Logout dialog
	@Override
	public Dialog onCreateDialog(int id) {
		super.onCreateDialog(id);
		Log.i(TAG, "onCreateDialog(" + id + ")");
		// Choose a certain dialog to create.
		switch(id) {
		case DIALOG_LOGIN:
			setLoginDialog();
			setLoginListener();
			return mLoginDialog;
		case DIALOG_LOGOUT:
			setLogoutDialog();
			return mLogoutDialog;
		default:
			return null;
		}
	}
		
	@Override
	// Create the option menus for main page.
	public boolean onCreateOptionsMenu(Menu menu) {
		Log.i(TAG, "onCreateOptionMenu(" + menu + ")");
        // Hold on to this
        mMenu = menu;
        
        // Inflate the currently selected menu XML resource.
        MenuInflater inflater = getMenuInflater();
        inflater.inflate(R.menu.main_page_menu, mMenu);

        return true;
    }
	
	@Override
	// Select a menu option group according to log status.
	public boolean onPrepareOptionsMenu(Menu menu)
	{
		Log.i(TAG, "onPrepareOptionMenu(" + menu + ")");
		super.onPrepareOptionsMenu(menu);
		if (UserProfile.getStatus() == OFF_LINE) {
			mMenu.setGroupVisible(R.id.group_login, true);
			mMenu.setGroupVisible(R.id.group_logout, false);
		}
		else {
			mMenu.setGroupVisible(R.id.group_login, false);
			mMenu.setGroupVisible(R.id.group_logout, true);
		}
		
		return true;
	}
	
	@Override
	// Menu options selected.
	public boolean onOptionsItemSelected(MenuItem item) {
		Log.i(TAG, "onOptionItemSelected(" + item + ")");
		switch (item.getItemId()) {
		case R.id.view_receipt_opt:
			// Start the receipt view activity
			Log.i(TAG, "View receipt option selected");
			if (ReceiptsManager.getNumValid() != 0) {
				final Intent receipt_list_intent = new Intent(MainPage.this, ReceiptsList.class);
				receipt_list_intent.addFlags(Intent.FLAG_ACTIVITY_REORDER_TO_FRONT);
				startActivity(receipt_list_intent);
			}
			else {
				final Intent empty_view_intent = new Intent(MainPage.this, EmptyView.class);
				empty_view_intent.addFlags(Intent.FLAG_ACTIVITY_NO_HISTORY);
				startActivity(empty_view_intent);
			}
			break;
		case R.id.search_opt:
			Log.i(TAG, "Search receipt option selected");
			final Intent receipt_search_intent = new Intent(MainPage.this, SearchView.class);
			receipt_search_intent.addFlags(Intent.FLAG_ACTIVITY_REORDER_TO_FRONT);
			startActivity(receipt_search_intent);
			return true;
//		case R.id.view_coupon_opt:
//			Log.i(TAG, "View coupon option selected");
//			
//			return true;
//		case R.id.conf_opt:
//			Log.i(TAG, "Configuration option selected");
//			
//			return false;
		case R.id.login_opt:
			Log.i(TAG, "Login option selected");
			// Pop up the login or the logout dialog
			showDialog(DIALOG_LOGIN);
			return true;
		case R.id.logout_opt:
			Log.i(TAG, "Logout option selected");
			// Pop up the login or the logout dialog
			showDialog(DIALOG_LOGOUT);
			return true;
		}
		Log.i(TAG, "No option selected");
		return false;
		
	}
	
	@Override
	// To prevent a mistaken touch, users are required to touch the screen for a little while.
	// Validate the touch screen event.
	public boolean onTouchEvent(MotionEvent event) {
		Log.i(TAG, "onTouchEvent(" + event +")");
		int action = event.getAction();
		long duration = 0;
		if (action == MotionEvent.ACTION_UP) {
			mUptime = event.getEventTime();
			duration = mUptime - mDowntime;
			if (duration >= 100 && UserProfile.getStatus() == ON_LINE) {
				// A valid touch screen event.
				Log.i(TAG, "Set a new intent: NfcConnecting");
				final Intent nfc_intent = new Intent(MainPage.this, NfcConnecting.class);
				nfc_intent.addFlags(Intent.FLAG_ACTIVITY_REORDER_TO_FRONT);
				startActivity(nfc_intent);
			}
		}
		else if (action == MotionEvent.ACTION_DOWN) {
			mDowntime = event.getEventTime();
		}
		return true;
	}
	
	@Override
	// Deal with any key press event
	public boolean onKeyUp (int keyCode, KeyEvent event) {
		Log.i(TAG, "onKeyUp(" + event + ")");
		switch (keyCode) {
//		case KeyEvent.KEYCODE_DPAD_CENTER:
//			openOptionsMenu();
//			break;
		case KeyEvent.KEYCODE_BACK:
			return true;
		default:
			break;
		}
		return super.onKeyUp(keyCode, event);
	}

	// Login dialog is a custom dialog, we take care of every details of it.
	private void setLoginDialog() {
		if (mLoginDialog == null) {
			mLoginDialog = new Dialog(MainPage.this);
			// Set the Login dialog view
			mLoginDialog.setContentView(R.layout.login_dialog);
			mLoginDialog.setTitle("Log In:");
			
			// IMPORTENT: Get button by id from login_dialog.xml, not from 
			// front_page.xml, which has no such component, "submit_btn".
			mSubmitBtn = (Button) mLoginDialog.findViewById(R.id.submit_btn);
			mCancelBtn = (Button) mLoginDialog.findViewById(R.id.cancel_btn);
			
			mUnameEdit = (EditText) mLoginDialog.findViewById(R.id.login_username);
	        mPwdEdit = (EditText) mLoginDialog.findViewById(R.id.login_password);
		}
	}
	
	// Deal with submit button click event
	private void setLoginListener() {
		mSubmitBtn.setOnClickListener(new OnClickListener() {
            public void onClick(View v) {
            	// Close the Login dialog when trying to log in.
				mLoginDialog.cancel();
				
            	boolean nametext = !TextUtils.isEmpty(mUnameEdit.getText());
            	boolean pwdtext = !TextUtils.isEmpty(mPwdEdit.getText());
            	if (nametext && pwdtext) {
					new LoginTask().execute(new Void[3]);
					// Show a progress bar and send account info to server.
					mLogProgress.setProgressStyle(ProgressDialog.STYLE_SPINNER);
					mLogProgress.setMessage("Logging in...");
					mLogProgress.setCancelable(true);
					mLogProgress.show();
				}
				else {
				    if (!nametext) {
						Toast.makeText(MainPage.this, "Please input user name", Toast.LENGTH_SHORT).show();
						mLoginDialog.show();
				    }
				    if (!pwdtext) {
						Toast.makeText(MainPage.this, "Please input password", Toast.LENGTH_SHORT).show();
						mLoginDialog.show();
				    }
				}
            }
        });

		mCancelBtn.setOnClickListener(new OnClickListener() {
            public void onClick(View v) {
            	// Close the Login dialog when trying to log in.
				mLoginDialog.cancel();
            }
		});
	}
	
	// Logout dialog is an alert dialog. One message and two buttons on it.
	// The event listeners are implemented in this method.
	private void setLogoutDialog() {
		AlertDialog.Builder builder = new AlertDialog.Builder(this);
		builder.setMessage(getResources().getString(R.string.logout_promote))
		       .setCancelable(false)
		       // Deal with logout button click event
		       .setPositiveButton("Yes", new DialogInterface.OnClickListener() {
		           public void onClick(DialogInterface dialog, int id) {
		        	   dialog.cancel();
		        	   new LoginTask().execute(new Void[3]);
		        	   // Show a progress bar and send log off signal to server.
		        	   mLogProgress.setProgressStyle(ProgressDialog.STYLE_SPINNER);
		        	   mLogProgress.setMessage("Logging out...");
		        	   mLogProgress.setCancelable(true);
		        	   mLogProgress.show();
		           }
		       })
		       .setNegativeButton("No", new DialogInterface.OnClickListener() {
		           public void onClick(DialogInterface dialog, int id) {
		        	   dialog.cancel();
		           }
		       });
		// create the Logout dialog.
		mLogoutDialog = builder.create();
	}
	
	// Set the content of the main page, including the user name and Fern image.
	private void setMainPage(String uname, int pic) {
		mUname.setText((CharSequence)uname);
        // TODO: set the front page's fractal fern image to indicate different status.
	}
		
	//--------------------- Log Thread ----------------------//
	private class LoginTask extends AsyncTask<Void, Void, Void> {
		private boolean isSuccessful = false; 
		@Override
		// Doing the logon/off operation in this async thread
		protected Void doInBackground(Void... params) {
			if (UserProfile.getStatus() == OFF_LINE) {
				// log on:
				isSuccessful = NetworkUtil.attemptLogin(mUnameEdit.getText().toString(),
						mPwdEdit.getText().toString(), DIALOG_LOGIN);
			}
			else if (UserProfile.getStatus() == ON_LINE) {
				// log off:
				isSuccessful = NetworkUtil.attemptLogin(UserProfile.getUsername(),
						null, DIALOG_LOGOUT);
			}
			return null;
		}
	    
		// Give the response to the result of logon/off operation.
	    protected void onPostExecute(Void result) {
	    	super.onPostExecute(result);
	    	String username = mUnameEdit.getText().toString();
	    	mLogProgress.dismiss();
	    	if (UserProfile.getStatus() == OFF_LINE) {
		    	if (isSuccessful) {
		    		// Prepare the ReceiptsManager and get the main page ready.
		    		UserProfile.resetUserProfile(ON_LINE, username);
		    		ReceiptsManager.initReceiptsManager();
		    		setMainPage(UserProfile.getUsername()+getResources().getString(R.string.masxaro_email), 0);
			    	Toast.makeText(MainPage.this, "Login succeeded!", Toast.LENGTH_SHORT).show();
		    	}
		    	else {
		    		mLoginDialog.show();
		    		Toast.makeText(MainPage.this, "Login failed!", Toast.LENGTH_SHORT).show();
		    	}
	    	}
	    	else if (UserProfile.getStatus() == ON_LINE) {
	    		if (isSuccessful) {
	    			// Clean all the stuffs of the previous user's.
	    			Log.i(TAG, "reset user profile");
		    		UserProfile.resetUserProfile(OFF_LINE, null);
		    		Log.i(TAG, "reset front page");
		    		setMainPage("Not Login", 0);
			    	Log.i(TAG, "reset receipt manager");
			    	ReceiptsManager.initReceiptsManager();
			    	Log.i(TAG, "log out succeeded");
			    	Toast.makeText(MainPage.this, "Logout succeeded!", Toast.LENGTH_SHORT).show();
		    	}
		    	else {
		    		mLogoutDialog.show();
		    		Toast.makeText(MainPage.this, "Logout failed!", Toast.LENGTH_SHORT).show();
		    	}
	    	}
	    }
	}
}
