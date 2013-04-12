package controllers;

import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.PrintWriter;
import java.lang.reflect.InvocationTargetException;
import java.lang.reflect.Method;
import java.util.UUID;

import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.HttpSession;
import javax.servlet.http.Part;

import models.DBSimpleRecord;
import models.User;

/**
 * Servlet implementation class MainApp
 */
@WebServlet("/MainApp")
public class MainApp extends HttpServlet 
{
	private static final long serialVersionUID = 1L;
	
	public static final String appName = "MOA";
    public static final String appTagline = "Multiuser Online Agenda";
    
    /**
     * @see HttpServlet#HttpServlet()
     */
    public MainApp() {
        super();
        // TODO Auto-generated constructor stub
    }

    public static boolean LoggedIn(HttpSession session)
	{
		return ((session.getAttribute("user_id")!=null));
	}
    
    public static int currentUserId(HttpSession session)
    {
    	return ((int)session.getAttribute("user_id"));
    }
    
    public static User currentUser(HttpSession session)
    {
    	return ((User)session.getAttribute("current_user"));
    }
    
    public static String fullPath(HttpSession session)
    {
    	return ((String)session.getAttribute("full_path"));
    }
    
    /**
	 * @see HttpServlet#doGet(HttpServletRequest request, HttpServletResponse response)
	 */
	protected void doGet(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException 
	{
		String page = ("".equals((String)request.getAttribute("page")))? "index" : (String)request.getAttribute("page");
		Class<?>[] param_handler = new Class[2];
		param_handler[0] = HttpServletRequest.class;
		param_handler[1] = HttpServletResponse.class;
		
		try {
			Method method;
			method = this.getClass().getMethod(page, param_handler);				
			method.invoke(this, request, response);			
		} catch (NoSuchMethodException e) {
			// TODO Redirect to error page
			System.out.println("-----------------");
			System.out.println(page);
			System.out.println("-----------------");
			e.printStackTrace();
		} catch (IllegalAccessException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (IllegalArgumentException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (InvocationTargetException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (SecurityException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}	
	}

	public void index(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException
	{
		request.getRequestDispatcher("pages/index.jsp").forward(request, response);
	}
	
	public void dashboard(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException
	{
		request.getRequestDispatcher("pages/dashboard.jsp").forward(request, response);
	}

	public void profile(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException
	{
		request.getRequestDispatcher("pages/profile.jsp").forward(request, response);
	}
	
	public void edit_profile(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException
	{
		request.getRequestDispatcher("pages/edit_profile.jsp").forward(request, response);
	}
	
	public void change_password(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException
	{
		request.getRequestDispatcher("pages/change_password.jsp").forward(request, response);
	}
	
	public void new_work(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException
	{
		request.getRequestDispatcher("pages/new_work.jsp").forward(request, response);
	}
	
	public void tugas(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException
	{
		request.getRequestDispatcher("pages/tugas.jsp").forward(request, response);
	}
	
	public void search(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException
	{
		request.getRequestDispatcher("pages/search.jsp").forward(request, response);
	}
	
	public void register(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException
	{
		if ("POST".equals(request.getMethod().toUpperCase()))
		{
			User user = new User();
			user.addData(request.getParameterMap());
			Part avatar = request.getPart("avatar");
			user.setAvatar(avatar.getName());
			boolean temperror = user.checkValidity();
			
			if (temperror)
			{
				// TODO print error screen
			}
			else
			{
				String temp = avatar.getName().split(".")[avatar.getName().split(".").length-1];
				String new_filename = DBSimpleRecord.MD5(UUID.randomUUID().toString()).toUpperCase()+temp;
				user.setAvatar(new_filename);
				if (user.save())
				{
					InputStream in = avatar.getInputStream();
					FileOutputStream out = new FileOutputStream(new File(MainApp.fullPath(request.getSession())));
					
					int read = 0;
					byte[] bytes = new byte[1024];
					while ((read = in.read(bytes)) != -1)
					{
						out.write(bytes, 0, read);
					}
					out.close();
					response.sendRedirect("dashboard");
				}
				else
				{
					// TODO print error screen
				}
			}
		}
		else
		{
			// TODO print error screen
		}
	}
	
	public void new_task(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException
	{
		// TODO new_task logic
	}
	
	public void edit_tugas(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException
	{
		// TODO edit_tugas logic
	}
	
	public void change_profile_data(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException
	{
		// TODO change_profile_data logic
	}
	
	public void change_user_password(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException
	{
		// TODO change_user_password logic
	}
	
	public void logout(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException
	{
		// TODO logout logic
	}
	
	public void test(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException
	{
		PrintWriter pw = response.getWriter();
		pw.println(request.getServletContext().getRealPath("/"));
		pw.close();
	}
}
