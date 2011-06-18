package com.android.W3T.app.network;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.UnsupportedEncodingException;
import java.net.URI;
import java.net.URISyntaxException;
import java.util.ArrayList;
import java.util.List;

import org.apache.http.HttpEntity;
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

import com.android.W3T.app.user.UserProfile;

import android.app.AlertDialog;

public class NetworkUtil {
	public final static String URL = "http://sweethomeforus.com/php/login.php";
	
	
	public static void attemptLogin(String url) {   
        HttpClient client = new DefaultHttpClient();   

        HttpPost request;
        
        try {
        	request = new HttpPost(new URI(URL));
            // Add your data
            List<NameValuePair> nameValuePairs = new ArrayList<NameValuePair>(3);
            nameValuePairs.add(new BasicNameValuePair("acc", "new"));
            nameValuePairs.add(new BasicNameValuePair("pwd", "123"));
            nameValuePairs.add(new BasicNameValuePair("type", "user"));
            request.setEntity(new UrlEncodedFormEntity(nameValuePairs));

            // Execute HTTP Post Request
            HttpResponse response = client.execute(request);
            
            if (response.getStatusLine().getStatusCode() == HttpStatus.SC_OK) {
            	System.out.println("status ok!");
            	String s = EntityUtils.toString(response.getEntity());

            	System.out.println(s+ " end!");

            }
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
    }

}
