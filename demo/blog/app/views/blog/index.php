- foreach( $posts -> all() as $post )
	%div.post 
		%h2 $post -> naslov
		%div $post -> sadrzaj
