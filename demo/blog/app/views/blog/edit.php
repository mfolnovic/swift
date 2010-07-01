%h1 Edit post: $post -> title
	
%form (action: /PRF/demo/blog/blog/update/$post -> id, method: post)
	- partial ( 'form' )
