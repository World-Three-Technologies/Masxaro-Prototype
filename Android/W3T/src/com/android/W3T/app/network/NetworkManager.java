package com.android.W3T.app.network;

import java.io.IOException;
import java.net.URI;
import java.net.URISyntaxException;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.util.EntityUtils;
import org.json.*;

import android.app.AlertDialog;

public class NetworkManager {
	private static String url = "http://10.0.2.2:8888/";
	/*
	private void getPDAServerData(String url) {   
        HttpClient client = new DefaultHttpClient();   

        HttpPost request;   
        try {   
            request = new HttpPost(new URI(url));   
            HttpResponse response = client.execute(request); 
            if (response.getStatusLine().getStatusCode() == 200) {
                HttpEntity entity = response.getEntity();   
                if (entity != null) {   
                    String out = EntityUtils.toString(entity);   
                    JSONObject jsonObject;   
                    String username = "";   
                    String password = "";   
                    String stateStr="";   
                       
                    UserBean userBean=new UserBean();   
                    try {   
               
                        //{"userbean":{"username":"100196","password":"1234453","State":1}}   
                        //JSONObject jsonObject = new JSONObject(builder.toString()).getJSONObject("userbean");    
                       
                        jsonObject = new JSONObject(out).getJSONObject("userbean");   
                           
                           
                        userBean.setUsername(jsonObject.getString("username"));   
                        userBean.setPassword( jsonObject.getString("password"));   
                        userBean.setState(Integer.parseInt(jsonObject.getString("state")));   
                           
                           
                           
                    } catch (JSONException e) {   
                        // TODO Auto-generated catch block   
                        e.printStackTrace();   
                    }   
                    
                }   
            }   
        } catch (URISyntaxException e) {   
            e.printStackTrace();   
         
        } catch (ClientProtocolException e) {   
            e.printStackTrace();   

        } catch (IOException e) {   
            e.printStackTrace();   
        }   
    }  */
	
}
