%h1 Edit post: $post -> title
	
%form { :action => "/PRF/demo/blog/blog/update/", :method => 'post' }
	- partial ( 'form' )
