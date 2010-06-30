%h1 $post -> title
%span 
	- link( 'Edit', 'blog/edit/' . $post -> id )
	- link( 'Delete', 'blog/delete/' . $post -> id )
%span $post -> time
%div $post -> content
