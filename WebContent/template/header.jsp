<!DOCTYPE html>

<%@page import="models.User"%>
<%@page import="java.util.HashMap"%>
<%@page import="controllers.MainApp"%>
<html>
	<head>
		<meta charset="utf-8">
		<title><?php echo $this->title ?></title>
		<base href="<%= session.getAttribute("base_url") %>">
		<link rel="stylesheet" href="css/style.css">
		<link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico">
	</head>

	<body>
		<div class="site-container">
			<header class="site-header">
				<h1><a href="dashboard"><%= MainApp.appName %></a></h1>
				<p><%= MainApp.appTagline %></p>

				<% 
					if (MainApp.LoggedIn(session))
					{
				%>
					<nav>
						<ul class="main-links">
							<% 
								HashMap<String, String> pages = new HashMap<String, String>();
								pages.put("dashboard", "Dashboard");
								pages.put("profile", ((User)session.getAttribute("currentUser")).getFullname());
								pages.put("logout", "Logout");

								for (String key: pages.keySet())
								{
									String label = pages.get(key);
									boolean active = (session.getAttribute("currentPage") == key);
							%>	
									<li class="<%//= page %> -link<%// if (active) out.print("active"); %>" id="<%//= page %> Li">
									<%
										if ("profile".equals(key)) 
										{
									%>
											<img src="upload/user_profile_pict/<%= ((User)session.getAttribute("currentUser")).getAvatar() %>" alt="">
									<%
										}
									%>
									<a href="<%= key %>>" id="<%= key %>>Link"><%= label %></a></li>
							<%
								}
							%>
						</ul>
	
						<%
							String q = "";
							String type = "";
							if ("search".equals(session.getAttribute("currentPage")))
							{
								q = request.getParameter("q");
								type = request.getParameter("type");
							}
						%>
						<div class="search-box">
							<form action="search" method="get" id="searchForm">
								<select name="type" id="searchType">
								<%
									HashMap<String, String> types = new HashMap<String, String>();
									types.put("all", "All");
									types.put("task", "Tasks");
									types.put("user", "Users");
									types.put("category", "Categories");
									
									for (String k: types.keySet())
									{
										String v = types.get(k);
										String selected = (k.equals(type)) ? " selected" : "";
										out.println("<option value=\""+k+selected+"\">"+v+"</option>");
									}
								%>
								</select>
								<input type="search" name="q" placeholder="Search" value="<%= q %>" id="searchQuery">
								<button type="submit">Search</button>
							</form>
						</div>
					</nav>
				<% 
					}
				%>
			</header>
			<div id="content">
