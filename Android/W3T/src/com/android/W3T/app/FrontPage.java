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

public class FrontPage extends Activity {
	public static final String TAG = "FrontPageActivity";
	// Indicators for every dialog view.
	public static final int DIALOG_LOGIN = 1;
	public static final int DIALOG_LOGOUT = 2;
	
	private static final boolean OFF_LINE = UserProfile.OFFLINE;
	private static final boolean ON_LINE = UserProfile.ONLINE;
	
	private LinearLayout mFrontPage;
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
	
	private EditText mUnameEdit;
	private EditText mPwdEdit;
	
	private Button mSubmitBtn;
	private Button mCancelBtn;	
	
	@Override
	// Create the activity
	public void onCreate(Bundle savedInstanceState) {
		Log.i(TAG, "onCreate(" + savedInstanceState + ")");
        super.onCreate(savedInstanceState);
        setContentView(R.layout.front_page);
        
        Log.i(TAG, "Get FrontPage elements");
        mFrontPage = (LinearLayout) findViewById(R.id.front_page);
        mUname = (TextView) findViewById(R.id.Username);
        mFractalImg = (ImageView) findViewById(R.id.FractalFern);
        mLogProgress = new ProgressDialog(FrontPage.this);
	}
	
	@Override
	public void onResume() {
		super.onResume();
		Log.i(TAG, "onResume" + "Set FrontPage elements");
		setFrontPage(UserProfile.getUsername(), 0);
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
	// Thinking of using context menu to display the menu bar next time.
	public boolean onCreateOptionsMenu(Menu menu) {
		Log.i(TAG, "onCreateOptionMenu(" + menu + ")");
        // Hold on to this
        mMenu = menu;
        
        // Inflate the currently selected menu XML resource.
        MenuInflater inflater = getMenuInflater();
        inflater.inflate(R.menu.front_page_menu, mMenu);

        return true;
    }
	
	@Override
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
	// All Toast messages are implemented later.
	public boolean onOptionsItemSelected(MenuItem item) {
		Log.i(TAG, "onOptionItemSelected(" + item + ")");
		switch (item.getItemId()) {
		case R.id.view_receipt_opt:
			// Start the receipt view activity
			Log.i(TAG, "View receipt option selected");
			Log.i(TAG, "Set a new intent: ReceiptsView");
			final Intent receipt_view_intent = new Intent(FrontPage.this, ReceiptsView.class);
			receipt_view_intent.addFlags(Intent.FLAG_ACTIVITY_REORDER_TO_FRONT);
			startActivity(receipt_view_intent);
			break;
		case R.id.search_opt:
			Log.i(TAG, "Search receipt option selected");
			
			return true;
		case R.id.view_coupon_opt:
			Log.i(TAG, "View coupon option selected");
			
			return true;
		case R.id.conf_opt:
			Log.i(TAG, "Configuration option selected");
			
			return false;
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
				final Intent nfc_intent = new Intent(FrontPage.this, NfcConnecting.class);
				nfc_intent.addFlags(Intent.FLAG_ACTIVITY_REORDER_TO_FRONT);
				startActivity(nfc_intent);
			}
		}
		else if(action == MotionEvent.ACTION_DOWN)
			mDowntime = event.getEventTime();
				
		return true;
	}
	
	@Override
	// Deal with any key press event
	public boolean onKeyUp (int keyCode, KeyEvent event) {
		Log.i(TAG, "onKeyUp(" + event + ")");
		switch (keyCode) {
		case KeyEvent.KEYCODE_DPAD_CENTER:
			openOptionsMenu();
			break;
		default:
			break;
		}
		return super.onKeyUp(keyCode, event);
	}

	private void setLoginDialog() {
		// Login dialog is a custom dialog, we take care of every details of it.
		if (mLoginDialog == null) {
			mLoginDialog = new Dialog(FrontPage.this);
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
	
	private void setLoginListener() {
		// Deal with submit button click event
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
						Toast.makeText(FrontPage.this, "Please input user name", Toast.LENGTH_SHORT).show();
						mLoginDialog.show();
				    }
				    if (!pwdtext) {
						Toast.makeText(FrontPage.this, "Please input password", Toast.LENGTH_SHORT).show();
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
	
	private void setLogoutDialog() {
		// Logout dialog is an alert dialog. One message and two buttons on it.
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
//		        	   UserProfile.resetUserProfile(OFF_LINE, null);
//		        	   setFrontPage(null, 0);
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
	
	private void setFrontPage(String uname, int pic) {
		mUname.setText((CharSequence)uname);
        // TODO: set the front page's fractal fern image to indicate different status.
	}
		
	//--------------------- Log Thread ----------------------//
	private class LoginTask extends AsyncTask<Void, Void, Void> {
		private boolean isSuccessful = false; 
		@Override
		protected Void doInBackground(Void... params) {
			if (UserProfile.getStatus() == OFF_LINE) {
				isSuccessful = NetworkUtil.attemptLogin(mUnameEdit.getText().toString(),
						mPwdEdit.getText().toString(), DIALOG_LOGIN);
			}
			else if (UserProfile.getStatus() == ON_LINE) {
				isSuccessful = NetworkUtil.attemptLogin(UserProfile.getUsername(),
						null, DIALOG_LOGOUT);
			}
			return null;
		}
	    
	    protected void onPostExecute(Void result) {
	    	super.onPostExecute(result);
	    	String username = mUnameEdit.getText().toString();
	    	mLogProgress.dismiss();
	    	if (UserProfile.getStatus() == OFF_LINE) {
		    	if (isSuccessful) {
		    		UserProfile.resetUserProfile(ON_LINE, username);
			    	setFrontPage(username, 0);
			    	Toast.makeText(FrontPage.this, "Login succeeded!", Toast.LENGTH_SHORT).show();
		    	}
		    	else {
		    		mLoginDialog.show();
		    		Toast.makeText(FrontPage.this, "Login failed!", Toast.LENGTH_SHORT).show();
		    	}
	    	}
	    	else if (UserProfile.getStatus() == ON_LINE) {
	    		if (isSuccessful) {
	    			// TODO: clear all user data
	    			Log.i(TAG, "reset user profile");
		    		UserProfile.resetUserProfile(OFF_LINE, null);
		    		Log.i(TAG, "reset front page");
			    	setFrontPage("Not Login", 0);
			    	Log.i(TAG, "reset receipt manager");
			    	ReceiptsManager.clearReceiptPool();
			    	Log.i(TAG, "log out succeeded");
			    	Toast.makeText(FrontPage.this, "Logout succeeded!", Toast.LENGTH_SHORT).show();
		    	}
		    	else {
		    		mLogoutDialog.show();
		    		Toast.makeText(FrontPage.this, "Logout failed!", Toast.LENGTH_SHORT).show();
		    	}
	    	}
	    }
	}

}
