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
import android.os.Handler;
import android.view.KeyEvent;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.MotionEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import com.android.W3T.app.network.NetworkUtil;
import com.android.W3T.app.user.*;

public class FrontPage extends Activity {
	// Indicators for every dialog view.
	public final static int DIALOG_LOGIN = 1;
	public final static int DIALOG_LOGOUT = 2;
	
	private final static boolean OFF_LINE = UserProfile.OFFLINE;
	private final static boolean ON_LINE = UserProfile.ONLINE;
	
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
//	private ProgressDialog mLogProgress = new ProgressDialog(FrontPage.this);
	
	private Button mSubmitBtn;
	private Button mCancelBtn;	
	
	@Override
	// Create the activity
	public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.front_page);
        
        mUname = (TextView)findViewById(R.id.Username);
        mFractalImg = (ImageView)findViewById(R.id.FractalFern);
	}
	
	@Override
	public void onResume() {
		super.onResume();
		setFrontPage(UserProfile.getUname(), 0);
	}
	
	private void setFrontPage(String uname, int pic) {
		mUname.setText((CharSequence)uname);
        // TODO: set the front page's fractal fern image to indicate different status.
	}
	
	// Create the dialogs: 1.Login dialog; 2.Logout dialog
	@Override
	public Dialog onCreateDialog(int id) {
		super.onCreateDialog(id);
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
		switch (item.getItemId()) {
		case R.id.view_receipt_opt:
			// Start the receipt view activity
			final Intent receipt_view_intent = new Intent(FrontPage.this, ReceiptsView.class);
			receipt_view_intent.addFlags(Intent.FLAG_ACTIVITY_REORDER_TO_FRONT);
			startActivity(receipt_view_intent);
			break;
		case R.id.search_opt:
			Toast.makeText(this, "Start Search receipts activity!", Toast.LENGTH_SHORT).show();
			return true;
		case R.id.view_coupon_opt:
			Toast.makeText(this, "Start view coupon activity!", Toast.LENGTH_SHORT).show();
			return true;
		case R.id.conf_opt:
			Toast.makeText(this, "Start configuration activity!", Toast.LENGTH_SHORT).show();
			return false;
		case R.id.login_opt:
			// Pop up the login or the logout dialog
			showDialog(DIALOG_LOGIN);
			return true;
		case R.id.logout_opt:
			// Pop up the login or the logout dialog
			showDialog(DIALOG_LOGOUT);
			return true;
		}
		return false;
		
	}
	
	@Override
	// To prevent a mistaken touch, users are required to touch the screen for a little while.
	// Validate the touch screen event.
	public boolean onTouchEvent(MotionEvent event) {
		int action = event.getAction();
		long duration = 0;
		if (action == MotionEvent.ACTION_UP) {
			mUptime = event.getEventTime();
			duration = mUptime - mDowntime;
			if (duration >= 100 && UserProfile.getStatus() == ON_LINE) {
				// A valid touch screen event.
				final Intent nfc_intent = new Intent(FrontPage.this, NfcConnecting.class);
				nfc_intent.addFlags(Intent.FLAG_ACTIVITY_REORDER_TO_FRONT);
				startActivity(nfc_intent);
//				System.out.println("Going to call menu bar activity.");
			}
		}
		else if(action == MotionEvent.ACTION_DOWN)
			mDowntime = event.getEventTime();
				
		return true;
	}
	
	@Override
	// Deal with any key press event
	public boolean onKeyUp (int keyCode, KeyEvent event) {
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
		mLoginDialog = new Dialog(FrontPage.this);
		// Set the Login dialog view
		mLoginDialog.setContentView(R.layout.login_dialog);
		mLoginDialog.setTitle("Log In:");
		
		// IMPORTENT: Get button by id from login_dialog.xml, not from 
		// front_page.xml, which has no such component, "submit_btn".
		mSubmitBtn = (Button) mLoginDialog.findViewById(R.id.submit_btn);
		mCancelBtn = (Button) mLoginDialog.findViewById(R.id.cancel_btn);
	}
	
	private void setLoginListener() {
		// Deal with submit button click event
		mSubmitBtn.setOnClickListener(new OnClickListener() {
            public void onClick(View v) {
            	// Close the Login dialog when trying to log in.
				mLoginDialog.cancel();
				UserProfile.setUname(((TextView)mLoginDialog.findViewById(R.id.login_username))
						.getText().toString());
				setFrontPage(UserProfile.getUname(), 0);
				UserProfile.setStatus(ON_LINE);
				
				new LoginTask().execute(new Void[3]);
				// Show a progress bar and send account info to server.
//				ProgressDialog mLogProgress = new ProgressDialog(FrontPage.this);
//				mLogProgress.setProgressStyle(ProgressDialog.STYLE_HORIZONTAL);
//				mLogProgress.setMessage("Logging in...");
//				mLogProgress.setCancelable(true);
//				mLogProgress.show();
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
		        	   // Show a progress bar and send log off signal to server.
		        	   ProgressDialog progresslog = new ProgressDialog(FrontPage.this);
		        	   progresslog.setProgressStyle(ProgressDialog.STYLE_HORIZONTAL);
		        	   progresslog.setMessage("Logging out...");
		        	   progresslog.setCancelable(true);
		        	   progresslog.show();
		        	   UserProfile.setStatus(OFF_LINE);
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
	
	private class LoginTask extends AsyncTask<Void, Void, Void> {
	    /** The system calls this to perform work in a worker thread and
	      * delivers it the parameters given to AsyncTask.execute() */
		@Override
		protected Void doInBackground(Void... params) {
			NetworkUtil.attemptLogin(null);
			return null;
		}
	    
	    /** The system calls this to perform work in the UI thread and delivers
	      * the result from doInBackground() */
	    protected void onPostExecute(Void result) {
	    	Toast.makeText(FrontPage.this, "finished login!", Toast.LENGTH_SHORT).show();
	    }

		
	}
}
