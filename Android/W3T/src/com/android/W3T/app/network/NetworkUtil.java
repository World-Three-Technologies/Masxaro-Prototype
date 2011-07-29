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
import java.util.List;

import org.apache.http.HttpResponse;
import org.apache.http.HttpStatus;
import org.apache.http.NameValuePair;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicNameValuePair;
import org.apache.http.util.EntityUtils;
import org.json.*;

import com.android.W3T.app.MainPage;
import com.android.W3T.app.ReceiptsView;
import com.android.W3T.app.rmanager.Receipt;
import com.android.W3T.app.rmanager.ReceiptsManager;
import com.android.W3T.app.user.UserProfile;

public class NetworkUtil {
	public static final int LOGIN = MainPage.DIALOG_LOGIN;
	public static final int LOGOUT = MainPage.DIALOG_LOGOUT;
	
	private static final boolean FROM_DB = ReceiptsManager.FROM_DB;
	private static final boolean FROM_NFC = ReceiptsManager.FROM_NFC;
	
	public static final String BASE_URL = "http://sweethomeforus.com/php";
	public static final String LOGIN_URL = BASE_URL + "/login.php";
	public static final String LOGOUT_URL = BASE_URL + "/logoff.php";
	public static final String RECEIPT_OP_URL = BASE_URL + "/receiptOperation.php";
	
	public static final String METHOD_RECEIVE_ALL_BASIC = "user_get_all_receipt"; 
	public static final String METHOD_KEY_SEARCH = "key_search";
	public static final String METHOD_TAG_SEARCH = "tag_search";
	public static final String METHOD_DATE_SEARCH = "time_search";
	
	private static HttpClient mClient = new DefaultHttpClient();
	
	private static boolean checkNetwork() {
		return true;
	}
	
	public static boolean syncUnsentReceipts() {
		ArrayList<Receipt> receipts = ReceiptsManager.getUnSentReceipts();
        int num = receipts.size();
        for (int i=0;i<num;i++) {
        	NetworkUtil.attemptSendReceipt(UserProfile.getUsername(), receipts.get(i));
        	// if the transit succeeded, set the flag.
        	receipts.get(i).setWhere(FROM_DB);
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
            HttpResponse response = mClient.execute(request);
            
            if (response.getStatusLine().getStatusCode() == HttpStatus.SC_OK) {
            	if (op == LOGOUT) {
            		return true;
            	}
            	String s = EntityUtils.toString(response.getEntity());
            	System.out.println(s);
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
	public static String attemptGetReceiptBasic(String method, String uname) {
		// Here we may want to check the network status.
		checkNetwork();
		if (method.equals(METHOD_RECEIVE_ALL_BASIC)) {
			
			HttpPost request;
	        
	        try {
	        	request = new HttpPost(new URI(RECEIPT_OP_URL));
	            // Add your data
	            List<NameValuePair> nameValuePairs = new ArrayList<NameValuePair>(3);
	            nameValuePairs.add(new BasicNameValuePair("opcode", "user_get_all_receipt_basic"));
	            nameValuePairs.add(new BasicNameValuePair("acc", uname));
	            request.setEntity(new UrlEncodedFormEntity(nameValuePairs));
	            // Execute HTTP Post Request
	            HttpResponse response = mClient.execute(request);
	            
	            if (response.getStatusLine().getStatusCode() == HttpStatus.SC_OK) {
	            	String s = EntityUtils.toString(response.getEntity());
	            	return s;
	            }
	            return null;
	        }
	        catch (Exception e) {
	        	
	        }	
		}		
		return null;
	}
	
	public static boolean attemptSendReceipt(String uname, Receipt r) {
		// Here we may want to check the network status.
		checkNetwork();
        try {
            // Add your data
        	JSONObject basicInfo = new JSONObject();
        	basicInfo.put("store_account", r.getEntry(0));	// store name
        	basicInfo.put("tax", r.getEntry(3));			// tax
        	basicInfo.put("receipt_time", "2011-07-12 06:12:32");			// receipt time
        	basicInfo.put("receipt_id", "105");
        	basicInfo.put("user_account", UserProfile.getUsername());
        	JSONObject receipt = new JSONObject();
        	receipt.put("receipt", basicInfo);
        	receipt.put("opcode", "new_receipt");
        	JSONObject jsonstr = new JSONObject();
        	jsonstr.put("json", receipt);
        	
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
        	System.out.println(in.readLine());
        	return true;
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		return false;
	}
	
	public static String attemptSearch(String op) {   
		// Here we may want to check the network status.
		checkNetwork();

        HttpPost request;
        
        try {
        	
            request = new HttpPost();
            request.setURI(new URI(RECEIPT_OP_URL));
        	if (op == METHOD_KEY_SEARCH) {
        		// Add your data
	            List<NameValuePair> nameValuePairs = new ArrayList<NameValuePair>();
	            nameValuePairs.add(new BasicNameValuePair("opcode", op));
	            nameValuePairs.add(new BasicNameValuePair("key", "Coffee"));
	            nameValuePairs.add(new BasicNameValuePair("key", "Salad"));
	            request.setEntity(new UrlEncodedFormEntity(nameValuePairs));
        	}
        	else if (op == METHOD_TAG_SEARCH) {
        		List<NameValuePair> nameValuePairs = new ArrayList<NameValuePair>();
        		nameValuePairs.add(new BasicNameValuePair("opcode", op));
	            nameValuePairs.add(new BasicNameValuePair("tag", "McD"));
	            request.setEntity(new UrlEncodedFormEntity(nameValuePairs));
        	}
            // Execute HTTP Post Request
            HttpResponse response = mClient.execute(request);
            
            if (response.getStatusLine().getStatusCode() == HttpStatus.SC_OK) {
            	String s = EntityUtils.toString(response.getEntity());
            	System.out.println(s);
            	return s;
            }
            return null;
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
		return null;
    }

}
