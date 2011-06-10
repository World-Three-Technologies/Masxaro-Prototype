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
import android.widget.TextView;
import android.widget.Toast;
import com.android.W3T.app.user.*;

public class FrontPage extends Activity {
	// Indicators for every dialog view.
	public final static int DIALOG_LOGIN = 1;
	public final static int DIALOG_LOGOUT = 2;
	// Indicators for logged in or no one logged in.
	public final static boolean OFFLINE = false;
	public final static boolean ONLINE = true;
	// Flag for whether any user logged in or not
	public static boolean log_status = OFFLINE;
	
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
	}
	
	// Create the dialogs: 1.Login dialog; 2.Logout dialog
	@Override
	public Dialog onCreateDialog(int id) {
		super.onCreateDialog(id);
			
		// Choose a certain dialog to create.
		switch(id) {
		case DIALOG_LOGIN:
			// Login dialog is a custom dialog, we take care of every details of it.
			mLoginDialog = new Dialog(FrontPage.this);
			// Set the Login dialog view
			mLoginDialog.setContentView(R.layout.login_dialog);
			mLoginDialog.setTitle("Log In:");
			
			// Deal with submit button click event
			// IMPORTENT: Get button by id from login_dialog.xml, not from 
			// front_page.xml, which has no such component, "submit_btn".
			mSubmitBtn= (Button) mLoginDialog.findViewById(R.id.submit_btn);
			mSubmitBtn.setOnClickListener(new OnClickListener() {
	            public void onClick(View v) {
	            	// Close the Login dialog when trying to log in.
					mLoginDialog.cancel();
					// Show a progress bar and send account info to server.
//					ProgressDialog mLogProgress = new ProgressDialog(FrontPage.this);
//					mLogProgress.setProgressStyle(ProgressDialog.STYLE_HORIZONTAL);
//					mLogProgress.setMessage("Logging in...");
//					mLogProgress.setCancelable(true);
//					mLogProgress.show();
					((TextView) findViewById(R.id.Username))
						.setText(((TextView)mLoginDialog.findViewById(R.id.login_username))
								.getText());
					
	            	log_status = ONLINE;
	            }
	        });
			
			return mLoginDialog;
		case DIALOG_LOGOUT:
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
			        	   log_status = OFFLINE;
			           }
			       })
			       // 
			       .setNegativeButton("No", new DialogInterface.OnClickListener() {
			           public void onClick(DialogInterface dialog, int id) {
			        	   dialog.cancel();
			           }
			       });
			// create the Logout dialog.
			mLogoutDialog = builder.create();
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
		if (log_status == OFFLINE) {
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
			log_status = ONLINE;
			showDialog(DIALOG_LOGIN);
			Toast.makeText(this, "Start log activity!", Toast.LENGTH_SHORT).show();
			return true;
		case R.id.logout_opt:
			// Pop up the login or the logout dialog
			log_status = OFFLINE;
			showDialog(DIALOG_LOGOUT);
			Toast.makeText(this, "Start log activity!", Toast.LENGTH_SHORT).show();
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
			if (duration >= 100) {
				// A valid touch screen event.
				final Intent nfc_intent = new Intent(FrontPage.this, NFCConnecting.class);
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
}