	%label (for: title) Title: 
	%input (type: text, name: post[title], id: title, value: $post -> title, placeholder: Title, required: required )
	%br
	%label (for: content) Content:
	%textarea (name: post[content], id: content, placeholder: Content, required: required ) $post -> content
	%br
	%button Send

