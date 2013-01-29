TheWikiSources.com
==================


to do
-----
- check for a book in our database before wqe query the google book api
	- consider requesting a higher rate limit on google books api
- check why this article crushes the system
	- "Places_in_Afghanistan" - http://en.wikipedia.org/wiki/Places_in_Afghanistan


data estimates
--------------
- 8M wiki articles ( why are the number of article titles twice what wiki reports on wiki.org ??? )
- 5 references per article ( 40M references )
- 3.5 events per article ( 28M events )
- 0.2 books per article ( 1.6M books )


resources
---------

### wikipedia webpage resources
- an image page: http://en.wikipedia.org/wiki/File:SD_Montage.jpg
	- we store the `/wiki/File:SD_Montage.jpg` part in the articles table
- an image: http://upload.wikimedia.org/wikipedia/commons/thumb/f/ff/SD_Montage.jpg/250px-SD_Montage.jpg
	- we store `//upload.wikimedia.org/wikipedia/commons/thumb/f/ff/SD_Montage.jpg/250px-SD_Montage.jpg` in the articles database
	- this is the thumbnail size in the artcile, so it'll be cached when we request it
- generate a thumbnail of any size: //upload.wikimedia.org/wikipedia/commons/thumb/8/8f/Poly-oxydiphenylene-pyromellitimide.png/220px-Poly-oxydiphenylene-pyromellitimide.png
	- change the 220px to anything
	- we store the most common thumbnail size for the main image from every article
- isbn search page links to libraries in every country and region for a specific isbn: http://en.wikipedia.org/wiki/Special:BookSources/9780306458071
	- feed in any isbn at the end of this url ( can contain dashes or not )
	
### wikipedia api
- http://www.mediawiki.org/wiki/API:Main_page
- request a specific article by name: /api.php?action=query&prop=revisions&rvlimit=1&rvprop=content&format=xml&titles=test
	- this is a fucking mess of proprietary code from wiki.  use the html directly from the site instead.

### wikipedia dumps
- http://en.wikipedia.org/wiki/Wikipedia:Database_download
- latest dump of all english articles: http://download.wikimedia.org/enwiki/latest/enwiki-latest-pages-articles.xml.bz2
- latest dump of all english article titles: http://download.wikimedia.org/enwiki/latest/enwiki-latest-all-titles-in-ns0.gz
- how to extract
	- .bz2 files: `bunzip1 file_name.bz2`
	- .gz files: `gunzip file_name.gz`

### amazon affiliate link embed code:
- generate links: https://affiliate-program.amazon.com/gp/associates/promo/buildlinks.html
	- may have to bounce the isbn off google books api, then use the asin number to get the amazon affiliate link
- `<iframe src="http://rcm.amazon.com/e/cm?lt1=_top&bc1=FFFFFF&IS2=1&bg1=FFFFFF&fc1=000000&lc1=0000FF&t=induinteinc-20&o=1&p=8&l=as1&m=amazon&f=ifr&ref=tf_til&asins=0914076728" style="width:120px;height:240px;" scrolling="no" marginwidth="0" marginheight="0" frameborder="0"></iframe>`

### amazon products api: https://affiliate-program.amazon.com/gp/advertising/api/detail/main.html
- apply for an account: https://affiliate-program.amazon.com/gp/flex/associates/apply-login.html
- apply for api access keys: https://affiliate-program.amazon.com/gp/flex/advertising/api/sign-in.html
- node.js api client: https://github.com/dmcquay/node-apac
- product api docs: http://docs.aws.amazon.com/AWSECommerceService/latest/DG/ProgrammingGuide.html

### google books api
- the google api project for this software: https://code.google.com/apis/console/#project:128598473024
- open api
	- querying by isbn: https://www.googleapis.com/books/v1/volumes?q=isbn:0735619670
	- google book id ( we store this ... eg. WrgNAQAAIAAJ )
		- get book cover: http://bks3.books.google.com/books?id=WrgNAQAAIAAJ&printsec=frontcover&img=1&zoom=1
		- web reader link: http://books.google.com/books/reader?id=WrgNAQAAIAAJ&hl
- closed api
	- getting started: https://developers.google.com/books/docs/getting-started
	- volumes: https://developers.google.com/books/docs/v1/reference/volumes


mysql data schema
-----------------
- see mysql_schema.sql