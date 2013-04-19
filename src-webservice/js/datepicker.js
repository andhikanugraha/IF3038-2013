var monthNames = [ "Januari", "Februari", "Maret", "April", "Mei", "Juni",
				"Juli", "Agustus", "September", "Oktober", "Nopember", "Desember" ];
var datePicker = 
{
	/*******************
	  REFERENCE PROPERTIES
	********************/
	calendarDiv : null,
	destinationForm : null,
	today : null,
	thisYear : null,
	thisMonth : null,
	date_id: null,
	choosing: false,
	
	/*******************
	  INITIALIZATIONS
	********************/
	// init dispatcher
	init: function(calendarDiv, destinationForm, date_id) 
	{
		this.calendarDiv = calendarDiv;
		this.destinationForm = destinationForm;
		this.initDate();
		this.populateTable(thisMonth, thisYear);
		this.date_id = date_id;
		document.getElementById(date_id).onclick = function(event)
		{
			datePicker.showCalendar(event);
		}
	},
	// create dynamic list of year choices
	initDate : function() 
	{
		today = new Date();
		thisYear = today.getFullYear();
		thisMonth = today.getMonth();
	},
	
	/*******************
	  UTILITY METHODS
	********************/
	// day of week of month's first day
	getFirstDay : function (theYear, theMonth)
	{
		var firstDate = new Date(theYear,theMonth,1);
		return firstDate.getDay();
	},
	// number of days in the month
	getMonthLen : function(theYear, theMonth) 
	{
		var nextMonth = new Date(theYear, theMonth + 1, 1);
		nextMonth.setHours(nextMonth.getHours() - 3);
		return nextMonth.getDate();
	},
	getElementPosition : function(elemID) 
	{
		var offsetTrail = document.getElementById(elemID);
		var offsetLeft = 0;
		var offsetTop = 0;
		while (offsetTrail) 
		{
			offsetLeft += offsetTrail.offsetLeft;
			offsetTop += offsetTrail.offsetTop;
			offsetTrail = offsetTrail.offsetParent;
		}
		return {left:offsetLeft, top:offsetTop};
	},
	// position and show [url=""]calendar
	showCalendar : function(evt) 
	{
		evt = (evt) ? evt : event;
		if (evt) 
		{
			if (this.calendarDiv.style.display != "block") 
			{
				var elem = (evt.target) ? evt.target : evt.srcElement;
				var position = this.getElementPosition(elem.id);
				this.calendarDiv.style.top = position.top + elem.offsetHeight + "px";
				this.calendarDiv.style.left = position.left - (elem.offsetWidth/2) + 50 + "px";
				this.calendarDiv.style.display = "block";
			} else 
			{
				this.calendarDiv.style.display = "none";
			}
		}
	},

	/************************
	  DRAW CALENDAR CONTENTS
	*************************/
	// clear and re-populate table based on form's selections
	populateTable : function(theMonth, theYear) 
	{
		// initialize date-dependent variables
		var firstDay = this.getFirstDay(theYear, theMonth);
		var howMany = this.getMonthLen(theYear, theMonth);

		// fill in month/year in table header
		document.getElementById("tableHeader").innerHTML = monthNames[theMonth]+ " " + theYear;

		// initialize vars for table creation
		var dayCounter = 1;
		var TBody = document.getElementById("tableBody");
		// clear any existing rows
		while (TBody.rows.length > 1) 
		{
			TBody.deleteRow(1);
		}
		var newR, newC, dateNum;
		var done=false;
		while (!done) 
		{
			// create new row at end
			newR = TBody.insertRow(TBody.rows.length);
			if (newR) 
			{
				for (var i = 0; i < 7; i++) 
				{
					// create new cell at end of row
					newC = newR.insertCell(newR.cells.length);
					if (TBody.rows.length == 2 && i < firstDay) 
					{
						// empty boxes before first day
						newC.innerHTML = " ";
						continue;
					}
					if (dayCounter == howMany) 
					{
						// no more rows after this one
						done = true;
					}
					// plug in link/date (or empty for boxes after last day)
					if (dayCounter <= howMany) 
					{
						if (today.getFullYear() == theYear &&
							today.getMonth() == theMonth &&
							today.getDate() == dayCounter) 
						{
							newC.id = "today";
						}
						newC.innerHTML = "<a href='#' onclick='datePicker.chooseDate(" +
							dayCounter + "," + theMonth + "," + theYear +
							"); return false;'>" + dayCounter + "</a>";
						dayCounter++;
					} else 
					{
						newC.innerHTML = " ";
					}
				}
			} else 
			{
				done = true;
			}
		}
	},

	/*******************
	   PROCESS CHOICE
	********************/
	chooseDate : function(date, month, year) 
	{
		choosing = true;
		var tempdate = (date<10) ? "0":"";
		var temp = ((month+1)<10) ? "0":"";
		var result = year + "-" + temp + (month+1) + "-" + tempdate + date;
		document.getElementById(this.date_id).value = result;
		this.blur();
		
		/*if ((birth_date.checkValidity()) && (check_date(birth_date.value)))
			birth_date.className = "";
		else
			birth_date.className = "invalid";
		check_submit();*/
		choosing = false;
	},
	
	blur: function()
	{
		setTimeout(function()
		{
			while(this.choosing);
			datePicker.calendarDiv.style.display = "none";
		},100);
	},
	
	prevMonth: function()
	{
		if (thisYear>=1955)
		{
			if ((thisMonth==0)&&(thisYear!=1955))
			{
				
				thisMonth = 11;
				thisYear--;
			}
			else
				thisMonth--;
		}
		this.populateTable(thisMonth, thisYear);
	},
	
	nextMonth: function()
	{
		thisMonth++;
		if (thisMonth==12)
		{
			thisMonth = 0;
			thisYear++;
		}
		this.populateTable(thisMonth, thisYear);
	}
}
