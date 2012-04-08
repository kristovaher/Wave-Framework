<html>
	<head>
		<title>JavaScript Tester</title>
		<script type="text/javascript" src="class.www-wrapper.js"></script>
	</head>
	<body>
		<span onclick="makersBreakers();">DO IT NOW</span>
		<form name="myform" id="myform" method="GET" action="">
			<input type="file" name="my-file"/>
		</form>
		<script type="text/javascript">
			var WWW=new WWW_Wrapper('http://www.waher.net/wdev/www.api');
			function myTest(data){
				alert(data['name']);
			}
			function makersBreakers(){
				WWW.setCommand('example-get');
				// WWW.setForm('myform');
				WWW.setInput('kristo','vaher');
				WWW.setInput('eeva','pukkila');
				WWW.sendRequest('myTest');
			}
		</script>
	</body>
</html>