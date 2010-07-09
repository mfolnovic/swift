!!! 5
%html
	%head
		%meta {:charset => 'utf-8'}
		- stylesheet( 'style.css', $current_time )
		%title Blog $current_time
	%body
		%header
			%h1 Blog
		%div#container
			- render()

