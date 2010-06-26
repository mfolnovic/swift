	%label (for: title) Title: 
	%input (type: text, name: title, id: title, value: $post -> title, placeholder: Title, required: required )
	%br
	%label (for: content) Content:
	%textarea (name: content, id: content, placeholder: Content, required: required ) $post -> content
	%br
	%button Send

