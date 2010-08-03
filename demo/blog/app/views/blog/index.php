%h1 Posts
-foreach( $posts -> all() as $id => $post )
	%div.post
		%h2.title 
			- link_tag( $post -> title, "blog/show/" . $post -> id )
		%div.content $post -> content
		%div.bottombar
			%span.time $post -> time
%br
- link_tag( 'New post', 'blog/new_form' )
