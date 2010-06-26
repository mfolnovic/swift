%h1 Posts
-foreach( $posts -> all() as $id => $post )
	%div.post
		%h2.title 
			- link( $post -> title, "blog/show/" . $post -> ID )
		%div.content $post -> content
		%div.bottombar
			%span.time $post -> time
%br
- link( 'New post', 'blog/new_form' )
