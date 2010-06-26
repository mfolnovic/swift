<!DOCTYPE html> 
%html
	%head
		%meta (charset: utf-8)
		- stylesheet( 'style.css' )
		%title Blog
	%body
		%header
			%h1 Blog
		%div#container
			- render()

