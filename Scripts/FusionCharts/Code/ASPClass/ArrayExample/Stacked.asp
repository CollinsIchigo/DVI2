<%@LANGUAGE="VBSCRIPT"%>
<% option explicit %>
<%
'We've included ../Includes/FusionCharts_Gen.asp, which contains FusionCharts ASP Class
'to help us easily embed the charts.
%>
<!--#include file="../Includes/FusionCharts_Gen.asp"-->
<HTML>
<HEAD>
	<TITLE>
	FusionCharts V3 - Array Example using Stacked Column 3D Chart
	</TITLE>
	<%
	'You need to include the following JS file, if you intend to embed the chart using JavaScript.
	'Embedding using JavaScripts avoids the "Click to Activate..." issue in Internet Explorer
	'When you make your own charts, make sure that the path to this JS file is correct. Else, you would get JavaScript errors.
	%>	
	<SCRIPT LANGUAGE="Javascript" SRC="../../FusionCharts/FusionCharts.js"></SCRIPT>
	<style type="text/css">
	<!--
	body {
		font-family: Arial, Helvetica, sans-serif;
		font-size: 12px;
	}
	-->
	</style>
</HEAD>
<BODY>

<CENTER>
<h2><a href="http://www.fusioncharts.com" target="_blank">FusionCharts V3</a> Examples</h2>
<h4>Plotting Stacked Chart from data contained in Array.</h4>
<%
	
	'In this example, using FusionCharts ASP Class we plot 
	'we plot a Stacked chart from data contained in arrays
	
	' The arrays need to be of the following  structure :
	
	'	1. Array to store Category Namesnames :
	'	
	'	  A single dimensional array storing the category names
	'	
	'	2. A 2 Dimensional Array to store data values 

	'		** Each row will store data for 1 dataset
	'	 
	'	 Column 1 will store : Dataset Series Name.
	'	 Column 2 will store : Dataset attributes 
	'	  		(as list separated by delimiter.)
	'	Column 3 and rest will store : values of the dataset
		  
	'
	'Let's store the sales data for 6 products in our array. We also store the name of products. 
	
	'Store Name of Products
	dim arrCatNames(6)
	arrCatNames(0) = "Product A"
	arrCatNames(1) = "Product B"
	arrCatNames(2) = "Product C"
	arrCatNames(3) = "Product D"
	arrCatNames(4) = "Product E"
	arrCatNames(5) = "Product F"
	'Store sales data for current year
	dim arrData(2,8)
	arrData(0,0) = "Current Year"
	arrData(0,1) = "" ' Dataset Parameters
	arrData(0,2) = 567500
	arrData(0,3) = 815300
	arrData(0,4) = 556800
	arrData(0,5) = 734500
	arrData(0,6) = 676800
	arrData(0,7) = 648500
	'Store sales data for previous year
	arrData(1,0) = "Previous Year"
	arrData(1,1) = "" ' Dataset Parameters
	arrData(1,2) = 547300
	arrData(1,3) = 584500
	arrData(1,4) = 754000
	arrData(1,5) = 456300
	arrData(1,6) = 754500
	arrData(1,7) = 437600


	dim FC
	' Create First FusionCharts ASP class object
 	set FC = new FusionCharts
	' Set chart type to Stacked Column 3D
	call FC.setChartType("StackedColumn3D")
	' Set chart size
	call FC.setSize("600","300")
	
	' Set Relative Path of swf file.
 	call FC.setSWFPath("../../FusionCharts/")
	
	dim strParam
	' Define chart attributes
	strParam="caption=Sales by Product;numberPrefix=$;formatNumberScale=1;rotateValues=1"

 	' Set chart attributes
 	call FC.setChartParams(strParam)
	
	' Pass the 2 arrays storing data and category names to 
	' FusionCharts ASP Class function addChartDataFromArray
	call FC.addChartDataFromArray(arrData, arrCatNames)
	
	' Render the Chart 
	call FC.renderChart(false)
	
%>
<BR><BR>
<a href='../NoChart.html' target="_blank">Unable to see the chart above?</a>
<H5 ><a href='../default.htm'>&laquo; Back to list of examples</a></h5>

</CENTER>
</BODY>
</HTML>