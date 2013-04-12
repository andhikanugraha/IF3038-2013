/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package models;
import java.io.ByteArrayInputStream;
import java.io.IOException;
import java.io.UnsupportedEncodingException;
import java.security.DigestInputStream;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.ResultSetMetaData;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.Formatter;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.Map.Entry;

/**
 *
 * @author Abraham Krisnanda
 */
public abstract class DBSimpleRecord 
{
    protected Connection connection;
    protected HashMap<String, Object> data;
    
    public DBSimpleRecord() 
    {
        connection = DBConnection.getConnection();
        data = new HashMap<String, Object>();
    }
    
    protected abstract String GetClassName();
    protected abstract String GetTableName();
    
    public static String MD5(String input)
    {
    	String result = "";
    	try {
    		MessageDigest MD5 = MessageDigest.getInstance("MD5");
			DigestInputStream dis = new DigestInputStream(new ByteArrayInputStream(input.getBytes("UTF-8")), MD5);
			
			while (dis.read()!=-1);
			byte[] hash = MD5.digest();
			
			Formatter formatter = new Formatter();
			for (byte b : hash)
			{
				formatter.format("%02x", b);
			}
			result = formatter.toString();
			formatter.close();
		} catch (UnsupportedEncodingException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
			return "";
		} catch (NoSuchAlgorithmException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
			return "";
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
			return "";
		}
    	return result;
    }
    
    public void putData(String key, Object value)
    {
    	data.put(key, value);
    }
    
    public void addData(Map<String, String[]> map)
    {
    	for (Entry<String, String[]> entry : map.entrySet())
    	{
    		data.put(entry.getKey(), entry.getValue().toString());
    	}
    }
    
    public boolean isEmpty()
    {
    	return data.isEmpty();
    }
    
    public HashMap<String, Object> getData()
    {
    	return data;
    }
    
    public DBSimpleRecord find(String query, Object[] params, String[] params_type , String[] selection)
    {
    	try
    	{

                Class<?> c = Class.forName(GetClassName());
		DBSimpleRecord result = (DBSimpleRecord)c.newInstance();

		if (query != "")
	        {
	            query = " WHERE "+query;
	        }
	        StringBuilder cmd = new StringBuilder();
	        if ((selection!=null) && (selection.length!=0))
	        {
	            for (int i=1;i<=selection.length;i++)
	            {
	            	cmd.append(selection[i]);
	            	cmd.append(", ");
	            }
	            cmd.substring(0, cmd.length()-2);
	        }
	        else {
	            cmd.append("*");
	        }
	        
	        PreparedStatement statement = connection.prepareStatement("SELECT " + cmd.toString() + " FROM " + this.GetTableName() + query
                        +" LIMIT 1");
	        for (int i=0;i<params.length;++i)
	        {
	        	if ("string".equals(params_type[i]))
	        	{
	        		statement.setNString(i+1, (String)params[i]);
	        	}
	        	else if ("integer".equals(params_type[i]))
	        	{
	        		statement.setInt(i+1, (Integer)params[i]);
	        	}
	        }
            ResultSet rs = statement.executeQuery() ;

            ResultSetMetaData meta_data = rs.getMetaData();
            int column_count = meta_data.getColumnCount();
            if (rs.next())
            {
            	for (int i=1;i<=column_count;++i)
            	{
            		String label = meta_data.getColumnLabel(i);
            		result.putData(label, rs.getObject(i));
            	}
            }
            
            return result;
    	} catch (SQLException e) {
            e.printStackTrace();
    	} catch (ClassNotFoundException e1) {
			e1.printStackTrace();
		} catch (InstantiationException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (IllegalAccessException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
	    } catch (Exception e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
    	return null;
    }
    
    public DBSimpleRecord[] findAll(String query, Object[] params, String[] params_type , String[] selection)
    {
        try {
            Class<?> c = Class.forName(GetClassName());
            List<DBSimpleRecord> result = new ArrayList<DBSimpleRecord>();
            if (query != "")
	        {
	            query = " WHERE "+query;
	        }
	        StringBuilder cmd = new StringBuilder();
	        if ((selection!=null) && (selection.length!=0))
	        {
	            for (int i=1;i<=selection.length;i++)
	            {
	            	cmd.append(selection[i]);
	            	cmd.append(", ");
	            }
	            cmd.substring(0, cmd.length()-2);
	        }
	        else {
	            cmd.append("*");
	        }
                
            PreparedStatement statement = connection.prepareStatement("SELECT " + cmd.toString() + " FROM " + this.GetTableName() + query);
	        for (int i=0;i<params.length;++i)
	        {
	        	if ("string".equals(params_type[i]))
	        	{
	        		statement.setNString(i+1, (String)params[i]);
	        	}
	        	else if ("integer".equals(params_type[i]))
	        	{
	        		statement.setInt(i+1, (Integer)params[i]);
	        	}
	        }
            ResultSet rs = statement.executeQuery() ;
            
            ResultSetMetaData meta_data = rs.getMetaData();
            int column_count = meta_data.getColumnCount();
            while (rs.next())
            {
            	DBSimpleRecord row = (DBSimpleRecord)c.newInstance();
            	for (int i=1;i<=column_count;++i)
            	{
            		String label = meta_data.getColumnLabel(i);
            		row.putData(label, rs.getObject(i));
            	}
            	result.add(row);
            }
            
            return (DBSimpleRecord[])result.toArray();
        } catch (ClassNotFoundException e1) {
		e1.printStackTrace();
        } catch (InstantiationException e) {
		// TODO Auto-generated catch block
		e.printStackTrace();
        } catch (IllegalAccessException e) {
		// TODO Auto-generated catch block
		e.printStackTrace();
        } catch (Exception e) {
		// TODO Auto-generated catch block
		e.printStackTrace();
        }
        return null;
    }
}
