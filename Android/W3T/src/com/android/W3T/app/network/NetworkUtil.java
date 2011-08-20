/*
 * NetworkUtil.java -- Network control class 
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
 *  The class is responsible for all network stuff: login/out connection,
 *  receipts retrieval and delivery.
 */

package com.android.W3T.app.network;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.io.UnsupportedEncodingException;
import java.net.URI;
import java.net.URISyntaxException;
import java.net.URL;
import java.net.URLConnection;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.List;

import org.apache.http.HttpResponse;
import org.apache.http.HttpStatus;
import org.apache.http.NameValuePair;
import org.apache.http.ParseException;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicNameValuePair;
import org.apache.http.util.EntityUtils;
import org.json.*;

import com.android.W3T.app.MainPage;
import com.android.W3T.app.rmanager.BasicInfo;
import com.android.W3T.app.rmanager.Receipt;
import com.android.W3T.app.rmanager.ReceiptsManager;
import com.android.W3T.app.user.UserProfile;

public class NetworkUtil {
	public static final int LOGIN = MainPage.DIALOG_LOGIN;
	public static final int LOGOUT = MainPage.DIALOG_LOGOUT;
	
	private static final boolean FROM_DB = ReceiptsManager.FROM_DB;
	
	public static final String BASE_URL = "http://50.19.213.157/masxaro/proj/php";
	public static final String LOGIN_URL = BASE_URL + "/login.php";
	public static final String LOGOUT_URL = BASE_URL + "/logoff.php";
	public static final String RECEIPT_OP_URL = BASE_URL + "/receiptOperation.php";
	
	// basic info entry index
	public static final int ENTRY_STORE_NAME = 0;
	public static final int ENTRY_TIME = 1;
	public static final int ENTRY_RECEIPT_ID = 2;
	public static final int ENTRY_TAX = 3;
	public static final int ENTRY_TOTAL = 4;
	public static final int ENTRY_CURRENCY = 5;
	public static final int ENTRY_CUT_DOWN = 6;
	public static final int ENTRY_EXTRA_COST = 7;
	public static final int ENTRY_SUB_COST = 8;
	public static final int ENTRY_ID = 9;
	public static final int ENTRY_STORE_ACC = 10;
	public static final int ENTRY_SOURCE = 11;
	
	// Receipt View
	public static final String METHOD_RECEIVE_ALL = "user_get_all_receipt";
	// Search function: search list
	public static final String METHOD_RECEIVE_RECEIPT_DETAIL = "user_get_receipts_detail";
	public static final String METHOD_RECEIVE_RECEIPT_ITEMS = "user_get_receipts_items";
	// Search function: search term
	public static final String METHOD_KEY_SEARCH = "key_search";
	public static final String METHOD_TAG_SEARCH = "tag_search";
	public static final String METHOD_KEY_DATE_SEARCH = "key_date_search";
	// Send function
	public static final String METHOD_SEND_RECEIPT = "new_receipt";
	
	private static final int SEVEN_DAYS = 7;
	private static final int FOURTEEN_DAYS = 14;
	private static final int ONE_MONTH = 1;
	private static final int THREE_MONTHS = 3;
	
	private static boolean checkNetwork() {
		return true;
	}
	
	public static boolean syncUnsentReceipts() {
		ArrayList<Receipt> receipts = ReceiptsManager.getUnSentReceipts();
        int num = receipts.size();
        for (int i=0;i<num;i++) {
        	int ret = NetworkUtil.attemptSendReceipt(METHOD_SEND_RECEIPT, receipts.get(i));
        	if (ret > 0) {
        		String detailstr = null;
    			detailstr = NetworkUtil.attemptGetReceipt(METHOD_RECEIVE_RECEIPT_DETAIL, String.valueOf(ret));
//    			String itemsstr = null;
//    			itemsstr = NetworkUtil.attemptGetReceipt(METHOD_RECEIVE_RECEIPT_ITEMS, String.valueOf(ret));
    			String time = null;
				try {
					time = (new JSONArray(detailstr)).getJSONObject(0).getString("receipt_time");
				} catch (JSONException e) {
					e.printStackTrace();
				}
    			receipts.get(i).getBasicInfo().setTime(time);
    			receipts.get(i).getBasicInfo().setId(String.valueOf(ret));
    			
        		receipts.get(i).setWhere(FROM_DB);
        	}
        	else {
        		return false;
        	}
        }
        return true;
	}
	
	/*
	 * This method is for attempting to login or logout.
	 * With the uname and pwd, the method will try to login/out.
	 * And the parameter op decides which operation it is.
	 * 
	 * @param String uname	user name used to login/out
	 *        String pwd	password used to login/out
	 *        int op 		whether this operation is login or logout
	 *        
	 * @return boolean 	whether the login/out is successful.
	 */
	
	public static boolean attemptLogin(String uname, String pwd, int op) {   
		// Here we may want to check the network status.
		checkNetwork();

		HttpClient Client = new DefaultHttpClient();
        HttpPost request;
        
        try {
            request = new HttpPost();
        	if (op == LOGIN) {
        		request.setURI(new URI(LOGIN_URL));
        		// Add your data
	            List<NameValuePair> nameValuePairs = new ArrayList<NameValuePair>(3);
	            nameValuePairs.add(new BasicNameValuePair("acc", uname));
	            nameValuePairs.add(new BasicNameValuePair("pwd", pwd));
	            nameValuePairs.add(new BasicNameValuePair("type", "user"));
	            request.setEntity(new UrlEncodedFormEntity(nameValuePairs));
        	}
        	else if (op == LOGOUT) {
        		request.setURI(new URI(LOGOUT_URL));
        		List<NameValuePair> nameValuePairs = new ArrayList<NameValuePair>(3);
	            nameValuePairs.add(new BasicNameValuePair("acc", uname));
	            request.setEntity(new UrlEncodedFormEntity(nameValuePairs));
        	}
            // Execute HTTP Post Request
            HttpResponse response = Client.execute(request);
            
            if (response.getStatusLine().getStatusCode() == HttpStatus.SC_OK) {
            	if (op == LOGOUT) {
            		return true;
            	}
            	String s = EntityUtils.toString(response.getEntity());
            	if (s.equals("1")) {
            		return true;
            	}
            	else {
            		return false;
            	}
            }
            return false;
        } catch (URISyntaxException e) {   
            e.printStackTrace();   
         
        } catch (UnsupportedEncodingException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (ClientProtocolException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		return false;
    }

	// return null means something wrong.
	public static String attemptGetReceipt(String op, String id) {
		// Here we may want to check the network status.
		checkNetwork();
		
		try {
			JSONObject jsonstr = new JSONObject();
			JSONObject param = new JSONObject();
			try {
				param.put("opcode", op);
				param.put("acc", UserProfile.getUsername());
				if (op.equals(METHOD_RECEIVE_ALL)) {
			        // Add your data
			        param.put("limitStart", "0");
			        param.put("limitOffset", "7");
				}
				else if (op.equals(METHOD_RECEIVE_RECEIPT_DETAIL)) {
					// Add your data
					JSONArray rid = new JSONArray();
					rid.put(Integer.valueOf(id));
		            param.put("receiptIds", rid);
		            
				}
				else if (op.equals(METHOD_RECEIVE_RECEIPT_ITEMS)) {
					// Add your data
					JSONArray rid = new JSONArray();
					rid.put(Integer.valueOf(id));
		            param.put("receiptIds", rid);
				}
				jsonstr.put("json", param);
			} catch (JSONException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
			URL url = new URL(RECEIPT_OP_URL);
        	URLConnection connection = url.openConnection();
        	connection.setDoOutput(true);
        	OutputStreamWriter out = new OutputStreamWriter(connection.getOutputStream(), "UTF-8");
        	// Must put "json=" here for server to decoding the data
        	String data = "json=" + jsonstr.toString();
        	out.write(data);
        	out.flush();
        	out.close();

        	BufferedReader in = new BufferedReader(new InputStreamReader(connection.getInputStream(), "UTF-8"));
        	String s = in.readLine();
        	
        	System.out.println("get "+s);
        	
        	return s;
            
		} catch (UnsupportedEncodingException e) {
			e.printStackTrace();
		} catch (ParseException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}
		return null;
	}
	
	public static int attemptSendReceipt(String op, Receipt r) {
		// Here we may want to check the network status.
		checkNetwork();
        try {
        	JSONObject jsonstr = new JSONObject();
        	
        	if (op.equals(METHOD_SEND_RECEIPT)) {
        		// Add your data
            	JSONObject basicInfo = new JSONObject();
            	basicInfo.put("store_account", r.getEntry(ENTRY_STORE_ACC));// store name
            	basicInfo.put("currency_mark", r.getEntry(ENTRY_CURRENCY));
            	basicInfo.put("store_define_id", r.getEntry(ENTRY_RECEIPT_ID));
            	basicInfo.put("source", r.getEntry(ENTRY_SOURCE));
            	basicInfo.put("tax", r.getEntry(ENTRY_TAX));				// tax
            	basicInfo.put("total_cost", r.getEntry(ENTRY_TOTAL));		// total price
            	basicInfo.put("user_account", UserProfile.getUsername());
            	JSONObject receipt = new JSONObject();
            	receipt.put("receipt", basicInfo);
            	receipt.put("items", r.getItemsJsonArray());
            	receipt.put("opcode", op);
            	receipt.put("acc", UserProfile.getUsername());
            	jsonstr.put("json", receipt);
        	}
        	URL url = new URL(RECEIPT_OP_URL);
        	URLConnection connection = url.openConnection();
        	connection.setDoOutput(true);

        	OutputStreamWriter out = new OutputStreamWriter(connection.getOutputStream(), "UTF-8");
        	// Must put "json=" here for server to decoding the data
        	String data = "json="+jsonstr.toString();
        	out.write(data);
        	out.flush();
        	out.close();
        	
        	BufferedReader in = new BufferedReader(new InputStreamReader(connection.getInputStream(), "UTF-8"));
        	String s = in.readLine();
        	System.out.println(s);
        	if (Integer.valueOf(s) > 0) {
        		return Integer.valueOf(s);
        	}
        	else return 0;
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		return 0;
	}
	
	public static String attemptSearch(String op, int range, String[] terms) {   
		// Here we may want to check the network status.
		checkNetwork();
        
        try {
            JSONObject param = new JSONObject();
            param.put("acc", UserProfile.getUsername());
            param.put("opcode", "search");
            param.put("mobile", true);
            JSONObject jsonstr = new JSONObject();            
        	if (op == METHOD_KEY_SEARCH) {
        		// Add your data
        		// Add keys if there is any.
        		if (terms != null) {
        			JSONArray keys = new JSONArray();
        			int numTerm = terms.length;
            		for (int i=0;i<numTerm;i++) {
            			keys.put(terms[i]);
            		}
            		param.put("keys", keys);
        		}
        		// Calculate the Search Range by last N days
        		Calendar c = Calendar.getInstance();
        		if (range == -SEVEN_DAYS || range == -FOURTEEN_DAYS) {
        			// 7 days or 14 days
        			c.add(Calendar.DAY_OF_MONTH, range);
        		}
        		else if (range == -ONE_MONTH || range == -THREE_MONTHS) {
        			// 1 month or 6 month
        			c.add(Calendar.MONTH, range);
        		}
        		
        		String timeStart = String.valueOf(c.get(Calendar.YEAR));
        		timeStart += ("-"+String.valueOf(c.get(Calendar.MONTH)+1));
        		timeStart += ("-"+String.valueOf(c.get(Calendar.DAY_OF_MONTH)));
        		Calendar current = Calendar.getInstance();
        		current.add(Calendar.DAY_OF_MONTH, 1);
        		String timeEnd = String.valueOf(current.get(Calendar.YEAR));
        		timeEnd += ("-"+String.valueOf(current.get(Calendar.MONTH)+1));
        		timeEnd += ("-"+String.valueOf(current.get(Calendar.DAY_OF_MONTH)));
        		
        		JSONObject timeRange = new JSONObject();
        		timeRange.put("start", timeStart);
        		timeRange.put("end", timeEnd);
        		param.put("timeRange", timeRange);
            	
            	jsonstr.put("json", param);
        	}
        	else if (op == METHOD_TAG_SEARCH) {

        	}
        	else if (op == METHOD_KEY_DATE_SEARCH) {
        		if (terms.length > 2) {
        			// Add keys if there is any.
            		JSONArray keys = new JSONArray();
            		int numTerm = terms.length - 2;
                	for (int i=0;i<numTerm;i++) {
                		keys.put(terms[i]);
                	}
                	param.put("keys", keys);
        		}
        		else if (terms.length < 2) {
        			System.out.println("Wrong terms: no start or end date.");
        			return null;
        		}
        		JSONObject timeRange = new JSONObject();
        		timeRange.put("start", terms[terms.length - 2]);
        		timeRange.put("end", terms[terms.length - 1]);
        		param.put("timeRange", timeRange);
            	jsonstr.put("json", param);
        	}
        	URL url = new URL(RECEIPT_OP_URL);
        	URLConnection connection = url.openConnection();
        	connection.setDoOutput(true);

        	OutputStreamWriter out = new OutputStreamWriter(connection.getOutputStream(), "UTF-8");
        	// Must put "json=" here for server to decoding the data
        	String data = "json=" + jsonstr.toString();
        	out.write(data);
        	out.flush();
        	out.close();

        	BufferedReader in = new BufferedReader(new InputStreamReader(connection.getInputStream(), "UTF-8"));
        	return in.readLine();
//        	String s = in.readLine();
//        	System.out.println(s);
//        	return s;
        } catch (UnsupportedEncodingException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (ClientProtocolException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		return null;
    }
}
