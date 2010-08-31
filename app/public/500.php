<html>
	<head>
		<title>Swift Framework - Error!</title>
		<style>
			body { 
				padding: 0; 
				margin: 0;
			}
			h1 { 
				background-color: #777; 
				margin: 0; 
				padding: 10px;
			}
			#main {
				float: left;
				margin-top: 20px;
				position: absolute;
				right: 160px;
				left: 0px;
			}
			#sidebar {
				width: 150px;
				background-color: #777;
				position: absolute;
				top: 0px;
				bottom: 0px;
				right: 0px;
				padding-top: 70px;
				padding-left: 10px;
			}
			#sidebar a {
				display: block;
				color: #CCC;
			}
			.trace { 
				display: none; 
				background-color: #FFF;
				color: #444;
				padding: 10px;
			}
			.error {
				background-color: #E32;
				color: white;
			}
			.notice, .warning {
				background-color: #FC0;
			}
			.error, .notice, .warning { 
				padding: 10px;
				margin: 10px 15px;
			}
		</style>
		<script>
			function show_backtrace( id ) {
				el = document.getElementById( 'trace' + id );
				el.style.display = el.style.display == 'block' ? 'none' : 'block';
			}
		</script>
	</head>
	<body>
		<h1>Swift Framework</h1>
		<div id="main">
			<?php foreach( $errors as $id => $error ) { ?>
				<?php $type = $error[ 'type' ]; ?>
				<div class="<?php echo $type; ?>"><div onclick="show_backtrace( <?php echo $id; ?> );" id="error<?php echo $id; ?>"><?php echo $type == 'notice' ? '' : '<b>' . ucfirst( $error[ 'type' ] ) . ':&nbsp;</b>'; ?><?php echo $error[ 'message' ]; ?></div>
					<div class="trace" id="trace<?php echo $id; ?>">
						<?php foreach( $error[ 'backtrace' ] as $id => $row ) { ?>
						<?php $function = isset( $error[ 'backtrace' ][ $id + 1 ] ) ? $error[ 'backtrace' ][ $id + 1 ][ "function" ] : 'MAIN'; ?>
						<div><?php echo $function; ?> - <?php echo $row[ 'file' ]; ?>, <?php echo 'line ' . $row[ 'line' ]; ?></div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
		</div>
		<div id="sidebar">
			<a href="">Homepage</a>
			<a href="">Docs</a>
			<a href="">GitHub</a>
		</div>
	</body>
</html>
