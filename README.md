TheWikiSources.com
==================


resources
---------

- wikipedia api
	- http://www.mediawiki.org/wiki/API:Main_page
	- request a specific article by name: /api.php?action=query&prop=revisions&rvlimit=1&rvprop=content&format=xml&titles=test
		- this is a fucking mess of proprietary code from wiki.  use the html directly from the site instead.

- wikipedia dumps
	- http://en.wikipedia.org/wiki/Wikipedia:Database_download
	- latest dump of all english articles: http://download.wikimedia.org/enwiki/latest/enwiki-latest-pages-articles.xml.bz2
	- latest dump of all english article titles: http://download.wikimedia.org/enwiki/latest/enwiki-latest-all-titles-in-ns0.gz
	- how to extract
		- .bz2 files: `bunzip1 file_name.bz2`
		- .gz files: `gunzip file_name.gz`

- amazon affiliate link embed code:
	- generate links: https://affiliate-program.amazon.com/gp/associates/promo/buildlinks.html
		- may have to bounce the isbn off google books api, then use the asin number to get the amazon affiliate link
	- `<iframe src="http://rcm.amazon.com/e/cm?lt1=_top&bc1=FFFFFF&IS2=1&bg1=FFFFFF&fc1=000000&lc1=0000FF&t=induinteinc-20&o=1&p=8&l=as1&m=amazon&f=ifr&ref=tf_til&asins=0914076728" style="width:120px;height:240px;" scrolling="no" marginwidth="0" marginheight="0" frameborder="0"></iframe>`

- amazon products api: https://affiliate-program.amazon.com/gp/advertising/api/detail/main.html
	- apply for an account: https://affiliate-program.amazon.com/gp/flex/associates/apply-login.html
	- apply for api access keys: https://affiliate-program.amazon.com/gp/flex/advertising/api/sign-in.html
	- node.js api client: https://github.com/dmcquay/node-apac
	- product api docs: http://docs.aws.amazon.com/AWSECommerceService/latest/DG/ProgrammingGuide.html

- google books api
	- getting started: https://developers.google.com/books/docs/getting-started
	- volumes: https://developers.google.com/books/docs/v1/reference/volumes
	- open api querying by isbn: https://www.googleapis.com/books/v1/volumes?q=isbn:0735619670



inline citation paragraph code snippet from wiki article
--------------------------------------------------------

### raw  
```html
<p>Lee's words set the standard by which Washington's overwhelming reputation was impressed upon the American memory. Washington set many precedents for the national government, and the presidency in particular, and was called the "<a href="/wiki/Father_of_the_Nation" title="Father of the Nation">Father of His Country</a>" as early as 1778.<sup id="cite_ref-144" class="reference"><a href="#cite_note-144"><span>[</span>Note 9<span>]</span></a></sup><sup id="cite_ref-145" class="reference"><a href="#cite_note-145"><span>[</span>136<span>]</span></a></sup><sup id="cite_ref-146" class="reference"><a href="#cite_note-146"><span>[</span>137<span>]</span></a></sup><sup id="cite_ref-147" class="reference"><a href="#cite_note-147"><span>[</span>138<span>]</span></a></sup> <a href="/wiki/Washington%27s_Birthday" title="Washington's Birthday">Washington's Birthday</a> (celebrated on Presidents' Day), is a federal holiday in the United States.<sup id="cite_ref-148" class="reference"><a href="#cite_note-148"><span>[</span>139<span>]</span></a></sup></p>
```


footnote code snippet from wiki article
---------------------------------------

### raw  
```html
<li id="cite_note-147"><span class="mw-cite-backlink"><b><a href="#cite_ref-147">^</a></b></span> <span class="reference-text"><span class="citation book">Wolf, Edwin, II, ed. (1983). <a rel="nofollow" class="external text" href="http://books.google.com/books?id=VmzfBuX1Z2QC&amp;lpg=PA93&amp;pg=PA93#v=onepage&amp;q&amp;f=false"><i>Germantown and the Germans</i></a>. Library Company of Philadelphia. <a href="/wiki/International_Standard_Book_Number" title="International Standard Book Number">ISBN</a>&#160;<a href="/wiki/Special:BookSources/978-0-914076-72-8" title="Special:BookSources/978-0-914076-72-8">978-0-914076-72-8</a><span class="printonly">. <a rel="nofollow" class="external free" href="http://books.google.com/books?id=VmzfBuX1Z2QC&amp;lpg=PA93&amp;pg=PA93#v=onepage&amp;q&amp;f=false">http://books.google.com/books?id=VmzfBuX1Z2QC&amp;lpg=PA93&amp;pg=PA93#v=onepage&amp;q&amp;f=false</a></span><span class="reference-accessdate">. Retrieved 2010-10-07</span>.</span></span></li>
```

### expanded  
```html
<li id="cite_note-147">
	<span class="mw-cite-backlink"><b><a href="#cite_ref-147">^</a></b></span>
	<span class="reference-text">
		<span class="citation book">
			Wolf, Edwin, II, ed. (1983).
			<a rel="nofollow" class="external text" href="http://books.google.com/books?id=VmzfBuX1Z2QC&amp;lpg=PA93&amp;pg=PA93#v=onepage&amp;q&amp;f=false"><i>Germantown and the Germans</i></a>.
			Library Company of Philadelphia.
			<a href="/wiki/International_Standard_Book_Number" title="International Standard Book Number">ISBN</a>&#160;<a href="/wiki/Special:BookSources/978-0-914076-72-8" title="Special:BookSources/978-0-914076-72-8">978-0-914076-72-8</a>
			<span class="printonly">. <a rel="nofollow" class="external free" href="http://books.google.com/books?id=VmzfBuX1Z2QC&amp;lpg=PA93&amp;pg=PA93#v=onepage&amp;q&amp;f=false">http://books.google.com/books?id=VmzfBuX1Z2QC&amp;lpg=PA93&amp;pg=PA93#v=onepage&amp;q&amp;f=false</a></span>
			<span class="reference-accessdate">. Retrieved 2010-10-07</span>.
		</span>
	</span>
</li>
```


mysql data schema
-----------------
- see mysql_schema.sql