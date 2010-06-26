%h1 Edit post:&nbsp;
	- echo $post -> naslov
	
%form (action: /PRF/demo/blog/blog/update/$post -> ID, method: post)
	- partial ( 'form' )
