/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package models;

import java.sql.Date;

/**
 *
 * @author Abraham Krisnanda
 */
public class Task extends DBSimpleRecord {
    /*private int id_task;
    private String nama_task;
    private boolean status;
    private Timestamp deadline;
    private int id_kategori;
    private int id_user;*/
    public Task model;
    public Task getModel() 
    {
        if(model==null)
        {
            model = new Task();
        }
        return model;
    }
    
    @Override
    protected  String GetClassName() 
    {
        return "models.Task";
    }
    
    @Override
    protected String GetTableName() 
    {
    	return "task";
    }
    
    public void save() {
        
    }
    
    public void checkValidity() {
        
    }
    /**
     * @return the id_task
     */
    public int getId_task() {
        return (Integer)data.get("id_task");
    }

    /**
     * @param id_task the id_task to set
     */
    public void setId_task(int id_task) {
        data.put ("id_task",id_task);
    }

    /**
     * @return the nama_task
     */
    public String getNama_task() {
        return (String)data.get("nama_task");
    }

    /**
     * @param nama_task the nama_task to set
     */
    public void setNama_task(String nama_task) {
        data.put ("nama_task",nama_task);
    }

    /**
     * @return the status
     */
    public boolean isStatus() {
        return (Boolean)data.get("status");
    }

    /**
     * @param status the status to set
     */
    public void setStatus(boolean status) {
        data.put("status",status);
    }

    /**
     * @return the deadline
     */
    public Date getDeadline() {
        return (Date)data.get("deadline");
    }

    /**
     * @param deadline the deadline to set
     */
    public void setDeadline(Date deadline) {
        data.put("deadline",deadline);
    }

    /**
     * @return the id_kategori
     */
    public int getId_kategori() {
        return (Integer)data.get("id_kategori");
    }

    /**
     * @param id_kategori the id_kategori to set
     */
    public void setId_kategori(int id_kategori) {
        data.put("id_kategori",id_kategori);
    }

    /**
     * @return the id_user
     */
    public int getId_user() {
        return (Integer)data.get("id_user");
    }

    /**
     * @param id_user the id_user to set
     */
    public void setId_user(int id_user) {
        data.put("id_user",id_user);
    }
}
