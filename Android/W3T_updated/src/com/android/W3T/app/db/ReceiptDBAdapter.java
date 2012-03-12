/*
 * ReceiptDBAdapter.java -- The adapter is used for the database of receipts. 
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
 */

package com.android.W3T.app.db;

import android.content.Context;
import android.database.SQLException;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;
import android.database.sqlite.SQLiteDatabase.CursorFactory;
import android.util.Log;

public class ReceiptDBAdapter {
	public static final String TAG = "ReceiptDBAdapter";
	
	private static final String DATABASE_NAME = "receiptDB.db";
	private static final String BASICINFO_TABLE = "basicInfo";
	private static final int DATABASE_VERSION = 1;
	
	public static final String KEY_ID = "_id";
	public static final String KEY_DATE_TIME = "receipt_time";
	public static final String KEY_STORE_NAME = "store_name";
	public static final String KEY_TAX = "tax";
	public static final String KEY_TOTAL = "total_cost";
	
	public static final int ID_COL = 0;
	public static final int DATE_TIME_COL = 1;
	public static final int STORE_NAME_COL = 2;
	public static final int TAX_COL = 3;
	public static final int TOTAL_COL = 4;
	
	private SQLiteDatabase db;
	private final Context context;
	private ReceiptDBOpenHelper dbHelper;
	
	public ReceiptDBAdapter(Context c) {
		context = c;
		dbHelper = new ReceiptDBOpenHelper(context, DATABASE_NAME, null, DATABASE_VERSION);
	}
	
	/* Open the database */
	public void open() throws SQLException {
		try {
			db = dbHelper.getWritableDatabase();
		}
		catch (SQLException s) {
			System.out.println("Database cannot be open writably");
			db = dbHelper.getReadableDatabase();
		}
	}
	
	/* Close the database */
	public void close() {
		db.close();
	}
	
	private static class ReceiptDBOpenHelper extends SQLiteOpenHelper {

		public ReceiptDBOpenHelper(Context context, String name,
				CursorFactory factory, int version) {
			super(context, name, factory, version);
		}
		
		/* SQL Statement to create a new database */
		private static final String DATABASE_CREATE = "create table " + 
			BASICINFO_TABLE + " (" + KEY_ID + " integer primary key, " +
			KEY_DATE_TIME + " text not null, " + KEY_STORE_NAME + " text not null, " +
			KEY_TAX + " real, " + KEY_TOTAL + "real);";
		
		@Override
		public void onCreate(SQLiteDatabase database) {
			database.execSQL(DATABASE_CREATE);
		}

		@Override
		public void onUpgrade(SQLiteDatabase database, int oldVersion, int newVersion) {
			Log.w(TAG, "Upgrading from version " + 
                    oldVersion + " to " +
                    newVersion + ", which will destroy all old data");

			// Drop the old table.
			database.execSQL("DROP TABLE IF EXISTS " + BASICINFO_TABLE);
			// Create a new one.
			onCreate(database);
		}
		
	}
}
