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
import android.widget.Toast;

public class FrontPage extends Activity {
	public final static int MENU_ITEM = Menu.FIRST;
	public final static int DIALOG_LOGIN = 1;
	public final static int DIALOG_LOGOUT = 2;
	public final static boolean OFFLINE = false;
	public final static boolean ONLINE = true;
	
	public static boolean log_status = OFFLINE;
	
	private long mUptime;
	private long mDowntime;
	private Menu mMenu;
	private Dialog mLoginDialog;
	private AlertDialog mLogoutDialog;
	
	@Override
	public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.front_page);
	}
	
	@Override
	public Dialog onCreateDialog(int id) {
		super.onCreateDialog(id);

		switch(id) {
		case DIALOG_LOGIN:
			mLoginDialog = new Dialog(FrontPage.this);
			mLoginDialog.setContentView(R.layout.login_dialog);
			mLoginDialog.setTitle("Log In:");
			findViewById(R.id.sumbit_button).setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					mLoginDialog.cancel();
					// Send account info to server and show a progress bar
					ProgressDialog progresslog = new ProgressDialog(FrontPage.this);
					progresslog.setProgressStyle(ProgressDialog.STYLE_HORIZONTAL);
					progresslog.setMessage("Logging in...");
					progresslog.setCancelable(true);
					progresslog.show();
				}
			});
			findViewById(R.id.cancel_button).setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					mLoginDialog.cancel();
				}
			});
			return mLoginDialog;
		case DIALOG_LOGOUT:
			AlertDialog.Builder builder = new AlertDialog.Builder(this);
			builder.setMessage(getResources().getString(R.string.logout_promote))
			       .setCancelable(false)
			       .setPositiveButton("Yes", new DialogInterface.OnClickListener() {
			           public void onClick(DialogInterface dialog, int id) {
			        	   dialog.cancel();
			        	   // Send account info to server and show a progress bar
			        	   ProgressDialog progresslog = new ProgressDialog(FrontPage.this);
			        	   progresslog.setProgressStyle(ProgressDialog.STYLE_HORIZONTAL);
			        	   progresslog.setMessage("Logging out...");
			        	   progresslog.setCancelable(true);
			        	   progresslog.show();
			           }
			       })
			       .setNegativeButton("No", new DialogInterface.OnClickListener() {
			           public void onClick(DialogInterface dialog, int id) {
			        	   dialog.cancel();
			           }
			       });
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
        inflater.inflate(R.menu.front_page_menu, menu);

        return true;
    }
	
	@Override
	// All Toast messages are implemented later.
	public boolean onOptionsItemSelected(MenuItem item) {
		switch (item.getItemId()) {
		case R.id.view_receipt_option:
			Intent receipt_view_intent = new Intent(FrontPage.this, ReceiptsView.class);
			startActivity(receipt_view_intent);
			break;
		case R.id.search_option:
			Toast.makeText(this, "Start Search receipts activity!", Toast.LENGTH_SHORT).show();
			return true;
		case R.id.Log_option:
			showDialog(DIALOG_LOGIN);
			Toast.makeText(this, "Start log activity!", Toast.LENGTH_SHORT).show();
			return true;
		case R.id.view_coupon_option:
			Toast.makeText(this, "Start view coupon activity!", Toast.LENGTH_SHORT).show();
			return true;
		case R.id.conf_option:
			Toast.makeText(this, "Start configuration activity!", Toast.LENGTH_SHORT).show();
			return false;
		}
		return false;
		
	}
	
	@Override 
	public boolean onTouchEvent(MotionEvent event) {
		int action = event.getAction();
		long duration = 0;
		if (action == MotionEvent.ACTION_UP) {
			mUptime = event.getEventTime();
			duration = mUptime - mDowntime;
			if (duration >= 100) {
				openOptionsMenu();
//				System.out.println("Going to call menu bar activity.");
			}
		}
		else if(action == MotionEvent.ACTION_DOWN)
			mDowntime = event.getEventTime();
				
		return true;
	}
	
	@Override
	public boolean onKeyUp (int keyCode, KeyEvent event) {
		switch (keyCode) {
		case KeyEvent.KEYCODE_DPAD_CENTER:
			openOptionsMenu();
			System.out.println("Going to call menu bar activity.");
			return true;
		default:
			return false;
		}
	}
	
}
