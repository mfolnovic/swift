%h1 $post -> title
%span 
	- link( 'Edit', 'blog/edit/' . $post -> ID )
	- link( 'Delete', 'blog/delete/' . $post -> ID )
%span $post -> time
%div $post -> content
