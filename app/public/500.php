<h1>There was an error!</h1>
<h2>Message:</h2>
<p><?php echo $message; ?></p>
<p>In file <?php echo $file; ?> (line <?php echo $line; ?>)
<h2>Backtrace:</h2>
<table>
<th>#<th>File<th>Line<th>Called
<?php foreach( $backtrace as $id => $row ) { ?>
<tr><td><?php echo $id; ?><td><?php echo isset( $row[ "file" ] ) ? $row[ "file" ] : ''; ?><td><?php echo isset( $row[ "line" ] ) ? $row[ 'line' ] : ''; ?><td><?php echo isset( $row[ "function" ] ) ? $row[ "function" ] : ""; ?>
<?php } ?>
</table>
