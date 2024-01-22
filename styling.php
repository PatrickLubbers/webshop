<html>
<head>
    <title>Styling and html navbar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
		
		.submit {
			display: inline-block;
			padding: 5px 8px;
			font-size: 15px;
			text-align: center;
			background-color: #4caf50; 
			color: #ffffff;
			border-radius: 3px;
			transition: background-color 0.3s ease;
		}
		
		.button:hover {
			background-color: #2980b9; 
		}
		
		table {
			width: 100%;
			border-collapse: collapse;
			margin-bottom: 20px;
		}

		th, td {
			padding: 12px;
			text-align: left;
			border-bottom: 3px solid #dddddd;
		}	
		

        nav {
			border-radius: 38px;
			background: linear-gradient(145deg, #95e9c2, #7dc4a3);
            overflow: hidden;
        }

        nav a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
			box-shadow:  19px 19px 61px #598c74,
             -19px -19px 61px #bdfff6;
        }

        h4 {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<nav>
    <a href="#">Home</a>
	<a href="page_login.php">Login</a>
    <a href="page_shopping.php">Browse shop</a>
    <a href="#">Contact</a>
    <!-- Add more links as needed -->
</nav>

</body>
</html>