/*
 * AppTabs.java -- Android app's AppTabs screen 
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
 *  This activity includes 2 tabs to give two options: viewing user info or receipts 
 */

package com.android.W3T.app;

import android.app.TabActivity;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Spinner;
import android.widget.TabHost;
import android.widget.AdapterView.OnItemSelectedListener;

public class AppTabs extends TabActivity {
	private TabHost app_tabs_host;
	private Spinner sort_spinner;
	private ArrayAdapter<CharSequence> sort_ba;
	private Spinner receipt_list_spinner;
	private ArrayAdapter<CharSequence> receipt_list_ba;

	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.app_tabs);
		
		String user_tab = getResources().getString(R.string.user_info);
		String receipt_tab = getResources().getString(R.string.receipt_info);
		String conf_tab = getResources().getString(R.string.configuration);
		
		/* Set the TabHost */
		// Get the TabHost where to place tabs from the TabActivity
		app_tabs_host = this.getTabHost();
		
		LayoutInflater.from(this).inflate(R.layout.app_tabs, 
				app_tabs_host.getTabContentView(), true);
		
		app_tabs_host.addTab(app_tabs_host.newTabSpec(user_tab)
				.setIndicator(user_tab).setContent(R.id.user_info_tab));
		app_tabs_host.addTab(app_tabs_host.newTabSpec(receipt_tab)
				.setIndicator(receipt_tab).setContent(R.id.receipt_info_tab));
		app_tabs_host.addTab(app_tabs_host.newTabSpec(conf_tab)
				.setIndicator(receipt_tab).setContent(R.id.configuration_tab));
		
		/* Set sort spinner in receipt tab */
		sort_spinner = (Spinner)this.findViewById(R.id.receipt_sort_type_spinner);
		ArrayAdapter<CharSequence> sort_ba = ArrayAdapter.createFromResource(
                this, R.array.sort_types, android.R.layout.simple_spinner_item);
		sort_ba.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        sort_spinner.setAdapter(sort_ba);

		sort_spinner.setOnItemSelectedListener(new OnItemSelectedListener() {

			public void onItemSelected(AdapterView<?> parent, View view,
			int position, long id) { }

			public void onNothingSelected(AdapterView<?> parent) { }
			});
	}
}