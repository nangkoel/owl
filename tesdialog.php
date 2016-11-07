<html>
<head>
<title>jQuery Dialog Form Example</title>
<link href="style/dialog.css" rel="stylesheet"> <!-- Including CSS File Here-->
<!-- Including CSS & jQuery Dialog UI Here-->
<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/themes/ui-darkness/jquery-ui.css" rel="stylesheet">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
<script src="js/dialog.js" type="text/javascript"></script>
</head>
<body>
<div class="container">
<div class="main">
<div id="dialog" title="Dialog Form">
<form action="" method="post">
<label>Name:</label>
<input id="name" name="name" type="text">
<label>Email:</label>
<input id="email" name="email" type="text">
<input id="submit" type="submit" value="Submit">
</form>
</div>
<h2>jQuery Dialog Form Example</h2>
<p>Click below button to see jQuery dialog form.</p>
<input id="button" type="button" value="Open Dialog Form">
</div>
</div>
</body>
</html>