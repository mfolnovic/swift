%h1 $post -> title
%span 
	- link_tag( 'Edit', 'blog/edit/' . $post -> id )
	- link_tag( 'Delete', 'blog/delete/' . $post -> id )
%span $post -> time
%div $post -> content
